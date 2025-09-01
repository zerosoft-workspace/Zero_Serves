<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Table;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TableController extends Controller
{
    /**
     * Masa yönetimi ana sayfası
     */
    public function index()
    {
        $tables = Table::with('waiter:id,name,email')->get();
        $waiters = User::where('role', 'waiter')->select('id', 'name', 'email')->get();
        
        return view('admin.tables.index', compact('tables', 'waiters'));
    }

    /**
     * Masa-garson atama işlemi
     */
    public function assignWaiter(Request $request)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'waiter_id' => 'nullable|exists:users,id'
        ]);

        // Garson rolü kontrolü
        if ($request->waiter_id) {
            $waiter = User::find($request->waiter_id);
            if ($waiter->role !== 'waiter') {
                return response()->json([
                    'success' => false,
                    'message' => 'Seçilen kullanıcı garson değil!'
                ], 400);
            }
        }

        $table = Table::find($request->table_id);
        $table->waiter_id = $request->waiter_id;
        $table->save();

        $waiterName = $request->waiter_id ? User::find($request->waiter_id)->name : 'Atanmadı';

        return response()->json([
            'success' => true,
            'message' => "Masa {$table->name} başarıyla {$waiterName} garsonuna atandı.",
            'waiter_name' => $waiterName
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
            'assignments.*.waiter_id' => 'nullable|exists:users,id'
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->assignments as $assignment) {
                // Garson rolü kontrolü
                if ($assignment['waiter_id']) {
                    $waiter = User::find($assignment['waiter_id']);
                    if ($waiter->role !== 'waiter') {
                        throw new \Exception("Kullanıcı {$waiter->name} garson değil!");
                    }
                }

                Table::where('id', $assignment['table_id'])
                    ->update(['waiter_id' => $assignment['waiter_id']]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Toplu atama başarıyla tamamlandı.'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Toplu atama sırasında hata: ' . $e->getMessage()
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

        // Atanmamış masalar
        $unassignedTables = Table::whereNull('waiter_id')->pluck('name')->toArray();

        return response()->json([
            'assignments' => $report,
            'unassigned_tables' => $unassignedTables,
            'unassigned_count' => count($unassignedTables)
        ]);
    }
}
