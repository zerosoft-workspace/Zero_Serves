<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $fillable = [
        'name',
        'token',
        'qr_code_path',
        'status'
    ];

    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class);
    }
    public function active_order()
    {
        return $this->hasOne(Order::class, 'table_id', 'id')
            ->whereNotIn('status', ['paid']) // ödenmiş siparişleri hariç tut
            ->latest();
    }

}
