<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'RentVentures') | RentVentures</title>
    <meta name="description" content="@yield('description', 'Sewa tenda, carrier, sleeping bag, dan perlengkapan camping lainnya. Pilih alat, ajukan sewa, ambil di basecamp.')">
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/favicon.svg') }}">
    @include('partials.head-assets')
    @stack('styles')
</head>
<body>
@php($user = auth()->user())
<header class="rv-nav">
    <nav class="navbar navbar-expand-lg py-2">
        <div class="container">
            <a href="{{ route('home') }}" class="rv-brand">
                <img src="{{ asset('img/LogoRentVentures.png') }}" alt="Logo RentVentures" style="width:38px;height:38px;object-fit:contain;">
                RentVentures
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Buka menu">
                <i class="bi bi-list fs-2"></i>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav mx-lg-auto gap-lg-1">
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('catalog.*') ? 'active' : '' }}" href="{{ route('catalog.index') }}">Katalog</a></li>
                    @if ($user && ! $user->isAdmin())
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('customer.rentals.*') ? 'active' : '' }}" href="{{ route('customer.rentals.index') }}">Penyewaan Saya</a></li>
                    @endif
                    @if ($user && $user->isAdmin())
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard Admin</a></li>
                    @endif
                </ul>
                <div class="d-flex align-items-center gap-2 mt-3 mt-lg-0">
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-outline-pine btn-sm">Masuk</a>
                        <a href="{{ route('register') }}" class="btn btn-pine btn-sm">Daftar</a>
                    @else
                        @unless ($user->isAdmin())
                            <a href="{{ route('customer.rentals.create') }}" class="btn btn-lantern btn-sm d-none d-lg-inline-flex align-items-center gap-1">
                                <i class="bi bi-plus-lg"></i>Sewa Alat
                            </a>
                            {{-- Notification Bell --}}
                            <a href="{{ route('customer.notifications.index') }}" class="notif-btn" aria-label="Notifikasi"
                               id="notif-bell"
                               data-url="{{ route('customer.notifications.count') }}">
                                <i class="bi bi-bell"></i>
                                @php($unread = $user->unreadNotificationsCount())
                                <span class="notif-count" id="notif-count"
                                      data-url="{{ route('customer.notifications.count') }}"
                                      style="{{ $unread > 0 ? '' : 'display:none' }}">{{ $unread > 99 ? '99+' : $unread }}</span>
                            </a>
                        @endunless
                        <div class="dropdown">
                            <button class="btn p-0 border-0 d-flex align-items-center gap-2" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Menu akun">
                                <span class="avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                <span class="d-none d-lg-inline fw-semibold small">{{ $user->first_name }}</span>
                                <i class="bi bi-chevron-down small d-none d-lg-inline"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="border-radius:14px; overflow:hidden; min-width:210px">
                                <li>
                                    <span class="dropdown-item-text small text-stone py-2">
                                        <span class="d-block fw-semibold text-pine">{{ $user->name }}</span>
                                        {{ $user->email }}<br>
                                        <span class="rv-badge st-{{ $user->isAdmin() ? 'rented' : 'approved' }} mt-1">{{ $user->isAdmin() ? 'Admin' : 'Customer' }}</span>
                                    </span>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                @unless($user->isAdmin())
                                <li><a class="dropdown-item py-2" href="{{ route('customer.profile') }}"><i class="bi bi-person-circle me-2"></i>Profil saya</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('customer.rentals.index') }}"><i class="bi bi-calendar-check me-2"></i>Penyewaan saya</a></li>
                                <li><hr class="dropdown-divider my-1"></li>
                                @endunless
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button class="dropdown-item py-2 text-danger" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Keluar</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </nav>
</header>

<main>
    @if (session('success') || session('error'))
        <div class="container pt-4">@include('partials.flash')</div>
    @endif
    @yield('content')
</main>

<footer class="footer mt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-5">
                <a href="{{ route('home') }}" class="rv-brand text-white mb-3">
                    <img src="{{ asset('img/LogoRentVentures.png') }}" alt="Logo" style="width:38px;height:38px;object-fit:contain;filter:brightness(0) invert(1);">
                    RentVentures
                </a>
                <p class="mb-0 mt-3" style="max-width: 40ch">Penyewaan alat camping dan outdoor. Tenda, carrier, sleeping bag, sampai kompor lapangan, siap diambil di basecamp.</p>
                <div class="d-flex gap-3 mt-3">
                    <a href="#" aria-label="Instagram" style="color:#BFD3C4;font-size:1.2rem"><i class="bi bi-instagram"></i></a>
                    <a href="#" aria-label="WhatsApp" style="color:#BFD3C4;font-size:1.2rem"><i class="bi bi-whatsapp"></i></a>
                    <a href="#" aria-label="Facebook" style="color:#BFD3C4;font-size:1.2rem"><i class="bi bi-facebook"></i></a>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <h4>Jelajahi</h4>
                <ul class="list-unstyled mb-0">
                    <li><a href="{{ route('catalog.index') }}">Katalog alat</a></li>
                    <li><a href="{{ route('home') }}#cara-sewa">Cara menyewa</a></li>
                    <li><a href="{{ route('register') }}">Buat akun</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-4">
                <h4>Basecamp</h4>
                <ul class="list-unstyled mb-0">
                    <li><i class="bi bi-geo-alt me-1"></i>Bandar Lampung</li>
                    <li><i class="bi bi-clock me-1"></i>Buka setiap hari, 08.00–20.00</li>
                    <li><a href="https://wa.me/6281200000001"><i class="bi bi-whatsapp me-1"></i>0812-0000-0001</a></li>
                </ul>
            </div>
        </div>
        <hr class="border-light opacity-25 my-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <small>&copy; {{ date('Y') }} RentVentures. Sistem Penyewaan Alat Camping &amp; Outdoor.</small>
            <small><a href="{{ route('catalog.index') }}" class="me-3">Katalog</a><a href="{{ route('home') }}">Beranda</a></small>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
