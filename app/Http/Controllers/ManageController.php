<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;



class ManageController extends Controller
{
   public function manage()
    {
        $meetings = Meeting::select([
            'id', 'title', 'description', 'district_selection', 
            'status', 'image_path', 'created_at', 'meeting_date'
        ])
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($meeting) {
            return [
                'id' => $meeting->id,
                'title' => $meeting->title,
                'description' => $meeting->description,
                'district' => $meeting->district_selection,
                'status' => $meeting->status,
                'status_color' => $this->getStatusColor($meeting->status),
                'image_url' => $meeting->getImageUrlAttribute(),
                'created_date' => $meeting->created_at->format('M d, Y'),
                'meeting_date' => $meeting->meeting_date ? $meeting->meeting_date->format('M d, Y') : null,
                'has_documents' => $meeting->hasDocuments()
            ];
        });

        return view('minutes.manage', compact('meetings'));
    }
    private function getStatusColor($status)
    {
        return match($status) {
            'approved' => ['bg' => 'bg-green-100', 'text' => 'text-green-800'],
            'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-800'],
            'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800'],
            default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800']
        };
    }
    public function approve($id)
    {
        $meeting = Meeting::findOrFail($id);
        $meeting->update(['status' => 'approved']);
        
        return response()->json(['success' => true, 'message' => 'Meeting approved successfully']);
    }

    public function reject($id)
    {
        $meeting = Meeting::findOrFail($id);
        $meeting->update(['status' => 'rejected']);
        
        return response()->json(['success' => true, 'message' => 'Meeting rejected successfully']);
    }

    public function destroy($id)
    {
        $meeting = Meeting::findOrFail($id);
        $meeting->delete();
        
        return response()->json(['success' => true, 'message' => 'Meeting deleted successfully']);
    }


  public function store(Request $request)
{
    // Validation
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'meeting_date' => 'required|date', // Add this
        'start_time' => 'required|date_format:H:i', // Add this
        'end_time' => 'nullable|date_format:H:i|after:start_time', // Add this
        'image' => 'nullable|image|max:5120',
        'documents.*' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        'district_selection' => 'nullable|string',
        'agenda_leader' => 'nullable|string',
        'default_agenda_items' => 'nullable|string',
        'custom_agenda_items' => 'nullable|string',
        'closing_remarks' => 'nullable|string'
    ]);

    try {
        DB::beginTransaction();

        // Prepare the data array
        $meetingData = [
            'title' => $validated['title'],
            'status' => 'pending',
            'meeting_date' => $validated['meeting_date'], // Add this
            'start_time' => $validated['start_time'], // Add this
            'end_time' => $validated['end_time'] ?? null, // Add this
            'description' => $validated['description'] ?? null,
            'district_selection' => $validated['district_selection'] ?? null,
            'agenda_leader' => $validated['agenda_leader'] ?? null,
        ];

        // Handle JSON fields - decode then re-encode to ensure proper format
        if (isset($validated['default_agenda_items'])) {
            $meetingData['default_agenda_items'] = $validated['default_agenda_items'];
        }
        if (isset($validated['custom_agenda_items'])) {
            $meetingData['custom_agenda_items'] = $validated['custom_agenda_items'];
        }
        if (isset($validated['closing_remarks'])) {
            $meetingData['closing_remarks'] = $validated['closing_remarks'];
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $meetingData['image_path'] = $request->file('image')->store('meetings/images', 'public');
        }

        // Handle documents
        if ($request->hasFile('documents')) {
            $documents = [];
            foreach ($request->file('documents') as $document) {
                $path = $document->store('meetings/documents', 'public');
                $documents[] = [
                    'name' => $document->getClientOriginalName(),
                    'path' => $path,
                    'size' => $document->getSize()
                ];
            }
            $meetingData['documents'] = json_encode($documents);
        }

        // Create meeting
        $meeting = Meeting::create($meetingData);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Meeting created successfully.',
            'meeting_id' => $meeting->id
        ]);

    } catch (\Exception $e) {
        DB::rollBack();

        Log::error('Meeting creation failed: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Failed to create meeting. Please try again.',
            'error' => $e->getMessage()
        ], 500);
    }
}


    public function update(Request $request, Meeting $meeting)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'status' => 'nullable|string|in:pending,approved,rejected,completed',
                'description' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
                'documents.*' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
                'district_selection' => 'nullable|string',
                'agenda_leader' => 'nullable|string',
            ]);

            $meetingData = [
                'title' => $request->title,
                'status' => $request->status ?? $meeting->status,
                'description' => $request->description,
                'district_selection' => $request->district_selection,
                'agenda_leader' => $request->agenda_leader,
            ];

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($meeting->image_path && Storage::disk('public')->exists($meeting->image_path)) {
                    Storage::disk('public')->delete($meeting->image_path);
                }

                $image = $request->file('image');
                $imageName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('meetings/images', $imageName, 'public');
                $meetingData['image_path'] = $imagePath;
            }

            // Handle document uploads
            if ($request->hasFile('documents')) {
                $documents = $meeting->documents ?? [];

                foreach ($request->file('documents') as $document) {
                    $docName = time() . '_' . Str::random(10) . '.' . $document->getClientOriginalExtension();
                    $docPath = $document->storeAs('meetings/documents', $docName, 'public');
                    $documents[] = [
                        'name' => $document->getClientOriginalName(),
                        'path' => $docPath,
                        'size' => $document->getSize(),
                        'type' => $document->getClientOriginalExtension()
                    ];
                }
                $meetingData['documents'] = $documents;
            }

            // Handle agenda items
            if ($request->has('default_agenda_items') && !empty($request->default_agenda_items)) {
                $meetingData['default_agenda_items'] = json_decode($request->default_agenda_items, true) ?? [];
            }

            if ($request->has('custom_agenda_items') && !empty($request->custom_agenda_items)) {
                $meetingData['custom_agenda_items'] = json_decode($request->custom_agenda_items, true) ?? [];
            }

            if ($request->has('closing_remarks') && !empty($request->closing_remarks)) {
                $meetingData['closing_remarks'] = json_decode($request->closing_remarks, true) ?? [];
            }

            $meeting->update($meetingData);

            return response()->json([
                'success' => true,
                'message' => 'Meeting updated successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating meeting: ' . $e->getMessage()
            ], 500);
        }
    }

}
