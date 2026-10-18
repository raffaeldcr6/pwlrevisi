@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="container py-5">
    <div class="mb-4">
        <h1 class="display-font" style="font-size: clamp(2rem, 4vw, 2.6rem)">Profil saya</h1>
        <p class="text-stone mb-0">Kelola informasi akun dan kata sandimu.</p>
    </div>

    <div class="row g-4">
        {{-- Informasi akun --}}
        <div class="col-lg-7">
            <div class="panel">
                <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
                    <div style="width:56px;height:56px;border-radius:50%;background:var(--rv-pine);color:#fff;display:grid;place-items:center;font-size:1.5rem;font-weight:700;font-family:'Bricolage Grotesque',sans-serif;flex:none">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="fw-bold" style="font-size:1.1rem">{{ $user->name }}</div>
                        <small class="text-stone">{{ $user->email }}</small><br>
                        <span class="rv-badge st-approved mt-1">Customer</span>
                    </div>
                </div>

                @include('partials.flash')

                <form method="POST" action="{{ route('customer.profile.info') }}">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label for="name" class="form-label">Nama lengkap</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                                   class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                                   class="form-control @error('email') is-invalid @enderror" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6">
                            <label for="phone" class="form-label">Nomor WhatsApp</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                   class="form-control @error('phone') is-invalid @enderror" placeholder="08xxxxxxxxxx">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label for="address" class="form-label">Alamat <span class="text-stone fw-normal">(opsional)</span></label>
                            <textarea id="address" name="address" class="form-control @error('address') is-invalid @enderror"
                                      rows="2" placeholder="Kota, Provinsi">{{ old('address', $user->address) }}</textarea>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-pine" data-busy="Menyimpan...">
                                <i class="bi bi-check-lg me-1"></i>Simpan perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Ganti kata sandi --}}
        <div class="col-lg-5">
            <div class="panel">
                <h2 class="h5 mb-4">Ganti kata sandi</h2>

                <form method="POST" action="{{ route('customer.profile.password') }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Kata sandi saat ini</label>
                        <input type="password" id="current_password" name="current_password"
                               class="form-control @error('current_password') is-invalid @enderror" required>
                        @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Kata sandi baru</label>
                        <input type="password" id="password" name="password"
                               class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @else
                            <div class="form-text">Minimal 8 karakter.</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">Ulangi kata sandi baru</label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-outline-pine" data-busy="Mengubah...">
                        <i class="bi bi-lock me-1"></i>Ganti kata sandi
                    </button>
                </form>
            </div>

            {{-- Info akun --}}
            <div class="panel mt-3" style="background: var(--rv-mist); border-color: transparent">
                <h3 class="h6 text-stone text-uppercase mb-3" style="letter-spacing:.06em">Info akun</h3>
                <dl class="detail-list">
                    <dt>Bergabung</dt>
                    <dd>{{ $user->created_at->translatedFormat('d F Y') }}</dd>
                    <dt>Role</dt>
                    <dd><span class="rv-badge st-approved">Customer</span></dd>
                </dl>
                <div class="mt-3">
                    <a href="{{ route('customer.rentals.index') }}" class="btn btn-sm btn-outline-pine w-100">
                        <i class="bi bi-calendar-check me-1"></i>Lihat riwayat penyewaan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
