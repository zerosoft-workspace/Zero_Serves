<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['category_id', 'name', 'description', 'price', 'stock_quantity', 'image', 'min_stock_level', 'max_stock_level', 'is_active'];


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Stok durumu kontrolü
     */
    public function isLowStock()
    {
        return $this->stock_quantity <= $this->min_stock_level;
    }

    /**
     * Kritik stok durumu (stok bitti)
     */
    public function isOutOfStock()
    {
        return $this->stock_quantity <= 0;
    }

    /**
     * Stok azaltma
     */
    public function decreaseStock($quantity)
    {
        if ($this->stock_quantity >= $quantity) {
            $this->stock_quantity -= $quantity;
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * Stok artırma
     */
    public function increaseStock($quantity)
    {
        $this->stock_quantity += $quantity;
        $this->save();
        return true;
    }

    /**
     * Düşük stoklu ürünleri getir
     */
    public static function getLowStockProducts()
    {
        return self::whereRaw('stock_quantity <= min_stock_level')
            ->where('is_active', true)
            ->get();
    }

    /**
     * Stok durumu badge rengi
     */
    public function getStockStatusAttribute()
    {
        if ($this->isOutOfStock()) {
            return 'danger';
        } elseif ($this->isLowStock()) {
            return 'warning';
        } else {
            return 'success';
        }
    }

    /**
     * Stok durumu metni
     */
    public function getStockStatusTextAttribute()
    {
        if ($this->isOutOfStock()) {
            return 'Stok Bitti';
        } elseif ($this->isLowStock()) {
            return 'Düşük Stok';
        } else {
            return 'Stokta';
        }
    }
}
