<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    // Rezervasyon formunu göster
    public function index()
    {
        return view('reservation');
    }

    // Rezervasyon kaydet
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'date' => 'required|date',
            'time' => 'required',
            'people' => 'required|string',
        ]);

        Reservation::create($request->all());

        return redirect()->back()->with('success', 'Rezervasyonunuz alındı!');
    }

    // Admin paneli için rezervasyonları listele
    public function admin()
    {
        $reservations = Reservation::all();
        return view('admin', compact('reservations'));
    }



}