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
    public function manage(){
        return view('minutes.manage');
    }

  public function store(Request $request)
{
    // Validation
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
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

        \Log::error('Meeting creation failed: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Failed to create meeting. Please try again.',
            'error' => $e->getMessage()
        ], 500);
    }
}
    public function edit(Meeting $meeting)
    {
        return view('minutes.manage', compact('meeting'));
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

    public function destroy(Meeting $meeting)
    {
        try {
            // Delete associated files
            if ($meeting->image_path && Storage::disk('public')->exists($meeting->image_path)) {
                Storage::disk('public')->delete($meeting->image_path);
            }

            if ($meeting->documents) {
                foreach ($meeting->documents as $document) {
                    if (Storage::disk('public')->exists($document['path'])) {
                        Storage::disk('public')->delete($document['path']);
                    }
                }
            }

            $meeting->delete();

            return response()->json([
                'success' => true,
                'message' => 'Meeting deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting meeting: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request, Meeting $meeting)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,approved,rejected,completed'
            ]);

            $meeting->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => 'Meeting status updated successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadDocument(Meeting $meeting, $documentIndex)
    {
        try {
            if (!isset($meeting->documents[$documentIndex])) {
                abort(404, 'Document not found');
            }

            $document = $meeting->documents[$documentIndex];
            $filePath = storage_path('app/public/' . $document['path']);

            if (!file_exists($filePath)) {
                abort(404, 'File not found');
            }

            return response()->download($filePath, $document['name']);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error downloading document: ' . $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        try {
            $meetings = Meeting::latest()->get();
            return view('meetings.index', compact('meetings'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading meetings: ' . $e->getMessage());
        }
    }
}
