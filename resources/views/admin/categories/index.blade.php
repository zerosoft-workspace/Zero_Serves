@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2>Kategoriler</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.categories.store') }}" class="d-flex gap-2 mb-3">
            @csrf
            <input type="text" name="name" class="form-control" placeholder="Kategori adı" required>
            <button class="btn btn-primary">Ekle</button>
        </form>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Kategori</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $cat)
                    <tr>
                        <td>{{ $cat->id }}</td>
                        <td>{{ $cat->name }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.categories.destroy', $cat->id) }}"
                                onsubmit="return confirm('Silinsin mi?')">
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