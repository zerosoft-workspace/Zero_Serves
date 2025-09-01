@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
            <h2 class="mb-0">Masalar</h2>

            <div class="d-flex gap-2 w-100 w-md-auto">
                {{-- Masa ekleme --}}
                <form action="{{ route('admin.tables.store') }}" method="POST" class="flex-grow-1 d-flex gap-2">
                    @csrf
                    <input type="text" name="name" class="form-control" placeholder="Masa Adı" required>
                    <button type="submit" class="btn btn-primary px-3">Masa Ekle</button>
                </form>

                {{-- EKLENDİ: Tüm QR’ları PDF indir --}}
                <form action="{{ route('admin.tables.qr.pdf') }}" method="POST" class="w-auto">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary px-3">
                        Tüm QR’ları PDF olarak indir
                    </button>
                </form>
            </div>
        </div>


        {{-- Flash messages --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- Masa kartları --}}
        <div class="row g-3">
            @forelse($tables as $table)
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column gap-3">
                            <div class="d-flex justify-content-between align-items-start gap-2">
                                <h5 class="card-title mb-0 text-truncate" title="{{ $table->name }}">{{ $table->name }}</h5>
                                @php
                                    $activeOrder = $table->active_order;
                                    $statusColor = 'success';
                                    $statusText = 'Boş';

                                    if ($activeOrder) {
                                        switch ($activeOrder->status) {
                                            case 'pending':
                                                $statusColor = 'warning';
                                                $statusText = 'Sipariş Bekliyor';
                                                break;
                                            case 'preparing':
                                                $statusColor = 'info';
                                                $statusText = 'Hazırlanıyor';
                                                break;
                                            case 'delivered':
                                                $statusColor = 'primary';
                                                $statusText = 'Teslim Edildi';
                                                break;
                                            default:
                                                $statusColor = 'secondary';
                                                $statusText = 'Dolu';
                                        }
                                    } else {
                                        // Masa durumuna göre renk belirle
                                        switch ($table->status) {
                                            case 'order_pending':
                                                $statusColor = 'warning';
                                                $statusText = 'Sipariş Var';
                                                break;
                                            case 'preparing':
                                                $statusColor = 'info';
                                                $statusText = 'Hazırlanıyor';
                                                break;
                                            case 'delivered':
                                                $statusColor = 'primary';
                                                $statusText = 'Teslim Edildi';
                                                break;
                                            case 'paid':
                                                $statusColor = 'success';
                                                $statusText = 'Boş';
                                                break;
                                            default:
                                                $statusColor = 'success';
                                                $statusText = 'Boş';
                                        }
                                    }
                                @endphp
                                <span class="badge text-bg-{{ $statusColor }}">
                                    {{ $statusText }}
                                </span>
                            </div>

                            {{-- Garson Atama --}}
                            <div class="mb-3">
                                <label class="form-label small text-muted">Atanan Garson</label>
                                <select class="form-select form-select-sm waiter-select" data-table-id="{{ $table->id }}">
                                    <option value="">Garson Seçin</option>
                                    @foreach($waiters as $waiter)
                                        <option value="{{ $waiter->id }}" {{ $table->waiter_id == $waiter->id ? 'selected' : '' }}>
                                            {{ $waiter->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($table->waiter)
                                    <small class="text-muted">{{ $table->waiter->email }}</small>
                                @endif
                            </div>

                            {{-- QR: responsive SVG --}}
                            <div class="qr-wrap text-center">
                                {!! QrCode::size(512)->format('svg')->generate(\App\Helpers\NetworkHelper::getTableQrUrl($table->token)) !!}
                            </div>

                            {{-- Token --}}
                            <div class="small">
                                <div class="text-muted mb-1">Token</div>
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <a href="{{ \App\Helpers\NetworkHelper::getTableQrUrl($table->token) }}" target="_blank"
                                        class="text-decoration-none text-break">{{ $table->token }}</a>
                                </div>
                            </div>

                            {{-- Aksiyonlar --}}
                            <div class="mt-auto">
                                <div class="d-grid gap-2 d-md-flex">
                                    {{-- EKLENDİ: Tek masa için QR PDF --}}
                                    <a href="{{ route('admin.tables.qr.single', $table->id) }}"
                                        class="btn btn-secondary w-100 w-md-auto">
                                        İndir
                                    </a>

                                    {{-- Kopyala --}}
                                    <button type="button" class="btn btn-info w-100 w-md-auto"
                                        data-copy="{{ \App\Helpers\NetworkHelper::getTableQrUrl($table->token) }}">URL
                                        Kopyala</button>

                                    {{-- Temizle --}}
                                    <form action="{{ route('admin.tables.clear', $table->id) }}" method="POST"
                                        onsubmit="return confirm('Masayı tamamen temizlemek istiyor musunuz?')">
                                        @csrf
                                        <button type="submit" class="btn btn-warning w-100 w-md-auto">Temizle</button>
                                    </form>

                                    {{-- Sil --}}
                                    <form method="POST" action="{{ route('admin.tables.destroy', $table->id) }}"
                                        onsubmit="return confirm('Bu masayı silmek istediğinize emin misiniz?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger w-100 w-md-auto">Sil</button>
                                    </form>
                                </div>
                            </div>


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

    {{-- Sayfaya özel ufak CSS ve JS --}}
    <style>
        /* Kart içi boşluklar ve tipografi mobilde daha rahat */
        .card-body {
            padding: 1rem;
        }

        .qr-wrap svg {
            width: 100%;
            height: auto;
            max-width: 260px;
        }

        .qr-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @media (min-width: 1200px) {
            .qr-wrap svg {
                max-width: 220px;
            }
        }
    </style>

    <script>
        // Token kopyalama
        document.addEventListener('click', async (e) => {
            const btn = e.target.closest('button[data-copy]');
            if (!btn) return;
            try {
                await navigator.clipboard.writeText(btn.getAttribute('data-copy') || '');
                const original = btn.textContent.trim();
                btn.textContent = 'Kopyalandı ✓';
                setTimeout(() => btn.textContent = original, 1200);
            } catch (err) {
                alert('Kopyalama desteklenmiyor.');
            }
        });

        // Garson atama
        document.addEventListener('change', async (e) => {
            if (!e.target.classList.contains('waiter-select')) return;

            const select = e.target;
            const tableId = select.dataset.tableId;
            const waiterId = select.value || null;

            try {
                const response = await fetch('/admin/tables/assign-waiter', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        table_id: tableId,
                        waiter_id: waiterId
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Başarı mesajı göster
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success alert-dismissible fade show position-fixed';
                    alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                    alert.innerHTML = `
                                    ${data.message}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                `;
                    document.body.appendChild(alert);

                    // 3 saniye sonra otomatik kapat
                    setTimeout(() => {
                        if (alert.parentNode) {
                            alert.remove();
                        }
                    }, 3000);
                } else {
                    alert('Hata: ' + data.message);
                    // Seçimi eski haline döndür
                    select.selectedIndex = 0;
                }
            } catch (error) {
                console.error('Garson atama hatası:', error);
                alert('Bir hata oluştu. Lütfen tekrar deneyin.');
                select.selectedIndex = 0;
            }
        });
    </script>
@endsection