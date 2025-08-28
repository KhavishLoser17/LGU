<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    public function __construct()
    {
        // Set timezone to Philippines
        Carbon::setLocale('en');
        date_default_timezone_set('Asia/Manila');
    }

    /**
     * Display the QR scanner page
     */
    public function index()
    {
        try {
            // Use Philippine timezone
            $today = Carbon::now('Asia/Manila')->format('Y-m-d');

            // Use DB query with connection management
            $attendances = DB::table('attendances')
                ->where('attendance_date', $today)
                ->orderBy('check_in_time')
                ->get();

            $stats = $this->calculateStats($today);

            return view('attendance.index', compact('attendances', 'stats', 'today'));

        } catch (\Exception $e) {
            Log::error('Attendance index error: ' . $e->getMessage());

            // Return with empty data if database fails
            $attendances = collect([]);
            $stats = ['total' => 0, 'on_time' => 0, 'late' => 0, 'early' => 0];
            $today = Carbon::now('Asia/Manila')->format('Y-m-d');

            return view('attendance.index', compact('attendances', 'stats', 'today'))
                ->with('error', 'Database connection issue. Please try again.');
        } finally {
            // Close any idle connections
            DB::disconnect();
        }
    }

    /**
     * Generate QR code for attendance
     */
    public function generateQR(Request $request)
    {
        $date = $request->get('date', Carbon::now('Asia/Manila')->format('Y-m-d'));

        // Create URL for attendance scanning
        $attendanceUrl = route('attendance.scan', ['date' => $date]);

       
        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($attendanceUrl);

        return view('attendance.qr-generator', compact('qrCodeUrl', 'date', 'attendanceUrl'));
    }

    /**
     * Display scan form
     */
    public function showScanForm(Request $request)
    {
        $date = $request->get('date', Carbon::now('Asia/Manila')->format('Y-m-d'));
        $currentTime = Carbon::now('Asia/Manila');

        return view('attendance.scan', compact('date', 'currentTime'));
    }

    /**
     * Process attendance scan
     */
    public function processScan(Request $request)
    {
        try {
            $request->validate([
                'employee_name' => 'required|string|max:255',
                'employee_id' => 'required|string|max:50',
                'attendance_date' => 'required|date'
            ]);

            $attendanceDate = $request->attendance_date;
            $employeeId = $request->employee_id;

            // Use Philippine timezone for current time
            $currentTime = Carbon::now('Asia/Manila')->format('H:i:s');

            // Check if already marked attendance for today using direct DB query
            $existingAttendance = DB::table('attendances')
                ->where('employee_id', $employeeId)
                ->where('attendance_date', $attendanceDate)
                ->first();

            if ($existingAttendance) {
                return redirect()->back()->with('error', 'Attendance already marked for today!');
            }

            // Determine status based on Philippine time
            $status = $this->determineStatus($currentTime);

            // Create attendance record using DB query
            DB::table('attendances')->insert([
                'employee_name' => $request->employee_name,
                'employee_id' => $employeeId,
                'attendance_date' => $attendanceDate,
                'check_in_time' => $currentTime,
                'expected_time' => '08:00:00',
                'status' => $status,
                'notes' => $request->notes,
                'created_at' => Carbon::now('Asia/Manila'),
                'updated_at' => Carbon::now('Asia/Manila')
            ]);

            $message = "Attendance marked successfully at " . Carbon::now('Asia/Manila')->format('h:i:s A') . "! Status: " . ucfirst(str_replace('_', ' ', $status));

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Process scan error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error processing attendance. Please try again.');
        } finally {
            DB::disconnect();
        }
    }

    /**
     * Display attendance records
     */
    public function records(Request $request)
    {
        try {
            $date = $request->get('date', Carbon::now('Asia/Manila')->format('Y-m-d'));

            // Use direct DB query to avoid connection issues
            $attendances = DB::table('attendances')
                ->where('attendance_date', $date)
                ->orderBy('check_in_time')
                ->get()
                ->map(function ($attendance) {
                    // Convert time to Philippine timezone for display
                    $attendance->check_in_time_formatted = Carbon::createFromFormat('H:i:s', $attendance->check_in_time)
                        ->setTimezone('Asia/Manila')
                        ->format('h:i:s A');
                    return $attendance;
                });

            $stats = $this->calculateStats($date);

            return view('attendance.records', compact('attendances', 'stats', 'date'));

        } catch (\Exception $e) {
            Log::error('Records error: ' . $e->getMessage());

            $attendances = collect([]);
            $stats = ['total' => 0, 'on_time' => 0, 'late' => 0, 'early' => 0];
            $date = $request->get('date', Carbon::now('Asia/Manila')->format('Y-m-d'));

            return view('attendance.records', compact('attendances', 'stats', 'date'))
                ->with('error', 'Unable to load records. Please try again.');
        } finally {
            DB::disconnect();
        }
    }

    /**
     * API endpoint for QR scanner
     */
    public function apiScan(Request $request)
    {
        try {
            $request->validate([
                'employee_name' => 'required|string|max:255',
                'employee_id' => 'required|string|max:50',
                'attendance_date' => 'required|date'
            ]);

            $attendanceDate = $request->attendance_date;
            $employeeId = $request->employee_id;
            $currentTime = Carbon::now('Asia/Manila')->format('H:i:s');

            // Check if already marked using direct query
            $existingAttendance = DB::table('attendances')
                ->where('employee_id', $employeeId)
                ->where('attendance_date', $attendanceDate)
                ->first();

            if ($existingAttendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attendance already marked for today!'
                ], 400);
            }

            // Determine status
            $status = $this->determineStatus($currentTime);

            // Create record
            $newAttendanceId = DB::table('attendances')->insertGetId([
                'employee_name' => $request->employee_name,
                'employee_id' => $employeeId,
                'attendance_date' => $attendanceDate,
                'check_in_time' => $currentTime,
                'expected_time' => '08:00:00',
                'status' => $status,
                'notes' => $request->notes,
                'created_at' => Carbon::now('Asia/Manila'),
                'updated_at' => Carbon::now('Asia/Manila')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Attendance marked successfully!',
                'status' => $status,
                'time' => $currentTime,
                'time_formatted' => Carbon::createFromFormat('H:i:s', $currentTime)->format('h:i:s A'),
                'timezone' => 'Asia/Manila',
                'id' => $newAttendanceId
            ]);

        } catch (\Exception $e) {
            Log::error('API scan error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error processing attendance: ' . $e->getMessage()
            ], 500);
        } finally {
            DB::disconnect();
        }
    }

    /**
     * Determine attendance status based on time
     */
    private function determineStatus($checkInTime)
    {
        $checkIn = Carbon::createFromFormat('H:i:s', $checkInTime, 'Asia/Manila');
        $expected = Carbon::createFromFormat('H:i:s', '08:00:00', 'Asia/Manila');

        // Late threshold: 15 minutes after expected time
        $lateThreshold = $expected->copy()->addMinutes(15);

        // Early threshold: 30 minutes before expected time
        $earlyThreshold = $expected->copy()->subMinutes(30);

        if ($checkIn->greaterThan($lateThreshold)) {
            return 'late';
        } elseif ($checkIn->lessThan($earlyThreshold)) {
            return 'early';
        } else {
            return 'on_time';
        }
    }

    /**
     * Calculate statistics for a given date
     */
    private function calculateStats($date)
    {
        try {
            $total = DB::table('attendances')->where('attendance_date', $date)->count();
            $onTime = DB::table('attendances')->where('attendance_date', $date)->where('status', 'on_time')->count();
            $late = DB::table('attendances')->where('attendance_date', $date)->where('status', 'late')->count();
            $early = DB::table('attendances')->where('attendance_date', $date)->where('status', 'early')->count();

            return [
                'total' => $total,
                'on_time' => $onTime,
                'late' => $late,
                'early' => $early
            ];
        } catch (\Exception $e) {
            Log::error('Calculate stats error: ' . $e->getMessage());
            return ['total' => 0, 'on_time' => 0, 'late' => 0, 'early' => 0];
        }
    }

    /**
     * Health check endpoint
     */
    public function healthCheck()
    {
        try {
            DB::table('attendances')->limit(1)->get();
            return response()->json([
                'status' => 'healthy',
                'timezone' => 'Asia/Manila',
                'server_time' => Carbon::now('Asia/Manila')->format('Y-m-d H:i:s'),
                'database' => 'connected'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'timezone' => 'Asia/Manila',
                'server_time' => Carbon::now('Asia/Manila')->format('Y-m-d H:i:s')
            ], 500);
        }
    }
}
