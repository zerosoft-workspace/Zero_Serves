<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use App\Models\Category;
use App\Models\WaiterCall;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CustomerOrderController extends Controller
{
    // Menü sayfası
    public function index(string $token)
    {
        $table = Table::where('token', $token)->firstOrFail();

        $categories = Category::with([
            'products' => function ($q) {
                $q->where('is_active', true);
            }
        ])->get();

        // Bu masanın aktif siparişleri (ödenen siparişler hariç)
        $orders = Order::with('items')
            ->where('table_id', $table->id)
            ->where('status', '!=', 'paid')
            ->where('payment_status', '!=', 'paid')
            ->orderBy('created_at', 'desc')
            ->get();

        // Handle different view types
        $view = request('view');
        
        switch ($view) {
            case 'menu':
                return view('customer.menu', compact('categories', 'table', 'orders'));
            
            case 'cart':
                return view('customer.cart', compact('categories', 'table', 'orders'));
            
            case 'orders':
                return view('customer.orders', compact('categories', 'table', 'orders'));
            
            default:
                // First visit or dashboard request - show dashboard
                return view('customer.dashboard', compact('categories', 'table', 'orders'));
        }
    }

    // Sepete ekle
    public function addToCart(Request $request, string $token)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'qty' => 'nullable|integer|min:1'
        ]);

        $qty = max(1, (int) $request->input('qty', 1));
        $product = Product::findOrFail($request->product_id);

        $cart = session('cart', []);
        if (isset($cart[$product->id])) {
            $cart[$product->id]['qty'] += $qty;
        } else {
            $cart[$product->id] = [
                'name' => $product->name,
                'price' => (float) $product->price,
                'qty' => $qty,
            ];
        }
        session(['cart' => $cart]);

        return back()->with('success', 'Sepete eklendi');
    }

    // Sepetten çıkar
    public function removeFromCart(string $token, int $productId)
    {
        $cart = session('cart', []);
        unset($cart[$productId]);
        session(['cart' => $cart]);
        return back();
    }

    // Sepeti boşalt
    public function clearCart(string $token)
    {
        session()->forget('cart');
        return back();
    }

    // Siparişi oluştur
    public function checkout(Request $request, string $token)
    {
        $table = Table::where('token', $token)->firstOrFail();
        $cart = session('cart', []);
        if (empty($cart))
            return back()->with('error', 'Sepetiniz boş');

        foreach ($cart as $pid => $row) {
            $p = Product::find($pid);
            if (!$p || $p->stock < $row['qty']) {
                return back()->with('error', $row['name'] . ' için yeterli stok yok.');
            }
        }

        $total = 0;
        foreach ($cart as $pid => $row) {
            $total += $row['price'] * $row['qty'];
        }

        $order = Order::create([
            'table_id' => $table->id,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'total_amount' => $total,
        ]);

        foreach ($cart as $pid => $row) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $pid,
                'quantity' => $row['qty'],
                'price' => $row['price'],
                'line_total' => $row['price'] * $row['qty'],
            ]);
            Product::find($pid)?->decrement('stock', $row['qty']);
        }

        $table->update(['status' => 'occupied']);
        
        // ✅ YENİ SİPARİŞ OLUŞTURULDUĞUNDA CACHE TEMİZLE
        $this->clearWaiterCache($table->id);

        session()->forget('cart');

        return redirect()->route('customer.table.token', $token)
            ->with('success', 'Siparişiniz alındı! Garson onaylayacaktır.');
    }

    // 🚨 Garson Çağır
    public function callWaiter(Request $request, string $token)
    {
        $table = Table::where('token', $token)->firstOrFail();

        WaiterCall::create([
            'table_id' => $table->id,
            'status' => 'new',
        ]);
        
        // ✅ YENİ ÇAĞRI OLUŞTURULDUĞUNDA CACHE TEMİZLE
        $this->clearWaiterCache($table->id);

        return back()->with('success', 'Garson çağrısı gönderildi.');
    }

    // 💳 Ödeme İsteği
    public function pay(Request $request, string $token)
    {
        $table = Table::where('token', $token)->firstOrFail();

        $total = Order::where('table_id', $table->id)
            ->where('payment_status', 'unpaid')
            ->sum('total_amount');

        if ($total <= 0) {
            return back()->with('error', 'Ödenecek sipariş bulunamadı.');
        }

        Payment::create([
            'table_id' => $table->id,
            'total_amount' => $total,
            'method' => 'cash', // şimdilik sabit, ileride online seçilebilir
            'status' => 'pending',
        ]);

        return back()->with('success', 'Ödeme isteğiniz alındı, garson yanınıza gelecek.');
    }
    
    /**
     * Garson paneli cache'ini temizle
     */
    protected function clearWaiterCache(int $tableId): void
    {
        try {
            // Spesifik masa cache'ini temizle
            Cache::forget("waiter_table_{$tableId}");
            
            // Dashboard cache'lerini temizle
            $dashboardKeys = [
                'waiter_dashboard_' . md5('_'),
                'waiter_dashboard_' . md5('pending_'),
                'waiter_dashboard_' . md5('preparing_'),
                'waiter_dashboard_' . md5('delivered_'),
                'waiter_dashboard_' . md5('paid_'),
            ];
            
            foreach ($dashboardKeys as $key) {
                Cache::forget($key);
            }
            
            // Tüm dashboard cache'lerini temizlemek için pattern matching
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                $redis = Cache::getStore()->getRedis();
                $keys = $redis->keys('*waiter_dashboard_*');
                if (!empty($keys)) {
                    $redis->del($keys);
                }
                
                $callKeys = $redis->keys('*waiter_calls_*');
                if (!empty($callKeys)) {
                    $redis->del($callKeys);
                }
            } else {
                // File cache için alternatif temizleme
                $cacheKeys = [
                    'waiter_dashboard_*',
                    'waiter_calls_*'
                ];
                
                foreach ($cacheKeys as $pattern) {
                    // File cache için manuel temizleme gerekebilir
                    Cache::flush(); // Son çare olarak tüm cache'i temizle
                    break;
                }
            }
            
        } catch (\Exception $e) {
            // Cache temizleme hatası durumunda log'la ama işlemi durdurma
            \Log::warning('Waiter cache temizleme hatası: ' . $e->getMessage());
        }
    }
}
