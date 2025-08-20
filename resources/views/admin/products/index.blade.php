@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2>Ürünler</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Ürün Ekleme Formu --}}
        <form method="POST" action="{{ route('admin.products.store') }}" class="row g-2 mb-4">
            @csrf
            <div class="col-md-3">
                <input type="text" name="name" class="form-control" placeholder="Ürün adı" required>
            </div>
            <div class="col-md-2">
                <input type="number" step="0.01" name="price" class="form-control" placeholder="Fiyat" required>
            </div>
            <div class="col-md-3">
                <select name="category_id" class="form-select">
                    <option value="">Kategori seç</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" name="stock" class="form-control" placeholder="Stok" value="100" min="0">
            </div>
            <div class="col-md-2">
                <input type="number" name="low_stock_threshold" class="form-control" placeholder="Kritik Eşik" value="10"
                    min="0">
            </div>

            <div class="col-12 d-flex justify-content-end mt-2">
                <button class="btn btn-success">Ekle</button>
            </div>
        </form>

        {{-- Ürün Listesi --}}
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Ürün</th>
                    <th>Kategori</th>
                    <th>Fiyat</th>
                    <th>Stok</th>
                    <th>Kritik</th>
                    <th>Durum</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $p)
                    <tr>
                        <td>{{ $p->id }}</td>
                        <td>{{ $p->name }}</td>
                        <td>{{ $p->category->name ?? '-' }}</td>
                        <td>{{ number_format($p->price, 2) }} ₺</td>
                        <td>
                            {{ $p->stock }}
                            @if(($p->low_stock_threshold ?? 0) > 0 && $p->stock <= $p->low_stock_threshold)
                                <span class="badge bg-warning text-dark ms-1">Azaldı</span>
                            @endif
                        </td>
                        <td>{{ $p->low_stock_threshold ?? 0 }}</td>
                        <td>
                            @if($p->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Pasif</span>
                            @endif
                        </td>
                        <td class="d-flex gap-2">
                            @if($p->is_active)
                                {{-- Pasifleştir --}}
                                <form method="POST" action="{{ route('admin.products.deactivate', $p->id) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm btn-outline-warning">Pasifleştir</button>
                                </form>
                            @else
                                {{-- Aktifleştir --}}
                                <form method="POST" action="{{ route('admin.products.activate', $p->id) }}">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-sm btn-outline-success">Aktifleştir</button>
                                </form>
                            @endif

                            {{-- Sil (artık gerçekten siler) --}}
                            <form method="POST" action="{{ route('admin.products.destroy', $p->id) }}"
                                onsubmit="return confirm('Ürün tamamen silinsin mi?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Sil</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
@endsection