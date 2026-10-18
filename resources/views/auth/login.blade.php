@extends('layouts.app')

@section('title', 'Masuk')
@section('description', 'Masuk ke akun RentVentures untuk menyewa alat camping dan outdoor.')

@section('content')
<div class="auth-wrap">
    <div class="container">
        <div class="auth-card">
            <div class="auth-logo-wrap">
                <a href="{{ route('home') }}" class="rv-brand">
                    <img src="{{ asset('img/LogoRentVentures.png') }}" alt="Logo" style="width:48px;height:48px;object-fit:contain;">
                    RentVentures
                </a>
            </div>
            <div class="panel p-4 p-md-5">
                <h1 class="h2 mb-1">Masuk ke akunmu</h1>
                <p class="text-stone mb-4">Belum punya akun? <a href="{{ route('register') }}" class="fw-semibold">Daftar dulu</a>.</p>

                @include('partials.flash')

                <form method="POST" action="{{ route('login.store') }}" novalidate id="login-form">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0" style="border-radius:10px 0 0 10px;border-color:#bccabb"><i class="bi bi-envelope text-stone"></i></span>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                   class="form-control border-start-0 ps-1 @error('email') is-invalid @enderror"
                                   style="border-radius:0 10px 10px 0"
                                   autocomplete="email" autofocus required placeholder="nama@email.com">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Kata sandi</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0" style="border-radius:10px 0 0 10px;border-color:#bccabb"><i class="bi bi-lock text-stone"></i></span>
                            <input type="password" id="password" name="password"
                                   class="form-control border-start-0 ps-1 border-end-0 @error('password') is-invalid @enderror"
                                   style="border-radius:0"
                                   autocomplete="current-password" required placeholder="Minimal 8 karakter">
                            <button type="button" class="input-group-text bg-white border-start-0" style="border-radius:0 10px 10px 0;border-color:#bccabb;cursor:pointer" id="toggle-password" aria-label="Tampilkan kata sandi">
                                <i class="bi bi-eye text-stone" id="eye-icon"></i>
                            </button>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1">
                            <label class="form-check-label" for="remember">Ingat saya</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-pine btn-lg w-100" id="login-btn" data-busy="Masuk...">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                    </button>
                </form>

                <hr class="my-4">
                <p class="text-center text-stone small mb-0">
                    Akun demo: <strong>admin@rentventures.test</strong> / <strong>budi@rentventures.test</strong><br>
                    Password: <code>password</code>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('toggle-password').addEventListener('click', function () {
    const pwd = document.getElementById('password');
    const icon = document.getElementById('eye-icon');
    if (pwd.type === 'password') {
        pwd.type = 'text';
        icon.className = 'bi bi-eye-slash text-stone';
    } else {
        pwd.type = 'password';
        icon.className = 'bi bi-eye text-stone';
    }
});
</script>
@endpush
