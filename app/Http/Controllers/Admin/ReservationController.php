<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Mail\ReservationApproved;
use App\Mail\ReservationRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;

class ReservationController extends Controller
{
    /**
     * Admin: Rezervasyon listesi (arama + tarih + opsiyonel durum filtresi)
     */
    public function index(Request $request)
    {
        $q = Reservation::query()->latest('created_at');

        // Arama: ad / e-posta / telefon
        if ($request->filled('search')) {
            $s = trim((string) $request->input('search'));
            $q->where(function ($w) use ($s) {
                $w->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        // Durum filtresi (approved / rejected / pending vb.)
        if ($request->filled('status')) {
            $q->where('status', $request->input('status'));
        }

        // Tarih filtresi (input type="date" -> Y-m-d gelir)
        if ($request->filled('date')) {
            $dateStr = (string) $request->input('date'); // "2025-09-02" gibi

            // Güvenli kontrol: geçerli bir Y-m-d mi?
            try {
                $date = Carbon::createFromFormat('Y-m-d', $dateStr)->startOfDay();
                // Kolonun tipi DATE/datetime ve ISO formatlı ise:
                $q->whereDate('date', $date->toDateString());

                // NOT: Eğer veritabanında "date" kolonu TEXT ve başka formatta tutuluyorsa,
                // yukarıdaki satırı bununla değiştirin:
                // $q->where('date', $dateStr);
            } catch (\Exception $e) {
                // format bozuksa filtre uygulamıyoruz
            }
        }

        // Sayfalama + query string'in korunması
        $reservations = $q->paginate(20)->withQueryString();

        return view('admin.reservations.index', compact('reservations'));
    }

    /**
     * Okunmamış sayısı (badge vs. için)
     */
    public function unreadCount()
    {
        $count = Reservation::whereNull('read_at')->count();
        return response()->json(['count' => $count]);
    }

    /**
     * Detay
     */
    public function show(Reservation $reservation)
    {
        if (is_null($reservation->read_at)) {
            // okunma işaretle
            $reservation->read_at = now();
            $reservation->save();
        }
        return view('admin.reservations.show', compact('reservation'));
    }

    /**
     * Onayla
     */
    public function approve(Reservation $reservation, Request $request)
    {
        $request->validate([
            'admin_note' => 'nullable|string|max:500'
        ]);

        $reservation->update([
            'status' => 'approved',
            'admin_note' => $request->admin_note,
            'status_updated_at' => now(),
            'status_updated_by' => optional(auth()->user())->id,
        ]);

        try {
            Mail::to($reservation->email)->send(new ReservationApproved($reservation));
            $message = 'Rezervasyon onaylandı ve müşteriye e-posta gönderildi.';
        } catch (\Exception $e) {
            $message = 'Rezervasyon onaylandı ancak e-posta gönderilemedi: ' . $e->getMessage();
        }

        return back()->with('success', $message);
    }

    /**
     * Reddet
     */
    public function reject(Reservation $reservation, Request $request)
    {
        $request->validate([
            'admin_note' => 'nullable|string|max:500'
        ]);

        $reservation->update([
            'status' => 'rejected',
            'admin_note' => $request->admin_note,
            'status_updated_at' => now(),
            'status_updated_by' => optional(auth()->user())->id,
        ]);

        try {
            Mail::to($reservation->email)->send(new ReservationRejected($reservation));
            $message = 'Rezervasyon reddedildi ve müşteriye e-posta gönderildi.';
        } catch (\Exception $e) {
            $message = 'Rezervasyon reddedildi ancak e-posta gönderilemedi: ' . $e->getMessage();
        }

        return back()->with('success', $message);
    }

    /**
     * Sil
     */
    public function destroy(Reservation $reservation)
    {
        $reservation->delete();
        return back()->with('success', 'Rezervasyon silindi.');
    }
}
