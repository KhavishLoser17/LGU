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
        date_default_timezone_set('Asia/Manila');
    }

    /**
     * Display the QR scanner page
     */
    public function index()
    {
        try {
            $today = Carbon::now('Asia/Manila')->format('Y-m-d');
            $attendances = Attendance::where('attendance_date', $today)
                ->orderBy('check_in_time')
                ->get();

            $stats = $this->calculateStats($today);

            return view('attendance.index', compact('attendances', 'stats', 'today'));
        } catch (\Exception $e) {
            Log::error('Attendance index error: ' . $e->getMessage());
            $attendances = collect([]);
            $stats = ['total' => 0, 'on_time' => 0, 'late' => 0, 'early' => 0];
            $today = Carbon::now('Asia/Manila')->format('Y-m-d');

            return view('attendance.index', compact('attendances', 'stats', 'today'));
        }
    }

    /**
     * Generate QR code for attendance
     */
    public function generateQR(Request $request)
    {
        $date = $request->get('date', Carbon::now('Asia/Manila')->format('Y-m-d'));
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
     * Process attendance scan - SIMPLIFIED
     */
    public function processScan(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'employee_name' => 'required|string|max:255|min:2',
                'employee_id' => 'required|string|max:255|min:1',
                'department' => 'nullable|string|max:255',
                'attendance_date' => 'required|date',
                'selfie_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
                'notes' => 'nullable|string|max:1000'
            ]);

            $employeeId = strtolower(trim($request->employee_id));
            $attendanceDate = $request->attendance_date;

            // Check if attendance already exists
            $existingAttendance = Attendance::where('employee_id', $employeeId)
                ->where('attendance_date', $attendanceDate)
                ->first();

            if ($existingAttendance) {
                return back()->with('error', 'Attendance already recorded for today!')->withInput();
            }

            // Get current time
            $currentTime = Carbon::now('Asia/Manila');
            $checkInTime = $currentTime->format('H:i:s');

            // Create attendance record
            $attendance = new Attendance();
            $attendance->employee_name = ucwords(strtolower($request->employee_name));
            $attendance->employee_id = $employeeId;
            $attendance->department = $request->department;
            $attendance->attendance_date = $attendanceDate;
            $attendance->check_in_time = $checkInTime;
            $attendance->expected_time = '08:00:00';
            $attendance->status = $this->determineStatus($checkInTime);
            $attendance->notes = $request->notes;

            // Handle file upload if present
            if ($request->hasFile('selfie_image') && $request->file('selfie_image')->isValid()) {
                try {
                    $file = $request->file('selfie_image');
                    $filename = 'selfie_' . $employeeId . '_' . $attendanceDate . '_' . time() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('selfies', $filename, 'public');
                    $attendance->selfie_path = $path;
                } catch (\Exception $e) {
                    Log::error('File upload error: ' . $e->getMessage());
                    // Continue without selfie
                }
            }

            // Save attendance
            $attendance->save();

            // Success message
            $statusMessages = [
                'on_time' => '✅ You are on time!',
                'late' => '⚠️ You are late.',
                'early' => '🕐 You are early.'
            ];

            $message = "Attendance recorded successfully! " . 
                      ($statusMessages[$attendance->status] ?? '') . 
                      " Check-in time: " . $currentTime->format('h:i:s A');

            return back()->with('success', $message);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Attendance error: ' . $e->getMessage() . ' | Data: ' . json_encode($request->all()));
            return back()->with('error', 'Failed to record attendance. Please try again.')->withInput();
        }
    }

    /**
     * Display attendance records
     */
    public function records(Request $request)
    {
        try {
            $date = $request->get('date', Carbon::now('Asia/Manila')->format('Y-m-d'));
            $attendances = Attendance::where('attendance_date', $date)
                ->orderBy('check_in_time')
                ->get();

            $stats = $this->calculateStats($date);

            return view('attendance.records', compact('attendances', 'stats', 'date'));
        } catch (\Exception $e) {
            Log::error('Records error: ' . $e->getMessage());
            $attendances = collect([]);
            $stats = ['total' => 0, 'on_time' => 0, 'late' => 0, 'early' => 0];
            $date = $request->get('date', Carbon::now('Asia/Manila')->format('Y-m-d'));

            return view('attendance.records', compact('attendances', 'stats', 'date'));
        }
    }

    /**
     * Determine attendance status
     */
    private function determineStatus($checkInTime, $expectedTime = '08:00:00')
    {
        try {
            $checkIn = Carbon::createFromFormat('H:i:s', $checkInTime);
            $expected = Carbon::createFromFormat('H:i:s', $expectedTime);

            $lateThreshold = $expected->copy()->addMinutes(15); // 8:15 AM
            $earlyThreshold = $expected->copy()->subMinutes(30); // 7:30 AM

            if ($checkIn->greaterThan($lateThreshold)) {
                return 'late';
            } elseif ($checkIn->lessThan($earlyThreshold)) {
                return 'early';
            } else {
                return 'on_time';
            }
        } catch (\Exception $e) {
            Log::error('Status determination error: ' . $e->getMessage());
            return 'on_time';
        }
    }

    /**
     * Calculate statistics for a given date
     */
    private function calculateStats($date)
    {
        try {
            $attendances = Attendance::where('attendance_date', $date)->get();
            
            return [
                'total' => $attendances->count(),
                'on_time' => $attendances->where('status', 'on_time')->count(),
                'late' => $attendances->where('status', 'late')->count(),
                'early' => $attendances->where('status', 'early')->count()
            ];
        } catch (\Exception $e) {
            Log::error('Stats calculation error: ' . $e->getMessage());
            return ['total' => 0, 'on_time' => 0, 'late' => 0, 'early' => 0];
        }
    }
}