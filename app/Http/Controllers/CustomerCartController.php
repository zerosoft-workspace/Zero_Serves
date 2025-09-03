<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Table;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CustomerCartController extends Controller
{
    // Tam sayfa sepet görünümü
    public function view(string $token)
    {
        $table = Table::where('token', $token)->firstOrFail();

        return view('customer.cart', [
            'table' => $table,
        ]);
    }

    // Sepeti JSON döndür (yalnızca oturum/cart içeriği)
    public function items(Request $request, string $token)
    {
        $table = Table::where('token', $token)->firstOrFail();

        $items = [];
        $total = 0.0;

        // 1) SESSION/CACHE: yalnızca henüz siparişe dönmemiş sepet verisi
        $itemsRaw = [];

        $sessionCandidates = [
            "cart:{$token}",
            "cart:table:{$table->id}",
            "customer_cart_{$table->id}",
            "customer_cart_{$token}",
        ];
        foreach ($sessionCandidates as $key) {
            if (session()->has($key)) {
                $val = session($key, []);
                if (!empty($val)) {
                    $itemsRaw = $val;
                    break;
                }
            }
        }

        if (empty($itemsRaw)) {
            try {
                foreach (["cart:{$token}", "cart:table:{$table->id}"] as $ckey) {
                    $val = Cache::get($ckey, []);
                    if (!empty($val)) {
                        $itemsRaw = $val;
                        break;
                    }
                }
            } catch (\Throwable $e) {
                // ignore
            }
        }

        if (!empty($itemsRaw)) {
            foreach ($itemsRaw as $row) {
                $pid = $row['product_id'] ?? null;
                $qty = (int) ($row['quantity'] ?? $row['qty'] ?? 1);
                if (!$pid || $qty < 1)
                    continue;

                $p = Product::find($pid);
                if (!$p)
                    continue;

                $price = (float) ($p->price ?? 0);
                $line = $qty * $price;
                $total += $line;

                $items[] = [
                    'id' => $p->id,
                    'name' => $p->name,
                    'price' => $price,
                    'quantity' => $qty,
                    'image' => $p->image_url ?? asset('images/placeholder.jpg'),
                    'description' => $p->description ?? '',
                ];
            }
        }

        return response()->json([
            'table' => ['name' => $table->name, 'token' => $table->token],
            'items' => $items,
            'total' => round($total, 2),
        ]);
    }
}
