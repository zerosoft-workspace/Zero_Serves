<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'phone', 'date', 'time', 'people'];

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

}