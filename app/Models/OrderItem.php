<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class OrderItem extends Model
{
    protected $fillable = ['order_id', 'product_id', 'quantity', 'price', 'line_total'];
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function product(): BelongsTo
    {
        // order_items.product_id → products.id
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
