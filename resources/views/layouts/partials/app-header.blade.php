@php
    $user = auth()->user();
    $role = $user->role ?? null;   // 'admin' | 'waiter' | null
    $isAdm = $role === 'admin';
    $isWait = $role === 'waiter';

    $brandHref = $isWait ? route('waiter.dashboard')
        : ($isAdm ? route('admin.dashboard') : url('/'));

    // Guest iken başlık URL’e göre
    if (!$role) {
        $brandText = request()->is('waiter/*') ? 'Garson Paneli'
            : (request()->is('admin/*') ? 'Admin Paneli' : 'Panel');
    } else {
        $brandText = $isWait ? 'Garson Paneli' : 'Admin Paneli';
    }
@endphp

<nav class="app-navbar">
    <div class="app-nav-inner container-fluid">
        <div class="app-brand-section">
            {{-- Mobile Menu Toggle - Sadece admin panelinde göster --}}
            @if($isAdm)
                <button class="mobile-menu-toggle d-md-none" id="mobileMenuToggle" aria-label="Menüyü Aç/Kapat">
                    <i class="bi bi-list"></i>
                </button>
            @endif
            
            <a href="{{ $brandHref }}" class="app-brand">
                <i class="bi bi-egg-fried"></i>
                <span>{{ $brandText }}</span>
            </a>
        </div>

        <div class="app-actions">
            @auth
                {{-- Giriş yapınca: Yenile --}}
                <button type="button" class="btn btn-sm btn-ghost" onclick="location.reload()">
                    <i class="bi bi-arrow-clockwise me-1"></i> Yenile
                </button>
            @else
                {{-- Guest: Ana Sayfa --}}
                <a href="{{ url('/') }}" class="btn btn-sm btn-ghost">
                    <i class="bi bi-house me-1"></i> Ana Sayfa
                </a>
            @endauth

            @auth
                <div class="dropdown">
                    <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" id="appUserMenu"
                        role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="avatar-chip me-2">
                            {{ strtoupper(Str::of($user->name ?? 'U')->substr(0, 1)) }}
                        </span>
                        <span class="d-none d-sm-inline text-white-50">{{ $user->name }}</span>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="appUserMenu">
                        @if($isAdm)
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a href="{{ route('admin.logout') }}" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i> Çıkış Yap
                                </a>
                            </li>
                        @elseif($isWait)
                            <li>
                                <a class="dropdown-item" href="{{ route('waiter.dashboard') }}">
                                    <i class="bi bi-table me-2"></i> Masa Yönetimi
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item position-relative" href="{{ route('waiter.calls') }}">
                                    <i class="bi bi-bell me-2"></i> Çağrı Yönetimi
                                    @php
                                        $activeCalls = \App\Models\WaiterCall::where('status', 'new')->count();
                                    @endphp
                                    @if($activeCalls > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            {{ $activeCalls }}
                                        </span>
                                    @endif
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item text-muted" href="#" onclick="return false;">
                                    <i class="bi bi-person-badge me-2"></i> {{ $user->name }}
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a href="{{ route('waiter.logout') }}" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i> Çıkış Yap
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            @endauth
        </div>
    </div>
</nav>