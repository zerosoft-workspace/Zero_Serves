@extends('layouts.waiter')

@section('title', 'Garson Çağrıları')

@section('header_actions')
    <a href="{{ route('waiter.dashboard') }}" class="btn btn-sm btn-ghost">
        <i class="bi bi-arrow-left me-1"></i> Dashboard
    </a>
@endsection

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="mb-0">Garson Çağrıları</h2>
        <div class="d-flex gap-2">
            <span class="badge bg-danger">{{ $calls->where('status', 'new')->count() }} Yeni</span>
            <span class="badge bg-warning text-dark">{{ $calls->where('status', 'responded')->count() }} Yanıtlandı</span>
            <span class="badge bg-success">{{ $calls->where('status', 'completed')->count() }} Tamamlandı</span>
        </div>
    </div>

    @if($calls->count() > 0)
        <div class="row g-3">
            @foreach($calls as $call)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card shadow-sm h-100 
                        @if($call->status === 'new') border-danger 
                        @elseif($call->status === 'responded') border-warning 
                        @else border-success @endif">
                        
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">{{ $call->table->name }}</h6>
                            @switch($call->status)
                                @case('new')
                                    <span class="badge bg-danger">Yeni Çağrı</span>
                                    @break
                                @case('responded')
                                    <span class="badge bg-warning text-dark">Yanıtlandı</span>
                                    @break
                                @case('completed')
                                    <span class="badge bg-success">Tamamlandı</span>
                                    @break
                            @endswitch
                        </div>

                        <div class="card-body">
                            <div class="small text-muted mb-3">
                                <div><i class="bi bi-clock me-1"></i> {{ $call->created_at->format('H:i') }}</div>
                                <div><i class="bi bi-calendar me-1"></i> {{ $call->created_at->diffForHumans() }}</div>
                                
                                @if($call->responded_at)
                                    <div class="mt-2 text-warning">
                                        <i class="bi bi-check-circle me-1"></i> 
                                        {{ $call->responded_at->format('H:i') }}'de yanıtlandı
                                    </div>
                                @endif
                                
                                @if($call->completed_at)
                                    <div class="mt-1 text-success">
                                        <i class="bi bi-check-circle-fill me-1"></i> 
                                        {{ $call->completed_at->format('H:i') }}'de tamamlandı
                                    </div>
                                @endif
                            </div>

                            @if($call->status === 'new')
                                <div class="d-grid gap-2">
                                    <button class="btn btn-warning btn-sm" 
                                        onclick="respondToCall({{ $call->id }}, 'respond')">
                                        <i class="bi bi-person-check me-1"></i> Yanıtla
                                    </button>
                                    <button class="btn btn-success btn-sm" 
                                        onclick="respondToCall({{ $call->id }}, 'complete')">
                                        <i class="bi bi-check-circle me-1"></i> Tamamla
                                    </button>
                                </div>
                            @elseif($call->status === 'responded')
                                <div class="d-grid">
                                    <button class="btn btn-success btn-sm" 
                                        onclick="respondToCall({{ $call->id }}, 'complete')">
                                        <i class="bi bi-check-circle me-1"></i> Tamamla
                                    </button>
                                </div>
                            @else
                                <div class="text-center text-success">
                                    <i class="bi bi-check-circle-fill fs-4"></i>
                                    <div class="small">Tamamlandı</div>
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
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            Henüz garson çağrısı bulunmuyor.
        </div>
    @endif
@endsection

@push('scripts')
<script>
    async function respondToCall(callId, action) {
        try {
            const response = await fetch(`/waiter/calls/${callId}/respond`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ action: action })
            });

            const data = await response.json();

            if (data.success) {
                // Sayfayı yenile
                window.location.reload();
            } else {
                alert('Hata: ' + (data.message || 'Bir hata oluştu'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Bir hata oluştu');
        }
    }

    // Otomatik yenileme (30 saniyede bir)
    setInterval(() => {
        window.location.reload();
    }, 30000);
</script>
@endpush
