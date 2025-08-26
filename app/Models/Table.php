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
            ->whereNotIn('status', ['paid', 'canceled']) // ödenmiş ve iptal edilmiş siparişleri hariç tut
            ->latest();
    }

    /**
     * Masanın durumunu sipariş durumuna göre güncelle
     */
    public function updateStatusBasedOnOrders()
    {
        $activeOrder = $this->active_order()->first();
        
        if ($activeOrder) {
            // Aktif sipariş varsa masa dolu
            $this->status = 'occupied';
        } else {
            // Aktif sipariş yoksa masa boş
            $this->status = 'empty';
        }
        
        $this->save();
        return $this->status;
    }

}
