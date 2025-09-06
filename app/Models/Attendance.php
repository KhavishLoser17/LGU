<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class Attendance extends Model
{
    protected $fillable = [
        'employee_name',
        'employee_id',
        'department',
        'attendance_date',
        'check_in_time',
        'expected_time',
        'status',
        'selfie_path',
        'notes'
    ];

    protected $casts = [
        'attendance_date' => 'date'
    ];

    /**
     * Get the full URL for the selfie image
     */
    public function getSelfieUrlAttribute()
    {
        if (!empty($this->selfie_path) && Storage::disk('public')->exists($this->selfie_path)) {
            return Storage::disk('public')->url($this->selfie_path);
        }
        return null;
    }

    /**
     * Check if selfie exists
     */
    public function hasSelfie()
    {
        return !empty($this->selfie_path) && Storage::disk('public')->exists($this->selfie_path);
    }

    /**
     * Get formatted check-in time
     */
    public function getFormattedCheckInTimeAttribute()
    {
        try {
            return Carbon::createFromFormat('H:i:s', $this->check_in_time)->format('h:i:s A');
        } catch (\Exception $e) {
            return $this->check_in_time;
        }
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'on_time' => 'green',
            'late' => 'red',
            'early' => 'yellow'
        ];
        
        return $colors[$this->status] ?? 'gray';
    }

    /**
     * Get attendance records for a specific date
     */
    public static function getByDate($date)
    {
        return self::where('attendance_date', $date)->orderBy('check_in_time')->get();
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

        $records = $query->get();

        return [
            'total' => $records->count(),
            'on_time' => $records->where('status', 'on_time')->count(),
            'late' => $records->where('status', 'late')->count(),
            'early' => $records->where('status', 'early')->count(),
        ];
    }
}