<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['category_id', 'name', 'description', 'price', 'stock', 'image', 'low_stock_threshold', 'is_active'];


    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
