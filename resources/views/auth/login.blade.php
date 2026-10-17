@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<div class="auth-wrap">
    <div class="container">
        <div class="auth-card">
            <div class="panel p-4 p-md-5">
                <h1 class="h2 mb-1">Masuk ke akunmu</h1>
                <p class="text-stone mb-4">Belum punya akun? <a href="{{ route('register') }}">Daftar dulu</a>.</p>

                @include('partials.flash')

                <form method="POST" action="{{ route('login.store') }}" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" autocomplete="email" autofocus required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Kata sandi</label>
                        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="current-password" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" value="1">
                        <label class="form-check-label" for="remember">Ingat saya di perangkat ini</label>
                    </div>
                    <button type="submit" class="btn btn-pine btn-lg w-100" data-busy="Memproses...">Masuk</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
