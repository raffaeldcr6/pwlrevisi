<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') | RentVentures Admin</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/favicon.svg') }}">
    @include('partials.head-assets')
    @stack('styles')
</head>
<body>
@php
    $nav = [
        ['admin.dashboard', 'bi-speedometer2', 'Dashboard', 'admin.dashboard'],
        ['admin.rentals.index', 'bi-calendar-check', 'Penyewaan', 'admin.rentals.*'],
        ['admin.equipment.index', 'bi-box-seam', 'Data Alat', 'admin.equipment.*'],
        ['admin.categories.index', 'bi-tags', 'Kategori', 'admin.categories.*'],
    ];
    $pending = \App\Models\Rental::where('status', 'pending')->count();
@endphp

<div class="admin-mobilebar">
    <a href="{{ route('admin.dashboard') }}" class="rv-brand">@include('partials.logo') Admin</a>
    <button class="btn btn-sm btn-lantern" data-bs-toggle="offcanvas" data-bs-target="#adminMenu" aria-label="Buka menu"><i class="bi bi-list"></i> Menu</button>
</div>

<div class="offcanvas offcanvas-start text-bg-dark" tabindex="-1" id="adminMenu" style="--bs-offcanvas-bg: var(--rv-pine); width: 270px">
    <div class="offcanvas-header"><span class="rv-brand text-white">@include('partials.logo') RentVentures</span><button class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Tutup"></button></div>
    <div class="offcanvas-body p-3">
        <nav class="sidebar" style="height:auto; position:static; padding:0">
            @foreach ($nav as [$route, $icon, $label, $pattern])
                <a href="{{ route($route) }}" class="{{ request()->routeIs($pattern) ? 'active' : '' }}"><i class="bi {{ $icon }}"></i>{{ $label }}</a>
            @endforeach
            <form action="{{ route('logout') }}" method="POST" class="mt-3">
                @csrf
                <button class="btn btn-sm w-100 text-start" style="color:#CFE0D3; background: rgba(255,255,255,.08)" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Keluar</button>
            </form>
        </nav>
    </div>
</div>

<div class="admin-shell">
    <aside class="sidebar">
        <a href="{{ route('admin.dashboard') }}" class="rv-brand">@include('partials.logo') RentVentures</a>
        <nav>
            @foreach ($nav as [$route, $icon, $label, $pattern])
                <a href="{{ route($route) }}" class="{{ request()->routeIs($pattern) ? 'active' : '' }}">
                    <i class="bi {{ $icon }}"></i>{{ $label }}
                    @if ($route === 'admin.rentals.index' && $pending > 0)
                        <span class="badge rounded-pill ms-auto" style="background: var(--rv-lantern); color: var(--rv-pine)">{{ $pending }}</span>
                    @endif
                </a>
            @endforeach
            <a href="{{ route('home') }}" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i>Lihat website</a>
        </nav>
        <div class="side-foot">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="avatar" style="background: var(--rv-lantern); color: var(--rv-pine)">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                <div class="lh-sm"><div class="fw-semibold text-white">{{ auth()->user()->first_name }}</div><small>Administrator</small></div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-sm w-100 text-start" style="color:#CFE0D3; background: rgba(255,255,255,.08)" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Keluar</button>
            </form>
        </div>
    </aside>

    <div class="admin-main">
        <div class="admin-top">
            <div>
                <h1>@yield('heading')</h1>
                @hasSection('subheading')<p class="text-stone mb-0 mt-1">@yield('subheading')</p>@endif
            </div>
            <div>@yield('actions')</div>
        </div>
        @include('partials.flash')
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
