<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\Attendance;
use App\Models\Document;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
   public function index()
    {
        // Get meeting statistics
        $meetingsCount = Meeting::count();
        $upcomingMeetingsCount = Meeting::upcoming()->count();
        
        // Get meeting status distribution
        $meetingStatusCounts = [
            'pending' => Meeting::where('status', 'pending')->count(),
            'approved' => Meeting::where('status', 'approved')->count(),
            'ongoing' => Meeting::where('status', 'ongoing')->count(),
            'completed' => Meeting::where('status', 'completed')->count(),
            'rejected' => Meeting::where('status', 'rejected')->count(),
        ];
        
        // Get today's attendance
        $today = Carbon::today()->toDateString();
        $todayAttendanceCount = Attendance::where('attendance_date', $today)->count();
        
        // Get documents count
        $documentsCount = Document::count();
        
        // Get recent meetings (last 5) with proper attributes
        $recentMeetings = Meeting::latest()
            ->take(5)
            ->get()
            ->map(function ($meeting) {
                // Add status color for dynamic styling
                $meeting->status_color = $this->getStatusColor($meeting->status);
                return $meeting;
            });
        
        // Get recent attendance (last 5) with proper attributes
        $recentAttendance = Attendance::latest()
            ->take(5)
            ->get()
            ->map(function ($attendance) {
                // Add status color for dynamic styling
                $attendance->status_color = $this->getAttendanceStatusColor($attendance->status);
                return $attendance;
            });
        
        // Get attendance trends for the last 7 days
        $attendanceTrends = $this->getAttendanceTrends();
        
        return view('dashboard', compact(
            'meetingsCount',
            'upcomingMeetingsCount',
            'meetingStatusCounts',
            'todayAttendanceCount',
            'documentsCount',
            'recentMeetings',
            'recentAttendance',
            'attendanceTrends'
        ));
    }
    private function getStatusColor($status)
    {
        $colors = [
            'pending' => 'yellow',
            'approved' => 'blue',
            'ongoing' => 'indigo',
            'completed' => 'green',
            'rejected' => 'red'
        ];
        
        return $colors[$status] ?? 'gray';
    }
     private function getAttendanceStatusColor($status)
    {
        $colors = [
            'on_time' => 'green',
            'late' => 'red',
            'early' => 'blue',
            'absent' => 'gray'
        ];
        
        return $colors[$status] ?? 'gray';
    }
    
    /**
     * Get attendance trends for the last 7 days
     */
    private function getAttendanceTrends()
    {
        $dates = [];
        $onTimeData = [];
        $lateData = [];
        $earlyData = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->toDateString();
            $dates[] = Carbon::parse($date)->format('M j');
            
            // Get attendance stats for the date
            $onTime = Attendance::where('attendance_date', $date)
                ->where('status', 'on_time')
                ->count();
            $late = Attendance::where('attendance_date', $date)
                ->where('status', 'late')
                ->count();
            $early = Attendance::where('attendance_date', $date)
                ->where('status', 'early')
                ->count();
            
            $onTimeData[] = $onTime;
            $lateData[] = $late;
            $earlyData[] = $early;
        }
        
        return [
            'dates' => $dates,
            'on_time' => $onTimeData,
            'late' => $lateData,
            'early' => $earlyData
        ];
    }
    
    /**
     * Get meeting analytics data for API
     */
    public function getMeetingAnalytics(Request $request)
    {
        $timeframe = $request->get('timeframe', 'month');
        
        if ($timeframe === 'week') {
            $data = $this->getWeeklyMeetingData();
        } else {
            $data = $this->getMonthlyMeetingData();
        }
        
        return response()->json($data);
    }
    
    /**
     * Get weekly meeting data
     */
    private function getWeeklyMeetingData()
    {
        $labels = [];
        $approvedData = [];
        $completedData = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('D');
            
            $approvedData[] = Meeting::whereDate('meeting_date', $date->toDateString())
                ->where('status', 'approved')
                ->count();
                
            $completedData[] = Meeting::whereDate('meeting_date', $date->toDateString())
                ->where('status', 'completed')
                ->count();
        }
        
        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Approved Meetings',
                    'data' => $approvedData,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                ],
                [
                    'label' => 'Completed Meetings',
                    'data' => $completedData,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.5)',
                    'borderColor' => 'rgb(16, 185, 129)',
                ]
            ]
        ];
    }
    
    /**
     * Get monthly meeting data
     */
    private function getMonthlyMeetingData()
    {
        $labels = [];
        $approvedData = [];
        $completedData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $startOfMonth = Carbon::today()->subMonths($i)->startOfMonth();
            $endOfMonth = Carbon::today()->subMonths($i)->endOfMonth();
            $labels[] = $startOfMonth->format('M Y');
            
            $approvedData[] = Meeting::whereBetween('meeting_date', [
                $startOfMonth->toDateString(),
                $endOfMonth->toDateString()
            ])->where('status', 'approved')->count();
            
            $completedData[] = Meeting::whereBetween('meeting_date', [
                $startOfMonth->toDateString(),
                $endOfMonth->toDateString()
            ])->where('status', 'completed')->count();
        }
        
        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Approved Meetings',
                    'data' => $approvedData,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
                ],
                [
                    'label' => 'Completed Meetings',
                    'data' => $completedData,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.5)',
                    'borderColor' => 'rgb(16, 185, 129)',
                ]
            ]
        ];
    }
    
    /**
     * Get attendance analytics data for API
     */
    public function getAttendanceAnalytics(Request $request)
    {
        $timeframe = $request->get('timeframe', 'month');
        
        if ($timeframe === 'week') {
            $data = $this->getWeeklyAttendanceData();
        } else {
            $data = $this->getMonthlyAttendanceData();
        }
        
        return response()->json($data);
    }
    
    /**
     * Get weekly attendance data
     */
    private function getWeeklyAttendanceData()
    {
        $labels = [];
        $onTimeData = [];
        $lateData = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('D');
            
            $stats = Attendance::getStats($date->toDateString());
            
            $onTimeData[] = $stats['on_time'] ?? 0;
            $lateData[] = $stats['late'] ?? 0;
        }
        
        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'On Time',
                    'data' => $onTimeData,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.5)',
                    'borderColor' => 'rgb(16, 185, 129)',
                ],
                [
                    'label' => 'Late',
                    'data' => $lateData,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.5)',
                    'borderColor' => 'rgb(239, 68, 68)',
                ]
            ]
        ];
    }
    
    /**
     * Get monthly attendance data
     */
    private function getMonthlyAttendanceData()
    {
        $labels = [];
        $onTimeData = [];
        $lateData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $startOfMonth = Carbon::today()->subMonths($i)->startOfMonth();
            $endOfMonth = Carbon::today()->subMonths($i)->endOfMonth();
            $labels[] = $startOfMonth->format('M Y');
            
            $onTimeCount = 0;
            $lateCount = 0;
            
            // Loop through each day of the month to get attendance stats
            $currentDay = $startOfMonth->copy();
            while ($currentDay->lte($endOfMonth)) {
                $stats = Attendance::getStats($currentDay->toDateString());
                $onTimeCount += $stats['on_time'] ?? 0;
                $lateCount += $stats['late'] ?? 0;
                $currentDay->addDay();
            }
            
            $onTimeData[] = $onTimeCount;
            $lateData[] = $lateCount;
        }
        
        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'On Time',
                    'data' => $onTimeData,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.5)',
                    'borderColor' => 'rgb(16, 185, 129)',
                ],
                [
                    'label' => 'Late',
                    'data' => $lateData,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.5)',
                    'borderColor' => 'rgb(239, 68, 68)',
                ]
            ]
        ];
    }
}