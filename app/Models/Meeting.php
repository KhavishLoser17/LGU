<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $fillable = [
        'title', 'status', 'description', 'image_path', 'documents',
        'district_selection', 'agenda_leader', 'default_agenda_items','custom_agenda_items', 'closing_remarks'
    ];

    protected $casts = [
        'default_agenda_items' => 'array',
        'custom_agenda_items' => 'array',
        'closing_remarks' => 'array',
        'documents' => 'array',
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
}
