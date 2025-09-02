<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Mail\ReservationApproved;
use App\Mail\ReservationRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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

    // Rezervasyon Onaylama
    public function approve(Reservation $reservation, Request $request)
    {
        $request->validate([
            'admin_note' => 'nullable|string|max:500'
        ]);

        $reservation->update([
            'status' => 'approved',
            'admin_note' => $request->admin_note,
            'status_updated_at' => now(),
            'status_updated_by' => auth()->id()
        ]);

        try {
            Mail::to($reservation->email)->send(new ReservationApproved($reservation));
            $message = 'Rezervasyon onaylandı ve müşteriye e-posta gönderildi.';
        } catch (\Exception $e) {
            $message = 'Rezervasyon onaylandı ancak e-posta gönderilemedi: ' . $e->getMessage();
        }

        return back()->with('success', $message);
    }

    // Rezervasyon Reddetme
    public function reject(Reservation $reservation, Request $request)
    {
        $request->validate([
            'admin_note' => 'nullable|string|max:500'
        ]);

        $reservation->update([
            'status' => 'rejected',
            'admin_note' => $request->admin_note,
            'status_updated_at' => now(),
            'status_updated_by' => auth()->id()
        ]);

        try {
            Mail::to($reservation->email)->send(new ReservationRejected($reservation));
            $message = 'Rezervasyon reddedildi ve müşteriye e-posta gönderildi.';
        } catch (\Exception $e) {
            $message = 'Rezervasyon reddedildi ancak e-posta gönderilemedi: ' . $e->getMessage();
        }

        return back()->with('success', $message);
    }

    // Sil
    public function destroy(Reservation $reservation)
    {
        $reservation->delete();
        return back()->with('success', 'Rezervasyon silindi.');
    }
}
