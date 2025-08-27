@extends('layouts.waiter')

@section('title', 'Çağrı Yönetimi')

@section('header_actions')
    <div class="d-flex gap-2">
        <a href="{{ route('waiter.dashboard') }}" class="btn btn-sm btn-ghost">
            <i class="bi bi-arrow-left me-1"></i> 
            <span class="d-none d-sm-inline">Masalar</span>
        </a>
        <button onclick="location.reload()" class="btn btn-sm btn-ghost">
            <i class="bi bi-arrow-clockwise me-1"></i>
            <span class="d-none d-sm-inline">Yenile</span>
        </button>
    </div>
@endsection

@section('content')
    {{-- Sayfa Başlığı --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="p-3 bg-warning bg-gradient rounded-circle text-white">
                <i class="bi bi-bell fs-4"></i>
            </div>
            <div>
                <h2 class="mb-0 fw-bold">Garson Çağrıları</h2>
                <p class="text-muted mb-0">Müşteri çağrılarını yönetin</p>
            </div>
        </div>
    </div>

    {{-- Hızlı İstatistikler --}}
    <div class="quick-stats mb-4">
        <div class="stat-card">
            <div class="stat-number text-danger">{{ $calls->where('status', 'new')->count() }}</div>
            <div class="stat-label">Yeni Çağrı</div>
        </div>
        <div class="stat-card">
            <div class="stat-number text-success">{{ $calls->where('status', 'completed')->count() }}</div>
            <div class="stat-label">Tamamlandı</div>
        </div>
        <div class="stat-card">
            <div class="stat-number text-primary">{{ $calls->count() }}</div>
            <div class="stat-label">Toplam Çağrı</div>
        </div>
    </div>

    @if($calls->count() > 0)
        <div class="row g-3">
            @foreach($calls as $call)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="call-card call-{{ $call->status }} h-100" data-id="{{ $call->id }}">
                        
                        <div class="card-header border-0 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                @switch($call->status)
                                    @case('new')
                                        <div class="p-2 bg-danger bg-opacity-10 rounded">
                                            <i class="bi bi-bell text-danger"></i>
                                        </div>
                                        @break
                                    @case('responded')
                                        <div class="p-2 bg-warning bg-opacity-10 rounded">
                                            <i class="bi bi-person-check text-warning"></i>
                                        </div>
                                        @break
                                    @case('completed')
                                        <div class="p-2 bg-success bg-opacity-10 rounded">
                                            <i class="bi bi-check-circle text-success"></i>
                                        </div>
                                        @break
                                @endswitch
                                <h6 class="mb-0 fw-semibold">{{ $call->table->name }}</h6>
                            </div>
                            @switch($call->status)
                                @case('new')
                                    <span class="badge bg-danger">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        Yeni Çağrı
                                    </span>
                                    @break
                                @case('responded')
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-hourglass-split me-1"></i>
                                        Yanıtlandı
                                    </span>
                                    @break
                                @case('completed')
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Tamamlandı
                                    </span>
                                    @break
                            @endswitch
                        </div>

                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="bi bi-clock text-muted"></i>
                                    <span class="fw-semibold">{{ $call->created_at->format('H:i') }}</span>
                                    <span class="text-muted">•</span>
                                    <span class="text-muted small">{{ $call->created_at->diffForHumans() }}</span>
                                </div>
                                
                                @if($call->responded_at)
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <i class="bi bi-person-check text-warning"></i>
                                        <span class="small text-warning">
                                            {{ $call->responded_at->format('H:i') }}'de yanıtlandı
                                        </span>
                                    </div>
                                @endif
                                
                                @if($call->completed_at)
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                        <span class="small text-success">
                                            {{ $call->completed_at->format('H:i') }}'de tamamlandı
                                        </span>
                                    </div>
                                @endif
                            </div>

                            @if($call->status === 'new' || $call->status === 'responded')
                                <div class="d-grid gap-2">
                                    <button class="btn btn-success" 
                                        onclick="completeCall({{ $call->id }})">
                                        <i class="bi bi-check-circle me-1"></i>
                                        <span>Tamamla</span>
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" 
                                        onclick="deleteCall({{ $call->id }})">
                                        <i class="bi bi-trash me-1"></i>
                                        <span>Sil</span>
                                    </button>
                                </div>
                            @else
                                <div class="d-grid">
                                    <div class="text-center py-3 mb-2">
                                        <div class="p-3 bg-success bg-opacity-10 rounded-circle d-inline-flex mb-2">
                                            <i class="bi bi-check-circle-fill text-success fs-4"></i>
                                        </div>
                                        <div class="fw-semibold text-success">Tamamlandı</div>
                                        <small class="text-muted">Çağrı başarıyla çözüldü</small>
                                    </div>
                                    <button class="btn btn-outline-danger btn-sm" 
                                        onclick="deleteCall({{ $call->id }})">
                                        <i class="bi bi-trash me-1"></i>
                                        <span>Sil</span>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $calls->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <div class="p-4 bg-light rounded-circle d-inline-flex mb-3">
                <i class="bi bi-bell-slash display-4 text-muted"></i>
            </div>
            <h5 class="text-muted mb-2">Henüz Çağrı Yok</h5>
            <p class="text-muted">Müşterilerden henüz garson çağrısı gelmemiş.</p>
        </div>
    @endif
@endsection

@push('scripts')
<script>
    function completeCall(callId){
        return respondToCall(callId, 'complete');
    }

    async function respondToCall(callId, action) {
        try {
            const response = await fetch(`/waiter/calls/${callId}/respond`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ action })
            });

            const data = await response.json();

            if (data.success) {
                const card = document.querySelector(`.call-card[data-id="${callId}"]`);
                if (!card) return;

                // Tamamlandı güncellemesi
                if (data.call?.status === 'completed' || action === 'complete') {
                    card.classList.remove('call-new', 'call-responded');
                    card.classList.add('call-completed');

                    const badge = card.querySelector('.badge');
                    if (badge) {
                        badge.className = 'badge bg-success';
                        badge.innerHTML = `<i class="bi bi-check-circle me-1"></i> Tamamlandı`;
                    }

                    const body = card.querySelector('.card-body');
                    if (body) {
                        body.innerHTML = `
                            <div class="text-center py-3 mb-2">
                                <div class="p-3 bg-success bg-opacity-10 rounded-circle d-inline-flex mb-2">
                                    <i class="bi bi-check-circle-fill text-success fs-4"></i>
                                </div>
                                <div class="fw-semibold text-success">Tamamlandı</div>
                                <small class="text-muted">Çağrı başarıyla çözüldü</small>
                            </div>
                            <button class="btn btn-outline-danger btn-sm" onclick="deleteCall(${callId})">
                                <i class="bi bi-trash me-1"></i>
                                <span>Sil</span>
                            </button>
                        `;
                    }
                }

                // Hızlı istatistikleri güncelle
                if (data.stats) {
                    document.querySelector(".quick-stats .text-danger").innerText = data.stats.new;
                    document.querySelector(".quick-stats .text-success").innerText = data.stats.completed;
                    document.querySelector(".quick-stats .text-primary").innerText = data.stats.total;
                }

            } else {
                alert('Hata: ' + (data.message || 'Bir hata oluştu'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Bir hata oluştu');
        }
    }

    // Otomatik yenileme (30 sn) – istersen kaldır
    setInterval(() => {
        window.location.reload();
    }, 30000);
</script>
@endpush
