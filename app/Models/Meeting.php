<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Meeting extends Model
{
    protected $fillable = [
        'title', 'status', 'description', 'meeting_date', 'start_time', 'end_time',
        'actual_start_time', 'actual_end_time', 'image_path', 'documents', 
        'district_selection', 'agenda_leader', 'default_agenda_items', 
        'custom_agenda_items', 'closing_remarks', 'meeting_minutes', 
        'meeting_progress', 'last_updated'
        
    ];

     protected $casts = [
        'meeting_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'actual_start_time' => 'datetime',
        'actual_end_time' => 'datetime',
        'last_updated' => 'datetime',
        'default_agenda_items' => 'array',
        'custom_agenda_items' => 'array',
        'closing_remarks' => 'array',
        'documents' => 'array',
        'meeting_minutes' => 'array',
        'meeting_progress' => 'array',
    ];
    protected $attributes = [
        'status' => 'pending'
    ];

    // Get image URL
    public function getImageUrlAttribute()
    {
        return $this->image_path ? asset('storage/'.$this->image_path) : null;
    }

    // Scope methods
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
     public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
     public function scopeToday($query)
    {
        return $query->whereDate('meeting_date', Carbon::today());
    }
     public function scopeUpcoming($query)
    {
        return $query->whereDate('meeting_date', '>=', Carbon::today())
                    ->where('status', 'approved');
    }
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('meeting_date', [$startDate, $endDate]);
    }
    public function scopeByTitle($query, $title)
    {
        return $query->where('title', 'like', '%' . $title . '%');
    }

    // Check if has documents
    public function hasDocuments()
    {
        return !empty($this->documents);
    }

    // Get all agenda items including closing remarks
    public function getAllAgendaItems()
    {
        $items = $this->agenda_items ?? [];

        if (!empty($this->closing_remarks)) {
            $items = array_merge($items, ['Closing Remarks' => $this->closing_remarks]);
        }

        return $items;
    }
    
     public function getDurationAttribute()
    {
        if ($this->actual_start_time && $this->actual_end_time) {
            return Carbon::parse($this->actual_start_time)
                        ->diff(Carbon::parse($this->actual_end_time));
        }
        
        if ($this->start_time && $this->end_time) {
            return Carbon::parse($this->start_time)
                        ->diff(Carbon::parse($this->end_time));
        }

        return null;
    }
    public function getFormattedDurationAttribute()
    {
        $duration = $this->duration;
        if ($duration) {
            return $duration->format('%H:%I:%S');
        }
        return '00:00:00';
    }
    public function getIsOngoingAttribute()
    {
        return $this->status === 'ongoing';
    }
     public function getIsCompletedAttribute()
    {
        return $this->status === 'completed';
    }

    // Get meeting status badge color
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'gray',
            'approved' => 'blue',
            'ongoing' => 'yellow',
            'completed' => 'green',
            'rejected' => 'red',
            default => 'gray'
        };
    }

    // Get progress summary
    public function getProgressSummaryAttribute()
    {
        if (!$this->meeting_progress) {
            return [
                'total' => 0,
                'completed' => 0,
                'ongoing' => 0,
                'pending' => 0,
                'skipped' => 0
            ];
        }

        $progress = $this->meeting_progress;
        return [
            'total' => count($progress),
            'completed' => count(array_filter($progress, fn($item) => $item['status'] === 'completed')),
            'ongoing' => count(array_filter($progress, fn($item) => $item['status'] === 'ongoing')),
            'pending' => count(array_filter($progress, fn($item) => $item['status'] === 'pending')),
            'skipped' => count(array_filter($progress, fn($item) => $item['status'] === 'skipped'))
        ];
    }

   
    public static function getPublished()
    {
        $publishedIds = session()->get('published_meetings', []);
        
        if (empty($publishedIds)) {
            return collect([]);
        }
        
        return self::approved()
            ->whereIn('id', $publishedIds)
            ->select(['id', 'title', 'description', 'meeting_date', 'start_time', 'end_time', 'image_path', 'district_selection', 'agenda_leader'])
            ->orderBy('meeting_date', 'asc')
            ->get();
    }

    // Search meetings
    public static function search($params = [])
    {
        $query = self::whereIn('status', ['approved', 'completed']);

        if (!empty($params['title'])) {
            $query->byTitle($params['title']);
        }

        if (!empty($params['date'])) {
            $query->whereDate('meeting_date', $params['date']);
        }

        if (!empty($params['date_from']) && !empty($params['date_to'])) {
            $query->byDateRange($params['date_from'], $params['date_to']);
        }

        if (!empty($params['status'])) {
            $query->byStatus($params['status']);
        }

        if (!empty($params['district'])) {
            $query->where('district_selection', $params['district']);
        }

        return $query->orderBy('meeting_date', 'desc')
                    ->orderBy('start_time')
                    ->get();
    }


}
