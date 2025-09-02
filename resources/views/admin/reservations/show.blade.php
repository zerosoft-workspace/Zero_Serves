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
                <dt class="col-sm-3">Durum</dt>
                <dd class="col-sm-9">{!! $reservation->status_badge !!}</dd>
                <dt class="col-sm-3">Oluşturma</dt>
                <dd class="col-sm-9">{{ optional($reservation->created_at)->format('d.m.Y H:i') }}</dd>
                @if($reservation->status_updated_at)
                <dt class="col-sm-3">Durum Güncelleme</dt>
                <dd class="col-sm-9">
                    {{ $reservation->status_updated_at->format('d.m.Y H:i') }}
                    @if($reservation->statusUpdatedBy)
                        - {{ $reservation->statusUpdatedBy->name }}
                    @endif
                </dd>
                @endif
                @if($reservation->admin_note)
                <dt class="col-sm-3">Admin Notu</dt>
                <dd class="col-sm-9">{{ $reservation->admin_note }}</dd>
                @endif
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
        <div class="card-footer d-flex justify-content-between">
            <div>
                @if($reservation->status === 'pending')
                    <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#approveModal">
                        <i class="bi bi-check-circle"></i> Onayla & Mail Gönder
                    </button>
                    <button type="button" class="btn btn-danger me-2" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="bi bi-x-circle"></i> Reddet & Mail Gönder
                    </button>
                @elseif($reservation->status === 'approved')
                    <span class="text-success"><i class="bi bi-check-circle"></i> Bu rezervasyon onaylanmış</span>
                @elseif($reservation->status === 'rejected')
                    <span class="text-danger"><i class="bi bi-x-circle"></i> Bu rezervasyon reddedilmiş</span>
                @endif
            </div>
            <div>
                <form action="{{ route('admin.reservations.destroy', $reservation) }}" method="post"
                    onsubmit="return confirm('Silinsin mi?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger"><i class="bi bi-trash"></i> Sil</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Onaylama Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.reservations.approve', $reservation) }}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Rezervasyonu Onayla</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>{{ $reservation->name }}</strong> adlı müşterinin rezervasyonunu onaylayıp e-posta göndermek istediğinizden emin misiniz?</p>
                        
                        <div class="mb-3">
                            <label class="form-label">Müşteriye Not (Opsiyonel)</label>
                            <textarea name="admin_note" class="form-control" rows="3" 
                                placeholder="Örn: Masanız hazır olacaktır. Lütfen 15 dakika önce gelin."></textarea>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Müşteriye rezervasyon onay e-postası gönderilecektir.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Onayla & Mail Gönder
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Reddetme Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.reservations.reject', $reservation) }}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Rezervasyonu Reddet</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>{{ $reservation->name }}</strong> adlı müşterinin rezervasyonunu reddetmek istediğinizden emin misiniz?</p>
                        
                        <div class="mb-3">
                            <label class="form-label">Reddetme Sebebi (Opsiyonel)</label>
                            <textarea name="admin_note" class="form-control" rows="3" 
                                placeholder="Örn: Belirtilen tarih ve saatte müsaitlik bulunmamaktadır. Farklı bir tarih önerebiliriz."></textarea>
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> Müşteriye rezervasyon red e-postası gönderilecektir.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-x-circle"></i> Reddet & Mail Gönder
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection