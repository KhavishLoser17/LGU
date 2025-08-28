<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_name',
        'employee_id',
        'attendance_date',
        'check_in_time',
        'expected_time',
        'status',
        'notes'
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in_time' => 'datetime:H:i:s',
        'expected_time' => 'datetime:H:i:s'
    ];

    /**
     * Determine attendance status based on check-in time
     */
    public function determineStatus($checkInTime, $expectedTime = '08:00:00')
    {
        $checkIn = Carbon::createFromFormat('H:i:s', $checkInTime);
        $expected = Carbon::createFromFormat('H:i:s', $expectedTime);

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
     * Get attendance records for a specific date
     */
    public static function getByDate($date)
    {
        return self::where('attendance_date', $date)->get();
    }

    /**
     * Get attendance statistics
     */
    public static function getStats($date = null)
    {
        $query = self::query();

        if ($date) {
            $query->where('attendance_date', $date);
        }

        return [
            'total' => $query->count(),
            'on_time' => $query->where('status', 'on_time')->count(),
            'late' => $query->where('status', 'late')->count(),
            'early' => $query->where('status', 'early')->count(),
        ];
    }
}
