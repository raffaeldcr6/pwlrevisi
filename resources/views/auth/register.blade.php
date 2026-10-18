@extends('layouts.app')

@section('title', 'Daftar Akun')
@section('description', 'Buat akun RentVentures secara gratis untuk mulai menyewa alat camping dan outdoor.')

@section('content')
<div class="auth-wrap">
    <div class="container">
        <div class="auth-card" style="max-width:520px">
            <div class="auth-logo-wrap">
                <a href="{{ route('home') }}" class="rv-brand">
                    <img src="{{ asset('img/LogoRentVentures.png') }}" alt="Logo" style="width:48px;height:48px;object-fit:contain;">
                    RentVentures
                </a>
            </div>
            <div class="panel p-4 p-md-5">
                <h1 class="h2 mb-1">Buat akun customer</h1>
                <p class="text-stone mb-4">Sudah punya akun? <a href="{{ route('login') }}" class="fw-semibold">Masuk di sini</a>.</p>

                @include('partials.flash')

                <form method="POST" action="{{ route('register.store') }}" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama lengkap</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0" style="border-radius:10px 0 0 10px;border-color:#bccabb"><i class="bi bi-person text-stone"></i></span>
                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                   class="form-control border-start-0 ps-1 @error('name') is-invalid @enderror"
                                   style="border-radius:0 10px 10px 0"
                                   autocomplete="name" autofocus required placeholder="Nama kamu">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0" style="border-radius:10px 0 0 10px;border-color:#bccabb"><i class="bi bi-envelope text-stone"></i></span>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                   class="form-control border-start-0 ps-1 @error('email') is-invalid @enderror"
                                   style="border-radius:0 10px 10px 0"
                                   autocomplete="email" required placeholder="nama@email.com">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Nomor WhatsApp <span class="text-stone fw-normal">(opsional)</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0" style="border-radius:10px 0 0 10px;border-color:#bccabb"><i class="bi bi-whatsapp text-stone"></i></span>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                                   class="form-control border-start-0 ps-1 @error('phone') is-invalid @enderror"
                                   style="border-radius:0 10px 10px 0"
                                   autocomplete="tel" placeholder="08xxxxxxxxxx">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <label for="password" class="form-label">Kata sandi</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0" style="border-radius:10px 0 0 10px;border-color:#bccabb"><i class="bi bi-lock text-stone"></i></span>
                                <input type="password" id="password" name="password"
                                       class="form-control border-start-0 ps-1 @error('password') is-invalid @enderror"
                                       style="border-radius:0 10px 10px 0"
                                       autocomplete="new-password" required placeholder="Min. 8 karakter">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @else
                                    <div class="form-text">Minimal 8 karakter.</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label for="password_confirmation" class="form-label">Ulangi kata sandi</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0" style="border-radius:10px 0 0 10px;border-color:#bccabb"><i class="bi bi-lock-fill text-stone"></i></span>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                       class="form-control border-start-0 ps-1"
                                       style="border-radius:0 10px 10px 0"
                                       autocomplete="new-password" required placeholder="Ulangi sandi">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-pine btn-lg w-100" data-busy="Membuat akun...">
                        <i class="bi bi-person-plus me-2"></i>Daftar Sekarang
                    </button>
                    <p class="text-center text-stone small mt-3 mb-0">
                        Dengan mendaftar, kamu menyetujui syarat & ketentuan penyewaan RentVentures.
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
