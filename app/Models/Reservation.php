<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'phone', 'date', 'time', 'people', 'status', 'admin_note', 'status_updated_at', 'status_updated_by'];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i',
        'status_updated_at' => 'datetime',
        'read_at' => 'datetime'
    ];

    // class Reservation extends Model { ... }
    public function scopeUnread($q)
    {
        return $q->whereNull('read_at');
    }
    public function markAsRead(): void
    {
        if (is_null($this->read_at)) {
            $this->read_at = now();
            $this->save();
        }
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => '<span class="badge bg-warning">Beklemede</span>',
            'approved' => '<span class="badge bg-success">Onaylandı</span>',
            'rejected' => '<span class="badge bg-danger">Reddedildi</span>',
            default => '<span class="badge bg-secondary">Bilinmiyor</span>'
        };
    }

    public function statusUpdatedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'status_updated_by');
    }
}