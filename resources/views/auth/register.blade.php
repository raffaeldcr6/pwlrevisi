@extends('layouts.app')

@section('title', 'Daftar')

@section('content')
<div class="auth-wrap">
    <div class="container">
        <div class="auth-card">
            <div class="panel p-4 p-md-5">
                <h1 class="h2 mb-1">Buat akun customer</h1>
                <p class="text-stone mb-4">Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>.</p>

                @include('partials.flash')

                <form method="POST" action="{{ route('register.store') }}" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama lengkap</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" autocomplete="name" autofocus required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" autocomplete="email" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Nomor WhatsApp <span class="text-stone fw-normal">(opsional)</span></label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" class="form-control @error('phone') is-invalid @enderror" autocomplete="tel">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <label for="password" class="form-label">Kata sandi</label>
                            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password" required>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@else<div class="form-text">Minimal 8 karakter.</div>@enderror
                        </div>
                        <div class="col-sm-6">
                            <label for="password_confirmation" class="form-label">Ulangi kata sandi</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" autocomplete="new-password" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-pine btn-lg w-100" data-busy="Membuat akun...">Daftar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
