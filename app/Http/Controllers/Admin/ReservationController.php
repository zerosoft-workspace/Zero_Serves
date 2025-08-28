<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    // Liste
    public function index(Request $request)
    {
        $q = Reservation::query()->latest();

        if ($request->filled('search')) {
            $s = trim((string) $request->input('search'));
            $q->where(function ($w) use ($s) {
                $w->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        if ($request->filled('date')) {
            // Eğer migration'da alan adın farklıysa (örn. reservation_date) bunu değiştir.
            $q->whereDate('date', $request->date('date'));
        }

        $reservations = $q->paginate(20)->withQueryString();
        return view('admin.reservations.index', compact('reservations'));
    }

    public function unreadCount()
    {
        $count = \App\Models\Reservation::whereNull('read_at')->count();
        return response()->json(['count' => $count]);
    }

    public function show(\App\Models\Reservation $reservation)
    {
        if (is_null($reservation->read_at)) {
            $reservation->read_at = now();
            $reservation->save();
        }
        return view('admin.reservations.show', compact('reservation'));
    }

    // Sil
    public function destroy(Reservation $reservation)
    {
        $reservation->delete();
        return back()->with('success', 'Rezervasyon silindi.');
    }
}
