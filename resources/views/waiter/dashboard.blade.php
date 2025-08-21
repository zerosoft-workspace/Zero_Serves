{{-- resources/views/waiter/dashboard.blade.php --}}
@extends('layouts.admin') {{-- Şimdilik admin layoutunu kullandık. İstersen ayrı waiter layout açabiliriz. --}}

@section('content')
    <div class="container py-4">
        <h2 class="mb-3">Garson Paneli</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card shadow-sm">
            <div class="card-header">
                <strong>Masalar</strong>
            </div>
            <div class="card-body">
                @if($tables->count() > 0)
                    <div class="row g-3">
                        @foreach($tables as $table)
                            <div class="col-md-4 col-sm-6">
                                <div class="card h-100 border">
                                    <div class="card-body d-flex flex-column justify-content-between">
                                        <h5 class="card-title mb-2">{{ $table->name }}</h5>
                                        <p class="card-text">
                                            Durum:
                                            <span class="badge 
                                                        @if($table->status === 'empty') bg-success 
                                                        @elseif($table->status === 'pending') bg-warning 
                                                        @elseif($table->status === 'preparing') bg-info 
                                                        @elseif($table->status === 'delivered') bg-primary 
                                                        @elseif($table->status === 'paid') bg-secondary 
                                                        @else bg-dark @endif">
                                                {{ ucfirst($table->status) }}
                                            </span>
                                        </p>
                                        <a href="{{ route('waiter.table', $table->id) }}"
                                            class="btn btn-outline-dark btn-sm mt-auto">
                                            Detayları Gör
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">Hiç masa bulunamadı.</p>
                @endif
            </div>
        </div>
    </div>
@endsection