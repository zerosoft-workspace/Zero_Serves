<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class StockService
{
    /**
     * Sipariş sonrası stok güncelleme
     */
    public function updateStockAfterOrder(Order $order)
    {
        $order->load('items.product'); // ürünleri kesin yükle
        foreach ($order->items as $item) {
            $product = $item->product;

            if ($product) {
                $success = $product->decreaseStock($item->quantity);

                if (!$success) {
                    Log::warning("Stok yetersiz: {$product->name} - Sipariş: {$order->id}");
                }

                // Düşük stok kontrolü
                if ($product->isLowStock()) {
                    $this->triggerLowStockAlert($product);
                }
            }
        }
    }

    /**
     * Sipariş iptali sonrası stok geri yükleme
     */
    public function restoreStockAfterCancellation(Order $order)
    {
        foreach ($order->items as $item) {
            $product = $item->product;

            if ($product) {
                $product->increaseStock($item->quantity);
                Log::info("Stok geri yüklendi: {$product->name} - Miktar: {$item->quantity}");
            }
        }
    }

    /**
     * Düşük stok uyarısı tetikleme
     */
    public function triggerLowStockAlert(Product $product)
    {
        $cacheKey = "low_stock_alert_{$product->id}";

        // Son 1 saatte aynı ürün için uyarı gönderilmişse tekrar gönderme
        if (Cache::has($cacheKey)) {
            return;
        }

        // Uyarı cache'e al (1 saat)
        Cache::put($cacheKey, true, 3600);

        // Log kaydet
        Log::warning("Düşük stok uyarısı: {$product->name} - Mevcut: {$product->stock_quantity}, Minimum: {$product->min_stock_level}");

        // Burada email, SMS veya push notification gönderilebilir
        // event(new LowStockAlert($product));
    }

    /**
     * Tüm düşük stoklu ürünleri kontrol et
     */
    public function checkAllLowStockProducts()
    {
        $lowStockProducts = Product::getLowStockProducts();

        foreach ($lowStockProducts as $product) {
            $this->triggerLowStockAlert($product);
        }

        return $lowStockProducts;
    }

    /**
     * Stok raporunu oluştur
     */
    public function generateStockReport()
    {
        $totalProducts = Product::where('is_active', true)->count();
        $lowStockCount = Product::whereRaw('stock_quantity <= min_stock_level')
            ->where('is_active', true)
            ->count();
        $outOfStockCount = Product::where('stock_quantity', '<=', 0)
            ->where('is_active', true)
            ->count();

        $totalStockValue = Product::where('is_active', true)
            ->selectRaw('SUM(stock_quantity * price) as total_value')
            ->first()
            ->total_value ?? 0;

        return [
            'total_products' => $totalProducts,
            'low_stock_count' => $lowStockCount,
            'out_of_stock_count' => $outOfStockCount,
            'total_stock_value' => $totalStockValue,
            'low_stock_percentage' => $totalProducts > 0 ? round(($lowStockCount / $totalProducts) * 100, 1) : 0
        ];
    }

    /**
     * Ürün stok geçmişini kaydet
     */
    public function logStockMovement(Product $product, $type, $quantity, $reason = null)
    {
        // Bu method için ayrı bir StockMovement modeli oluşturulabilir
        Log::info("Stok hareketi: {$product->name} - Tip: {$type} - Miktar: {$quantity} - Sebep: {$reason}");
    }

    /**
     * Otomatik stok siparişi önerisi
     */
    public function suggestRestockOrders()
    {
        $lowStockProducts = Product::getLowStockProducts();
        $suggestions = [];

        foreach ($lowStockProducts as $product) {
            $suggestedQuantity = max($product->min_stock_level * 2, 10); // Minimum stokun 2 katı veya en az 10 adet

            $suggestions[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'current_stock' => $product->stock_quantity,
                'min_stock' => $product->min_stock_level,
                'suggested_quantity' => $suggestedQuantity,
                'estimated_cost' => $suggestedQuantity * $product->price
            ];
        }

        return $suggestions;
    }
}
