@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2 class="mb-3">Masalar</h2>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- Masa ekleme --}}
        <form action="{{ route('admin.tables.store') }}" method="POST" class="row g-2 align-items-center mb-4">
            @csrf
            <div class="col-sm-6 col-md-4 col-lg-3">
                <input type="text" name="name" class="form-control" placeholder="Masa Adı" required>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Masa Ekle</button>
            </div>
        </form>

        {{-- Masa kartları --}}
        <div class="row g-3">
            @forelse($tables as $table)
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card h-100 p-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <h5 class="mb-1">{{ $table->name }}</h5>
                            {{-- (İstersen status badge koyabilirsin) --}}
                            {{-- <x-status-badge :status="$table->status" /> --}}
                        </div>

                        {{-- QR: token ile müşteri sayfası --}}
                        <div class="qr-code my-2 text-center">
                            {!! QrCode::size(180)->generate(route('customer.table.token', $table->token)) !!}
                        </div>

                        {{-- Token linki --}}
                        <p class="small mb-3">
                            <strong>Token:</strong>
                            <a href="{{ route('customer.table.token', $table->token) }}" target="_blank">
                                {{ $table->token }}
                            </a>
                        </p>

                        <div class="mt-auto d-flex gap-2">
                            {{-- Sil --}}
                            <form method="POST" action="{{ route('admin.tables.destroy', $table->id) }}"
                                onsubmit="return confirm('Bu masayı silmek istediğinize emin misiniz?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">Sil</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info mb-0">Henüz masa eklenmemiş.</div>
                </div>
            @endforelse
        </div>
    </div>
@endsection