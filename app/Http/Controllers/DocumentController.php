<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function document()
    {
        $documents = Document::latest()->paginate(10);
        return view('recording.documents', compact('documents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document' => 'required|file|mimes:pdf,doc,docx,xlsx,xls,jpg,jpeg,png|max:10240'
        ]);

        try {
            $file = $request->file('document');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('documents', $fileName, 'public');

            Document::create([
                'name' => $request->name,
                'category' => $request->category,
                'description' => $request->description,
                'file_name' => $fileName,
                'file_path' => $filePath,
                'file_size' => $this->formatBytes($file->getSize()),
                'file_type' => $file->getClientOriginalExtension()
            ]);

            return redirect()->route('documents')->with('success', 'Document uploaded successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error uploading document: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $document = Document::findOrFail($id);
            
            // Delete file from storage
            Storage::delete('public/' . $document->file_path);
            
            // Delete record from database
            $document->delete();
            
            return redirect()->route('documents')->with('success', 'Document deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error deleting document: ' . $e->getMessage()]);
        }
    }

    private function formatBytes($size, $precision = 2)
    {
        if ($size > 0) {
            $size = (int) $size;
            $base = log($size) / log(1024);
            $suffixes = array(' bytes', ' KB', ' MB', ' GB', ' TB');

            return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
        } else {
            return $size;
        }
    }
}