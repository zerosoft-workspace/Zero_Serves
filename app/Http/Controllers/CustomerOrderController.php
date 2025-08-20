<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use Illuminate\Http\Request;
use App\Models\Category;


class CustomerOrderController extends Controller
{
    public function index()
    {
        // Sadece aktif ürünleri çekecek
        $categories = Category::with([
            'products' => function ($q) {
                $q->where('is_active', true);
            }
        ])->get();

        return view('customer.menu', compact('categories'));
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
            $p = \App\Models\Product::find($pid);
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
            'total_price' => $total,
        ]);

        foreach ($cart as $pid => $row) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $pid,
                'quantity' => $row['qty'],
                'price' => $row['price'],
                'line_total' => $row['price'] * $row['qty'],
            ]);
        }
        foreach ($cart as $pid => $row) {
            $p = \App\Models\Product::find($pid);
            $p->decrement('stock', $row['qty']);
        }

        // Masayı "order_pending" yapalım (ileride garson paneli görecek)
        $table->update(['status' => 'order_pending']);

        session()->forget('cart');

        return redirect()->route('customer.table.token', $token)
            ->with('success', 'Siparişiniz alındı! Garson onaylayacaktır.');
    }
}
