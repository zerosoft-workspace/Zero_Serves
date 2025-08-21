<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['table_id', 'status', 'payment_status', 'total_price'];
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
    public function table()
    {
        return $this->belongsTo(Table::class);
    }
}
