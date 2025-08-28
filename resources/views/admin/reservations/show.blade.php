@extends('layouts.admin')
@section('title', 'Rezervasyon Detayı')

@section('content')
    <a href="{{ route('admin.reservations.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Listeye Dön
    </a>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Rezervasyon Bilgileri</h5>
        </div>
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Ad Soyad</dt>
                <dd class="col-sm-9">{{ $reservation->name }}</dd>
                <dt class="col-sm-3">Telefon</dt>
                <dd class="col-sm-9">{{ $reservation->phone }}</dd>
                <dt class="col-sm-3">E-posta</dt>
                <dd class="col-sm-9">{{ $reservation->email }}</dd>
                <dt class="col-sm-3">Tarih</dt>
                <dd class="col-sm-9">
                    @php $d = $reservation->date ? \Illuminate\Support\Carbon::parse($reservation->date) : null; @endphp
                    {{ $d ? $d->format('d.m.Y') : '-' }}
                </dd>
                <dt class="col-sm-3">Saat</dt>
                <dd class="col-sm-9">
                    @php $t = $reservation->time ? \Illuminate\Support\Carbon::parse($reservation->time) : null; @endphp
                    {{ $t ? $t->format('H:i') : '-' }}
                </dd>
                <dt class="col-sm-3">Kişi</dt>
                <dd class="col-sm-9">{{ $reservation->people ?? '-' }}</dd>
                <dt class="col-sm-3">Oluşturma</dt>
                <dd class="col-sm-9">{{ optional($reservation->created_at)->format('d.m.Y H:i') }}</dd>
                @if(!empty($reservation->note))
                    <dt class="col-sm-3">Not</dt>
                    <dd class="col-sm-9">{{ $reservation->note }}</dd>
                @endif
                @if(!empty($reservation->ip) || !empty($reservation->user_agent))
                    <dt class="col-sm-3">Teknik</dt>
                    <dd class="col-sm-9">
                        IP: {{ $reservation->ip ?? '-' }}<br>
                        UA: {{ $reservation->user_agent ?? '-' }}
                    </dd>
                @endif
            </dl>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <form action="{{ route('admin.reservations.destroy', $reservation) }}" method="post"
                onsubmit="return confirm('Silinsin mi?')">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger"><i class="bi bi-trash"></i> Sil</button>
            </form>
        </div>
    </div>
@endsection