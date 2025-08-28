@extends('layouts.admin')
@section('title', 'Rezervasyonlar')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold">Rezervasyonlar</h2>
            <p class="text-muted mb-0">Müşteri tarafından gönderilen rezervasyon talepleri</p>
        </div>
        <form class="d-flex gap-2" method="get" action="{{ route('admin.reservations.index') }}">
            <input type="date" name="date" value="{{ request('date') }}" class="form-control form-control-sm">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm"
                placeholder="Ad / E-posta / Telefon">
            <button class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
            @if(request()->hasAny(['date', 'search']))
                <a href="{{ route('admin.reservations.index') }}" class="btn btn-sm btn-outline-secondary">Temizle</a>
            @endif
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Ad Soyad</th>
                        <th>Telefon</th>
                        <th>E-posta</th>
                        <th>Tarih</th>
                        <th>Saat</th>
                        <th>Kişi</th>
                        <th>Gönderim</th>
                        <th class="text-end">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservations as $r)
                        <tr>
                            <td>{{ $r->id }}</td>
                            <td>{{ $r->name }}</td>
                            <td>{{ $r->phone }}</td>
                            <td>{{ $r->email }}</td>
                            <td>
                                @php $d = $r->date ? \Illuminate\Support\Carbon::parse($r->date) : null; @endphp
                                {{ $d ? $d->format('d.m.Y') : '-' }}
                            </td>
                            <td>
                                @php $t = $r->time ? \Illuminate\Support\Carbon::parse($r->time) : null; @endphp
                                {{ $t ? $t->format('H:i') : '-' }}
                            </td>
                            <td>{{ $r->people ?? '-' }}</td>
                            <td>{{ optional($r->created_at)->format('d.m.Y H:i') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.reservations.show', $r) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form action="{{ route('admin.reservations.destroy', $r) }}" method="post" class="d-inline"
                                    onsubmit="return confirm('Silinsin mi?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">Kayıt bulunamadı.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $reservations->links() }}
        </div>
    </div>
@endsection