<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaiterCall extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_id',
        'status',
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }
}
