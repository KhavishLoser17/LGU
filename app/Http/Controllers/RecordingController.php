<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RecordingController extends Controller
{
    public function index()
    {
        // Get today's meetings and upcoming meetings
        $todaysMeetings = Meeting::whereDate('meeting_date', Carbon::today())
                                ->whereIn('status', ['approved', 'completed'])
                                ->orderBy('start_time')
                                ->get();

        $upcomingMeetings = Meeting::whereDate('meeting_date', '>', Carbon::today())
                                  ->where('status', 'approved')
                                  ->orderBy('meeting_date')
                                  ->orderBy('start_time')
                                  ->limit(10)
                                  ->get();

        return view('recording.record', compact('todaysMeetings', 'upcomingMeetings'));
    }

    public function searchMeetings(Request $request)
    {
        $query = Meeting::whereIn('status', ['approved', 'completed']);

        // Search by title
        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        // Search by date
        if ($request->filled('date')) {
            $query->whereDate('meeting_date', $request->date);
        }

        // Search by date range
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('meeting_date', [$request->date_from, $request->date_to]);
        }

        $meetings = $query->orderBy('meeting_date', 'desc')
                         ->orderBy('start_time')
                         ->get();

        return response()->json($meetings);
    }

    public function getMeetingDetails($id)
{
    try {
        $meeting = Meeting::findOrFail($id);
        
        // Prepare agenda items with proper structure
        $agendaItems = [];
        
        // Add default agenda items
        if (!empty($meeting->default_agenda_items)) {
            // Handle both string and array formats
            $defaultItems = $meeting->default_agenda_items;
            
            // If it's a JSON string, decode it
            if (is_string($defaultItems)) {
                $defaultItems = json_decode($defaultItems, true);
            }
            
            // Ensure we have an array
            if (is_array($defaultItems)) {
                foreach ($defaultItems as $index => $item) {
                    // Handle both simple strings and array items
                    $title = is_array($item) ? ($item['title'] ?? $item['name'] ?? 'Default Item') : $item;
                    
                    $agendaItems[] = [
                        'id' => 'default_' . $index,
                        'title' => $title,
                        'type' => 'default',
                        'order' => $index + 1,
                        'status' => 'pending',
                        'start_time' => null,
                        'end_time' => null,
                        'duration' => 0,
                        'remarks' => ''
                    ];
                }
            }
        }

        // Add custom agenda items
        if (!empty($meeting->custom_agenda_items)) {
            // Handle both string and array formats
            $customItems = $meeting->custom_agenda_items;
            
            // If it's a JSON string, decode it
            if (is_string($customItems)) {
                $customItems = json_decode($customItems, true);
            }
            
            // Ensure we have an array
            if (is_array($customItems)) {
                $defaultCount = count($agendaItems);
                foreach ($customItems as $index => $item) {
                    // Handle both simple strings and array items
                    $title = is_array($item) ? ($item['title'] ?? $item['name'] ?? 'Custom Item') : $item;
                    
                    $agendaItems[] = [
                        'id' => 'custom_' . $index,
                        'title' => $title,
                        'type' => 'custom',
                        'order' => $defaultCount + $index + 1,
                        'status' => 'pending',
                        'start_time' => null,
                        'end_time' => null,
                        'duration' => 0,
                        'remarks' => ''
                    ];
                }
            }
        }

        // If no agenda items found, create some default ones
        if (empty($agendaItems)) {
            $agendaItems = [
                [
                    'id' => 'default_1',
                    'title' => 'Call to Order',
                    'type' => 'default',
                    'order' => 1,
                    'status' => 'pending',
                    'start_time' => null,
                    'end_time' => null,
                    'duration' => 0,
                    'remarks' => ''
                ],
                [
                    'id' => 'default_2',
                    'title' => 'Approval of Agenda',
                    'type' => 'default',
                    'order' => 2,
                    'status' => 'pending',
                    'start_time' => null,
                    'end_time' => null,
                    'duration' => 0,
                    'remarks' => ''
                ],
                [
                    'id' => 'default_3',
                    'title' => 'Adjournment',
                    'type' => 'default',
                    'order' => 3,
                    'status' => 'pending',
                    'start_time' => null,
                    'end_time' => null,
                    'duration' => 0,
                    'remarks' => ''
                ]
            ];
        }

        return response()->json([
            'meeting' => $meeting,
            'agenda_items' => $agendaItems
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error getting meeting details: ' . $e->getMessage());
        \Log::error('Error trace: ' . $e->getTraceAsString());
        
        return response()->json([
            'error' => true,
            'message' => 'Error loading meeting details: ' . $e->getMessage()
        ], 500);
    }
}

    public function startMeeting(Request $request, $id)
    {
        $meeting = Meeting::findOrFail($id);
        
        // Update meeting status and start time if not already started
        if ($meeting->status !== 'ongoing') {
            $meeting->update([
                'status' => 'ongoing',
                'actual_start_time' => now()
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Meeting started successfully',
            'start_time' => now()->toISOString()
        ]);
    }

    public function completeMeeting(Request $request, $id)
    {
        $meeting = Meeting::findOrFail($id);
        
        $meeting->update([
            'status' => 'completed',
            'actual_end_time' => now(),
            'meeting_minutes' => $request->input('meeting_minutes', [])
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Meeting completed successfully',
            'end_time' => now()->toISOString()
        ]);
    }

    public function saveProgress(Request $request, $id)
    {
        $meeting = Meeting::findOrFail($id);
        
        $meeting->update([
            'meeting_progress' => $request->input('progress', []),
            'last_updated' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Progress saved successfully'
        ]);
    }

    public function generateReport($id)
{
    try {
        $meeting = Meeting::findOrFail($id);
        
        // Get the meeting progress/agenda items from the request or database
        $agendaItems = $meeting->meeting_progress ?? [];
        
        // If no progress data, use the default agenda structure
        if (empty($agendaItems)) {
            $agendaItems = [];
            
            // Add default agenda items
            if (!empty($meeting->default_agenda_items)) {
                $defaultItems = $meeting->default_agenda_items;
                if (is_string($defaultItems)) {
                    $defaultItems = json_decode($defaultItems, true);
                }
                
                if (is_array($defaultItems)) {
                    foreach ($defaultItems as $index => $item) {
                        $title = is_array($item) ? ($item['title'] ?? $item['name'] ?? 'Default Item') : $item;
                        $agendaItems[] = [
                            'id' => 'default_' . $index,
                            'title' => $title,
                            'type' => 'default',
                            'status' => 'pending',
                            'duration' => 0,
                            'remarks' => ''
                        ];
                    }
                }
            }

            // Add custom agenda items
            if (!empty($meeting->custom_agenda_items)) {
                $customItems = $meeting->custom_agenda_items;
                if (is_string($customItems)) {
                    $customItems = json_decode($customItems, true);
                }
                
                if (is_array($customItems)) {
                    foreach ($customItems as $index => $item) {
                        $title = is_array($item) ? ($item['title'] ?? $item['name'] ?? 'Custom Item') : $item;
                        $agendaItems[] = [
                            'id' => 'custom_' . $index,
                            'title' => $title,
                            'type' => 'custom',
                            'status' => 'pending',
                            'duration' => 0,
                            'remarks' => ''
                        ];
                    }
                }
            }
        }

        // Calculate agenda summary
        $agendaSummary = [
            'total' => count($agendaItems),
            'completed' => count(array_filter($agendaItems, fn($item) => ($item['status'] ?? 'pending') === 'completed')),
            'ongoing' => count(array_filter($agendaItems, fn($item) => ($item['status'] ?? 'pending') === 'ongoing')),
            'pending' => count(array_filter($agendaItems, fn($item) => ($item['status'] ?? 'pending') === 'pending')),
            'skipped' => count(array_filter($agendaItems, fn($item) => ($item['status'] ?? 'pending') === 'skipped'))
        ];

        $report = [
            'meeting' => $meeting,
            'total_duration' => $this->calculateTotalDuration($meeting),
            'agenda_summary' => $agendaSummary,
            'agenda_items' => $agendaItems, // Include the full agenda items with remarks
            'generated_at' => now()->toDateTimeString()
        ];

        return response()->json($report);
        
    } catch (\Exception $e) {
        \Log::error('Error generating report: ' . $e->getMessage());
        \Log::error('Error trace: ' . $e->getTraceAsString());
        
        return response()->json([
            'error' => true,
            'message' => 'Error generating report: ' . $e->getMessage()
        ], 500);
    }
}

    private function calculateTotalDuration($meeting)
    {
        if ($meeting->actual_start_time && $meeting->actual_end_time) {
            return Carbon::parse($meeting->actual_start_time)
                        ->diffInMinutes(Carbon::parse($meeting->actual_end_time));
        }
        return 0;
    }

    private function getAgendaSummary($meeting)
    {
        $progress = $meeting->meeting_progress ?? [];
        
        return [
            'total_items' => count($progress),
            'completed' => count(array_filter($progress, fn($item) => $item['status'] === 'completed')),
            'ongoing' => count(array_filter($progress, fn($item) => $item['status'] === 'ongoing')),
            'pending' => count(array_filter($progress, fn($item) => $item['status'] === 'pending')),
            'skipped' => count(array_filter($progress, fn($item) => $item['status'] === 'skipped'))
        ];
    }
}