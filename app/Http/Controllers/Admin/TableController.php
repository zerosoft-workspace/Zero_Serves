<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// PDF & QR
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Helpers\NetworkHelper;

class TableController extends Controller
{
    /**
     * Masa yönetimi ana sayfası
     * Not: blade 'active_order' ve 'waiter' kullanıyor → eager load edelim.
     */
    public function index()
    {
        $tables = Table::with(['waiter:id,name,email', 'active_order'])->get();
        $waiters = User::where('role', 'waiter')->select('id', 'name', 'email')->get();

        return view('admin.tables.index', compact('tables', 'waiters'));
    }

    /**
     * Yeni masa oluştur
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
        ]);

        Table::create([
            'name' => $request->name,
            'token' => bin2hex(random_bytes(16)), // benzersiz token
            'status' => 'empty',
        ]);

        return redirect()->back()->with('success', 'Masa oluşturuldu');
    }

    /**
     * Masayı tamamen temizle (açık siparişleri sil + durumu boş yap)
     */
    public function clear(Table $table)
    {
        // Açık siparişleri sil
        $table->orders()
            ->whereIn('status', ['pending', 'preparing', 'delivered'])
            ->delete();

        // Masayı boş yap
        $table->status = 'empty';
        $table->save();

        return back()->with('success', 'Masa başarıyla temizlendi.');
    }

    /**
     * Masa sil
     */
    public function destroy(Table $table)
    {
        $hasOpen = $table->orders()
            ->whereIn('status', ['pending', 'preparing', 'delivered'])
            ->exists();

        if ($hasOpen) {
            return back()->with('error', 'Bu masada devam eden sipariş var. Önce siparişi kapatın (Ödendi).');
        }

        $table->delete();
        return back()->with('success', 'Masa silindi.');
    }

    /**
     * Masa-garson atama (tekil)
     */
    public function assignWaiter(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'waiter_id' => 'nullable|exists:users,id',
        ]);

        if ($request->waiter_id) {
            $waiter = User::find($request->waiter_id);
            if ($waiter->role !== 'waiter') {
                return response()->json([
                    'success' => false,
                    'message' => 'Seçilen kullanıcı garson değil!'
                ], 400);
            }
        }

        $table = Table::findOrFail($request->table_id);
        $table->waiter_id = $request->waiter_id;
        $table->save();

        $waiterName = $request->waiter_id ? User::find($request->waiter_id)->name : 'Atanmadı';

        return response()->json([
            'success' => true,
            'message' => "Masa {$table->name} başarıyla {$waiterName} garsonuna atandı.",
            'waiter_name' => $waiterName,
        ]);
    }

    /**
     * Toplu masa-garson atama
     */
    public function bulkAssign(Request $request)
    {
        $request->validate([
            'assignments' => 'required|array',
            'assignments.*.table_id' => 'required|exists:tables,id',
            'assignments.*.waiter_id' => 'nullable|exists:users,id',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->assignments as $assignment) {
                if (!empty($assignment['waiter_id'])) {
                    $waiter = User::find($assignment['waiter_id']);
                    if (!$waiter || $waiter->role !== 'waiter') {
                        throw new \Exception("Kullanıcı uygun değil (garson değil).");
                    }
                }

                Table::where('id', $assignment['table_id'])
                    ->update(['waiter_id' => $assignment['waiter_id']]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Toplu atama başarıyla tamamlandı.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Toplu atama sırasında hata: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Garson atama durumu raporu
     */
    public function assignmentReport()
    {
        $report = DB::table('tables')
            ->leftJoin('users', 'tables.waiter_id', '=', 'users.id')
            ->select(
                'users.name as waiter_name',
                'users.email as waiter_email',
                DB::raw('COUNT(tables.id) as table_count'),
                DB::raw('GROUP_CONCAT(tables.name ORDER BY tables.name) as table_names')
            )
            ->groupBy('users.id', 'users.name', 'users.email')
            ->get();

        $unassignedTables = Table::whereNull('waiter_id')->pluck('name')->toArray();

        return response()->json([
            'assignments' => $report,
            'unassigned_tables' => $unassignedTables,
            'unassigned_count' => count($unassignedTables),
        ]);
    }

    /**
     * Tüm masaların QR'larını tek PDF
     */
    public function qrPdfAll()
    {
        $tables = Table::orderBy('name')->get();
        $items = $this->buildQrItems($tables);

        $pdf = Pdf::loadView('admin.tables.qr-pdf', [
            'items' => $items,
            'title' => 'Masalar QR',
        ])->setPaper('a4', 'portrait');

        return $pdf->download('masalar-qr-' . now()->format('Ymd-His') . '.pdf');
    }

    /**
     * Tek masanın QR'ı PDF
     */
    public function qrPdfSingle(Table $table)
    {
        $items = $this->buildQrItems(collect([$table]));

        $pdf = Pdf::loadView('admin.tables.qr-pdf', [
            'items' => $items,
            'title' => 'Masa QR - ' . $table->name,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('masa-' . $table->id . '-qr-' . now()->format('Ymd-His') . '.pdf');
    }

    /**
     * Yardımcı: URL + SVG hazırla (Imagick gerekmez)
     */
    /**
     * URL + base64 SVG hazırla (Imagick gerekmez, DomPDF uyumlu)
     */
    protected function buildQrItems($tables)
    {
        return $tables->map(function ($table) {
            $url = NetworkHelper::getTableQrUrl($table->token);

            // SVG üret
            $svg = \QrCode::format('svg')
                ->size(600)   // yüksek çözünürlük; boyutu CSS'te kısacağız
                ->margin(0)
                ->generate($url);

            // base64 olarak <img src="data:..."> içinde kullanacağız
            $svgBase64 = 'data:image/svg+xml;base64,' . base64_encode($svg);

            return [
                'name' => $table->name,
                'token' => $table->token,
                'url' => $url,
                'img' => $svgBase64,  // <-- dikkat: img anahtarı
            ];
        });
    }

}
