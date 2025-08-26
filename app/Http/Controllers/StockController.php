<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
        $this->middleware('auth');
    }

    /**
     * Stok yönetimi ana sayfası
     */
    public function index(Request $request)
    {
        $query = Product::with('category')->where('is_active', true);

        // Arama filtresi
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Kategori filtresi
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Stok durumu filtresi
        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'low':
                    $query->whereRaw('stock_quantity <= min_stock_level');
                    break;
                case 'out':
                    $query->where('stock_quantity', 0);
                    break;
                case 'ok':
                    $query->whereRaw('stock_quantity > min_stock_level');
                    break;
            }
        }

        $products = $query->orderBy('name')->paginate(20);
        $stockReport = $this->stockService->generateStockReport();
        $lowStockProducts = Product::getLowStockProducts();

        return view('admin.stock.index', compact('products', 'stockReport', 'lowStockProducts'));
    }

    /**
     * Düşük stoklu ürünler sayfası
     */
    public function lowStock()
    {
        $lowStockProducts = Product::getLowStockProducts();
        $suggestions = $this->stockService->suggestRestockOrders();

        return view('admin.stock.low-stock', compact('lowStockProducts', 'suggestions'));
    }

    /**
     * Stok raporları sayfası
     */
    public function reports()
    {
        $stockReport = $this->stockService->generateStockReport();
        
        // Kategori bazında stok analizi
        $categoryStockAnalysis = Category::with(['products' => function($query) {
            $query->where('is_active', true);
        }])->get()->map(function($category) {
            $totalProducts = $category->products->count();
            $lowStockCount = $category->products->filter(function($product) {
                return $product->isLowStock();
            })->count();
            $outOfStockCount = $category->products->filter(function($product) {
                return $product->isOutOfStock();
            })->count();
            $totalValue = $category->products->sum(function($product) {
                return $product->stock_quantity * $product->price;
            });

            return [
                'category_name' => $category->name,
                'total_products' => $totalProducts,
                'low_stock_count' => $lowStockCount,
                'out_of_stock_count' => $outOfStockCount,
                'total_value' => $totalValue,
                'low_stock_percentage' => $totalProducts > 0 ? round(($lowStockCount / $totalProducts) * 100, 1) : 0
            ];
        });

        return view('admin.stock.reports', compact('stockReport', 'categoryStockAnalysis'));
    }

    /**
     * Stok güncelleme
     */
    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'max_stock_level' => 'nullable|integer|min:0',
            'reason' => 'nullable|string|max:255'
        ]);

        $oldStock = $product->stock_quantity;
        $newStock = $request->stock_quantity;
        $difference = $newStock - $oldStock;

        $updateData = [
            'stock_quantity' => $newStock,
            'min_stock_level' => $request->min_stock_level
        ];

        if ($request->filled('max_stock_level')) {
            $updateData['max_stock_level'] = $request->max_stock_level;
        }

        $product->update($updateData);

        // Stok hareketini logla
        $movementType = $difference > 0 ? 'increase' : 'decrease';
        $this->stockService->logStockMovement(
            $product, 
            $movementType, 
            abs($difference), 
            $request->reason ?? 'Manuel güncelleme'
        );

        // Düşük stok kontrolü
        if ($product->isLowStock()) {
            $this->stockService->triggerLowStockAlert($product);
        }

        return response()->json([
            'success' => true,
            'message' => 'Stok başarıyla güncellendi',
            'product' => [
                'id' => $product->id,
                'stock_quantity' => $product->stock_quantity,
                'min_stock_level' => $product->min_stock_level,
                'stock_status' => $product->stock_status,
                'stock_status_text' => $product->stock_status_text
            ]
        ]);
    }

    /**
     * Toplu stok güncelleme
     */
    public function bulkUpdateStock(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.stock_quantity' => 'required|integer|min:0',
            'products.*.min_stock_level' => 'required|integer|min:0'
        ]);

        $updatedCount = 0;
        $errors = [];

        DB::transaction(function() use ($request, &$updatedCount, &$errors) {
            foreach ($request->products as $productData) {
                try {
                    $product = Product::findOrFail($productData['id']);
                    
                    $product->update([
                        'stock_quantity' => $productData['stock_quantity'],
                        'min_stock_level' => $productData['min_stock_level']
                    ]);

                    // Düşük stok kontrolü
                    if ($product->isLowStock()) {
                        $this->stockService->triggerLowStockAlert($product);
                    }

                    $updatedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Ürün ID {$productData['id']}: {$e->getMessage()}";
                }
            }
        });

        return response()->json([
            'success' => count($errors) === 0,
            'message' => "{$updatedCount} ürün başarıyla güncellendi",
            'updated_count' => $updatedCount,
            'errors' => $errors
        ]);
    }

    /**
     * Stok uyarılarını kontrol et
     */
    public function checkStockAlerts()
    {
        $lowStockProducts = $this->stockService->checkAllLowStockProducts();
        
        return response()->json([
            'low_stock_count' => $lowStockProducts->count(),
            'products' => $lowStockProducts->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'stock_quantity' => $product->stock_quantity,
                    'min_stock_level' => $product->min_stock_level,
                    'category' => $product->category->name ?? 'Kategorisiz'
                ];
            })
        ]);
    }

    /**
     * Stok sipariş önerileri
     */
    public function restockSuggestions()
    {
        $suggestions = $this->stockService->suggestRestockOrders();
        
        return response()->json([
            'suggestions' => $suggestions,
            'total_estimated_cost' => collect($suggestions)->sum('estimated_cost')
        ]);
    }

    /**
     * Stok durumu istatistikleri (AJAX)
     */
    public function stockStats()
    {
        $stats = $this->stockService->generateStockReport();
        $recentLowStock = Product::getLowStockProducts()->take(5);

        return response()->json([
            'stats' => $stats,
            'recent_low_stock' => $recentLowStock->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'stock_quantity' => $product->stock_quantity,
                    'min_stock_level' => $product->min_stock_level,
                    'stock_status' => $product->stock_status
                ];
            })
        ]);
    }

    /**
     * Ürün stok geçmişi
     */
    public function stockHistory(Product $product)
    {
        // Bu method için StockMovement modeli oluşturulabilir
        // Şimdilik basit bir response döndürüyoruz
        
        return response()->json([
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'current_stock' => $product->stock_quantity,
                'min_stock' => $product->min_stock_level
            ],
            'movements' => [] // Gelecekte StockMovement tablosundan gelecek
        ]);
    }

    /**
     * Stok export (CSV)
     */
    public function exportStock()
    {
        $products = Product::with('category')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $filename = 'stok_raporu_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM ekle (Excel için)
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header
            fputcsv($file, [
                'Ürün Adı',
                'Kategori', 
                'Mevcut Stok',
                'Minimum Stok',
                'Fiyat',
                'Stok Değeri',
                'Durum'
            ], ';');

            // Data
            foreach ($products as $product) {
                fputcsv($file, [
                    $product->name,
                    $product->category->name ?? 'Kategorisiz',
                    $product->stock_quantity,
                    $product->min_stock_level,
                    number_format($product->price, 2),
                    number_format($product->stock_quantity * $product->price, 2),
                    $product->stock_status_text
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
