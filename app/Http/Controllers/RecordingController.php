<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Meeting;

class RecordingController extends Controller
{
    public function record(Request $request)
    {
        // Get meeting ID from request parameter or session
        $meetingId = $request->get('meeting_id') ?? session('current_meeting_id');
        
        if (!$meetingId) {
            // If no meeting ID provided, redirect to meetings list or show error
            return redirect()->route('meetings.index')->with('error', 'No meeting selected for recording.');
        }

        // Fetch the meeting data
        $meeting = Meeting::find($meetingId);
        
        if (!$meeting) {
            return redirect()->route('meetings.index')->with('error', 'Meeting not found.');
        }

        // Prepare meeting data for the view
        $meetingData = [
            'id' => $meeting->id,
            'title' => $meeting->title,
            'description' => $meeting->description,
            'meeting_date' => $meeting->meeting_date,
            'start_time' => $meeting->start_time,
            'end_time' => $meeting->end_time,
            'image_url' => $meeting->image_url, // Uses the accessor from your model
            'district_selection' => $meeting->district_selection,
            'agenda_leader' => $meeting->agenda_leader,
            'default_agenda_items' => $meeting->default_agenda_items ?? [],
            'custom_agenda_items' => $meeting->custom_agenda_items ?? [],
            'all_agenda_items' => $meeting->getAllAgendaItems()
        ];

        return view('recording.record', compact('meetingData'));
    }

    // Alternative method to get all meetings for selection
    public function index()
    {
        $meetings = Meeting::select([
                'id', 
                'title', 
                'description', 
                'meeting_date', 
                'start_time', 
                'end_time', 
                'image_path',
                'status'
            ])
            ->orderBy('meeting_date', 'desc')
            ->get()
            ->map(function ($meeting) {
                return [
                    'id' => $meeting->id,
                    'title' => $meeting->title,
                    'description' => $meeting->description,
                    'meeting_date' => $meeting->meeting_date,
                    'start_time' => $meeting->start_time,
                    'end_time' => $meeting->end_time,
                    'image_url' => $meeting->image_url,
                    'status' => $meeting->status
                ];
            });

        return view('recording.index', compact('meetings'));
    }

    // Method to start recording for a specific meeting
    public function startRecording($meetingId)
    {
        $meeting = Meeting::findOrFail($meetingId);
        
        // Store meeting ID in session for the recording process
        session(['current_meeting_id' => $meetingId]);
        
        return redirect()->route('recording.record')->with('success', 'Recording started for: ' . $meeting->title);
    }
}