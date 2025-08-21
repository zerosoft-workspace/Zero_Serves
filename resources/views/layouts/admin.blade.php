{{-- resources/views/layouts/admin.blade.php --}}
<!doctype html>
<html lang="tr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') | Restoran Yönetimi</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Admin CSS -->
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">

    @stack('styles')
</head>
@stack('scripts')

<body>
    <div class="admin-container">
        {{-- Navbar --}}
        @include('admin.partials.navbar')

        {{-- Sidebar --}}
        @include('admin.partials.sidebar')

        {{-- Sidebar Overlay for Mobile --}}
        <div class="sidebar-overlay"></div>

        {{-- Main Content --}}
        <main class="admin-main">
            {{-- Breadcrumb --}}
            @if(isset($breadcrumbs) || View::hasSection('breadcrumb'))
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-house-door"></i> Ana Sayfa
                            </a>
                        </li>
                        @if(isset($breadcrumbs))
                            @foreach($breadcrumbs as $breadcrumb)
                                @if($loop->last)
                                    <li class="breadcrumb-item active" aria-current="page">
                                        {{ $breadcrumb['title'] }}
                                    </li>
                                @else
                                    <li class="breadcrumb-item">
                                        <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a>
                                    </li>
                                @endif
                            @endforeach
                        @endif
                        @yield('breadcrumb')
                    </ol>
                </nav>
            @endif

            {{-- Page Header --}}
            @if(View::hasSection('page-header') || isset($pageTitle))
                <div class="page-header mb-4">
                    @if(isset($pageTitle))
                        <h1 class="page-title">{{ $pageTitle }}</h1>
                    @endif
                    @yield('page-header')
                </div>
            @endif

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Main Content --}}
            <div class="content-wrapper">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Admin JS -->
    <script src="{{ asset('js/admin.js') }}"></script>

    @stack('scripts')
</body>

</html>