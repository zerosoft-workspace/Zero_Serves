{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')
@section('content')
    <div class="container py-4">
        <h2 class="mb-3">Admin Paneli</h2>

        <div class="d-flex gap-2 mb-3">
            {{-- Masalar Butonu --}}
            <a href="{{ route('admin.tables') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-grid-3x3-gap"></i> Masalar
            </a>
            <a href="{{ route('admin.products.index') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-grid-3x3-gap"></i> ürünler
            </a>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-grid-3x3-gap"></i> kategoriler
            </a>
            {{-- Çıkış --}}
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button class="btn btn-outline-danger btn-sm">Çıkış</button>
            </form>
        </div>

        <hr>
        <p>Hoş geldin, {{ auth()->user()->name }} ({{ auth()->user()->role }})</p>
    </div>
@endsection