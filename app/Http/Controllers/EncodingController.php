<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser as PdfParser;

class EncodingController extends Controller
{
    public function encoding()
    {
        return view('recording.encoding');
    }
    public function summarize(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:pdf,doc,docx,txt|max:10240',
            ]);

            $file = $request->file('file');
            $content = $this->extractContent($file);
            
            // Call Gemini AI API
            $summary = $this->callGeminiAPI($content);
            
            return response()->json([
                'success' => true,
                'title' => 'AI Summary: ' . $file->getClientOriginalName(),
                'content' => $summary,
            ]);
            
        } catch (\Exception $e) {
            Log::error('AI Summarization Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error processing document: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    private function extractContent($file)
    {
        $extension = $file->getClientOriginalExtension();
        $content = '';
        
        try {
            switch (strtolower($extension)) {
                case 'pdf':
                    $content = $this->extractFromPdf($file);
                    break;
                case 'doc':
                case 'docx':
                    $content = $this->extractFromWord($file);
                    break;
                case 'txt':
                    $content = file_get_contents($file->getRealPath());
                    break;
                default:
                    throw new \Exception('Unsupported file type');
            }
        } catch (\Exception $e) {
            throw new \Exception('Failed to extract content: ' . $e->getMessage());
        }
        
        // Limit content to avoid token limits
        return substr($content, 0, 10000);
    }
    
    private function extractFromPdf($file)
    {
        $parser = new PdfParser();
        $pdf = $parser->parseFile($file->getRealPath());
        return $pdf->getText();
    }
    
    private function extractFromWord($file)
    {
        // For DOC/DOCX files, we'll use a simple text extraction
        // In a production environment, you might want to use a library like phpword
        $content = shell_exec('cat ' . escapeshellarg($file->getRealPath()) . ' | strings');
        return $content ?: 'Could not extract text from Word document.';
    }
    
   private function callGeminiAPI($content)
{
    $apiKey = config('services.gemini.api_key');
    $model = config('services.gemini.model', 'gemini-2.0-flash');
    
    // Correct URL without API key parameter
    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";
    
    $prompt = "Please analyze this meeting document and create a comprehensive, well-structured summary. 
    Include key points, decisions made, action items, and next steps. Format the response using HTML with appropriate headings, lists, and formatting.
    
    Document content:
    " . $content;
    
    $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'X-goog-api-key' => $apiKey, // Correct header format
    ])
    ->withOptions([
        'verify' => false, // For development - fix SSL issue
    ])
    ->timeout(60)
    ->post($url, [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.7,
            'maxOutputTokens' => 2048,
        ]
    ]);
    
    if ($response->failed()) {
        Log::error('Gemini API Error: ' . $response->body());
        throw new \Exception('Gemini API request failed: ' . $response->status());
    }
    
    $data = $response->json();
    
    if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
        Log::error('Invalid Gemini Response: ' . json_encode($data));
        throw new \Exception('Invalid response from Gemini API');
    }
    
    return $data['candidates'][0]['content']['parts'][0]['text'];
}
}
