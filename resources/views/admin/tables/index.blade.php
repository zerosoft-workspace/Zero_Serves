@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
            <h2 class="mb-0">Masalar</h2>

            {{-- Masa ekleme --}}
            <form action="{{ route('admin.tables.store') }}" method="POST" class="w-100 w-md-auto d-flex gap-2">
                @csrf
                <input type="text" name="name" class="form-control" placeholder="Masa Adı" required>
                <button type="submit" class="btn btn-primary px-3">Masa Ekle</button>
            </form>
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
                                @if(!empty($table->status))
                                    <span
                                        class="badge text-bg-{{ $table->status === 'empty' ? 'success' : ($table->status === 'reserved' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst($table->status) }}
                                    </span>
                                @endif
                            </div>

                            {{-- QR: responsive SVG --}}
                            <div class="qr-wrap text-center">
                                {!! QrCode::size(512)->format('svg')->generate(route('customer.table.token', $table->token)) !!}
                            </div>

                            {{-- Token --}}
                            <div class="small">
                                <div class="text-muted mb-1">Token</div>
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <a href="{{ route('customer.table.token', $table->token) }}" target="_blank"
                                        class="text-decoration-none text-break">{{ $table->token }}</a>
                                </div>
                            </div>

                            {{-- Aksiyonlar --}}
                            <div class="mt-auto">
                                <div class="d-grid gap-2 d-md-flex">
                                    {{-- Kopyala --}}
                                    <button type="button" class="btn btn-info w-100 w-md-auto"
                                        data-copy="{{ $table->token }}">Kopyala</button>

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
    </script>
@endsection