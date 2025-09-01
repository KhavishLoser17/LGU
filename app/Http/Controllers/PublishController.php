<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Meeting;
use Illuminate\Support\Facades\Cache;

class PublishController extends Controller
{
    public function status()
    {
        // Fetch approved meetings with minimal data for publishing
        $approvedMeetings = Meeting::where('status', 'approved')
            ->select([
                'id', 'title', 'description', 'meeting_date', 'start_time', 'end_time',
                'image_path', 'district_selection', 'agenda_leader', 'created_at'
            ])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($meeting) {
                // Get published status from cache/session since no DB field
                $isPublished = $this->isPublished($meeting->id);
                
                return [
                    'id' => $meeting->id,
                    'title' => $meeting->title,
                    'description' => $meeting->description ? 
                        (strlen($meeting->description) > 80 ? 
                            substr($meeting->description, 0, 80) . '...' : 
                            $meeting->description) : 
                        'No description available',
                    'district' => $meeting->district_selection ?? 'Not specified',
                    'agenda_leader' => $meeting->agenda_leader,
                    'meeting_date' => $meeting->meeting_date ? 
                        $meeting->meeting_date->format('M d, Y') : null,
                    'meeting_time' => ($meeting->start_time && $meeting->end_time) ? 
                        date('g:i A', strtotime($meeting->start_time)) . ' - ' . 
                        date('g:i A', strtotime($meeting->end_time)) : null,
                    'image_url' => $meeting->image_path ? 
                        asset('storage/' . $meeting->image_path) : null,
                    'created_date' => $meeting->created_at->format('M d, Y'),
                    'is_published' => $isPublished
                ];
            });

        return view('minutes.status', compact('approvedMeetings'));
    }

    public function publish(Request $request, $id)
    {
        try {
            // Ensure this is an AJAX request
            if (!$request->ajax() && !$request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid request type'
                ], 400);
            }

            $meeting = Meeting::select(['id', 'title', 'status'])
                ->where('id', $id)
                ->where('status', 'approved')
                ->first();
                
            if (!$meeting) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Meeting not found or not approved'
                ], 404);
            }
            
            // Store published status in cache (temporary solution)
            Cache::put("meeting_published_{$id}", true, now()->addDays(30));
            
            // Store in session as backup
            $publishedMeetings = session()->get('published_meetings', []);
            if (!in_array((int)$id, $publishedMeetings)) {
                $publishedMeetings[] = (int)$id;
                session()->put('published_meetings', $publishedMeetings);
            }

            return response()->json([
                'success' => true, 
                'message' => "'{$meeting->title}' has been published to the landing page!"
            ]);
        } catch (\Exception $e) {
            \Log::error('Publish meeting error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Failed to publish meeting: ' . $e->getMessage()
            ], 500);
        }
    }

    public function unpublish(Request $request, $id)
    {
        try {
            // Ensure this is an AJAX request
            if (!$request->ajax() && !$request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid request type'
                ], 400);
            }

            $meeting = Meeting::select(['id', 'title'])
                ->find($id);
                
            if (!$meeting) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Meeting not found'
                ], 404);
            }
            
            // Remove from cache
            Cache::forget("meeting_published_{$id}");
            
            // Remove from session
            $publishedMeetings = session()->get('published_meetings', []);
            $publishedMeetings = array_filter($publishedMeetings, function($meetingId) use ($id) {
                return $meetingId != $id;
            });
            session()->put('published_meetings', array_values($publishedMeetings));

            return response()->json([
                'success' => true, 
                'message' => "'{$meeting->title}' has been unpublished from the landing page!"
            ]);
        } catch (\Exception $e) {
            \Log::error('Unpublish meeting error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Failed to unpublish meeting: ' . $e->getMessage()
            ], 500);
        }
    }

    public function printMeeting($id)
    {
        $meeting = Meeting::select([
            'id', 'title', 'description', 'meeting_date', 'start_time', 'end_time',
            'district_selection', 'agenda_leader', 'default_agenda_items', 
            'custom_agenda_items', 'closing_remarks'
        ])->findOrFail($id);
        
        return view('minutes.print', compact('meeting'));
    }

    // Helper method to check if meeting is published
    private function isPublished($meetingId)
    {
        // Check cache first
        if (Cache::has("meeting_published_{$meetingId}")) {
            return Cache::get("meeting_published_{$meetingId}");
        }
        
        // Check session as backup
        $publishedMeetings = session()->get('published_meetings', []);
        return in_array($meetingId, $publishedMeetings);
    }

    // Landing page method to show published meetings
    public function landingPage()
    {
        // Get only published meetings
        $publishedMeetingIds = session()->get('published_meetings', []);
        
        if (empty($publishedMeetingIds)) {
            $publishedMeetings = collect([]);
        } else {
            $publishedMeetings = Meeting::where('status', 'approved')
                ->whereIn('id', $publishedMeetingIds)
                ->select([
                    'id', 'title', 'description', 'meeting_date', 'start_time', 'end_time',
                    'image_path', 'district_selection', 'agenda_leader'
                ])
                ->orderBy('meeting_date', 'asc')
                ->get()
                ->map(function ($meeting) {
                    return [
                        'id' => $meeting->id,
                        'title' => $meeting->title,
                        'description' => $meeting->description ? 
                            (strlen($meeting->description) > 120 ? 
                                substr($meeting->description, 0, 120) . '...' : 
                                $meeting->description) : 
                            'No description available',
                        'district' => $meeting->district_selection ?? 'Not specified',
                        'agenda_leader' => $meeting->agenda_leader,
                        'meeting_date' => $meeting->meeting_date ? 
                            $meeting->meeting_date->format('M d, Y') : 'Date TBD',
                        'meeting_time' => ($meeting->start_time && $meeting->end_time) ? 
                            date('g:i A', strtotime($meeting->start_time)) . ' - ' . 
                            date('g:i A', strtotime($meeting->end_time)) : 'Time TBD',
                        'image_url' => $meeting->image_path ? 
                            asset('storage/' . $meeting->image_path) : null,
                    ];
                });
        }

        return view('/welcome', compact('publishedMeetings'));
    }
}