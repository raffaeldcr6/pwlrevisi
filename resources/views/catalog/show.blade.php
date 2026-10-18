@extends('layouts.app')

@section('title', $equipment->name)
@section('description', Str::limit($equipment->description, 155) ?: 'Detail alat '.$equipment->name.' di RentVentures.')

@section('content')
@php
    $isAdmin = auth()->check() && auth()->user()->isAdmin();
    $rentable = $equipment->isRentable();
    $specs = $equipment->specifications ? preg_split('/\n+/', trim($equipment->specifications)) : [];
    $specs = array_filter($specs);
@endphp
<div class="container py-5">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('catalog.index') }}">Katalog</a></li>
            <li class="breadcrumb-item"><a href="{{ route('catalog.index', ['kategori' => $equipment->category_id]) }}">{{ $equipment->category->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $equipment->name }}</li>
        </ol>
    </nav>

    <div class="row g-5">
        {{-- Gambar --}}
        <div class="col-lg-6">
            <div class="thumb tone-{{ $equipment->category->tone }}" style="border-radius: 20px; aspect-ratio: 5/4; box-shadow: var(--shadow-lg);">
                @if ($equipment->image_url)
                    <img src="{{ $equipment->image_url }}" alt="{{ $equipment->name }}">
                @else
                    <i class="bi {{ $equipment->category->icon }}" style="font-size: 7rem"></i>
                @endif
            </div>
            {{-- Status & Kondisi badges di bawah gambar --}}
            <div class="d-flex flex-wrap gap-2 mt-3">
                <span class="rv-badge st-{{ $equipment->status }}">
                    <i class="bi {{ $equipment->status === 'available' ? 'bi-check-circle' : 'bi-wrench' }} me-1"></i>
                    {{ $equipment->status_label }}
                </span>
                @if($equipment->condition)
                    <span class="rv-badge" style="color:var(--rv-lake);background:#DCEBF1;border-color:#B7D3DE">
                        <i class="bi bi-shield-check me-1"></i>Kondisi: {{ $equipment->condition_label }}
                    </span>
                @endif
                @if($equipment->stock > 0)
                    <span class="rv-badge" style="color:#3C5A4B;background:#E1ECE3;border-color:#C4D6C8">
                        <i class="bi bi-boxes me-1"></i>Stok {{ $equipment->stock }} unit
                    </span>
                @else
                    <span class="rv-badge st-rejected"><i class="bi bi-x-circle me-1"></i>Stok habis</span>
                @endif
            </div>
        </div>

        {{-- Informasi --}}
        <div class="col-lg-6">
            <small class="text-stone text-uppercase fw-semibold tracking-wide">{{ $equipment->category->name }}</small>
            <h1 style="font-size: clamp(1.9rem, 3.6vw, 2.6rem); margin-top:.3rem">{{ $equipment->name }}</h1>

            <div class="price-tag my-3" style="font-size: 2.2rem">
                {{ $equipment->price_formatted }}
                <small style="font-size:1rem">/hari</small>
            </div>

            @if ($equipment->description)
            <p style="max-width: 62ch; color:#3b4d43">{{ $equipment->description }}</p>
            @endif

            {{-- Spesifikasi --}}
            @if (!empty($specs))
            <div class="mt-4 mb-4">
                <h2 class="h6 mb-2 text-stone text-uppercase fw-semibold" style="font-size:.8rem;letter-spacing:.08em">Spesifikasi</h2>
                <div class="spec-list">
                    @foreach ($specs as $spec)
                        <div class="spec-item">
                            <i class="bi bi-check2-circle"></i>
                            <span>{{ trim($spec) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Ketentuan sewa --}}
            <div class="rv-alert info mb-4" style="font-size:.92rem">
                <i class="bi bi-info-circle-fill" style="font-size:1.1rem;margin-top:.1rem"></i>
                <div>
                    <strong>Ketentuan sewa:</strong> Barang diambil dan dikembalikan di basecamp kami.
                    Denda keterlambatan <strong>10%</strong> dari total harga per hari terlambat.
                </div>
            </div>

            {{-- Tombol aksi --}}
            <div class="d-flex flex-wrap gap-2">
                @if ($isAdmin)
                    <a href="{{ route('admin.equipment.edit', $equipment) }}" class="btn btn-pine btn-lg">
                        <i class="bi bi-pencil me-1"></i>Ubah data alat
                    </a>
                @elseif ($rentable)
                    @auth
                        <a href="{{ route('customer.rentals.create', ['alat' => $equipment->id]) }}" class="btn btn-pine btn-lg">
                            <i class="bi bi-bag-plus me-1"></i>Sewa alat ini
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-pine btn-lg">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Masuk untuk sewa
                        </a>
                    @endauth
                @else
                    <button class="btn btn-secondary btn-lg" disabled>
                        <i class="bi bi-{{ $equipment->status === 'maintenance' ? 'wrench' : 'x-circle' }} me-1"></i>
                        {{ $equipment->status === 'maintenance' ? 'Sedang perawatan' : 'Stok habis' }}
                    </button>
                @endif
                <a href="{{ route('catalog.index', ['kategori' => $equipment->category_id]) }}" class="btn btn-outline-pine btn-lg">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>

            @guest
                <p class="small text-stone mt-3 mb-0">
                    Kamu perlu <a href="{{ route('login') }}">masuk</a> atau <a href="{{ route('register') }}">daftar</a> untuk mengajukan sewa.
                </p>
            @endguest
        </div>
    </div>

    {{-- Alat lain di kategori --}}
    @if ($related->isNotEmpty())
        <div class="mt-5">
            <div class="d-flex justify-content-between align-items-end flex-wrap gap-2 mb-4">
                <div>
                    <h2 style="font-size:1.6rem;margin-bottom:.2rem">Alat lain di kategori {{ $equipment->category->name }}</h2>
                    <p class="text-stone mb-0">Mungkin kamu juga membutuhkan ini.</p>
                </div>
                <a href="{{ route('catalog.index', ['kategori' => $equipment->category_id]) }}" class="fw-semibold">
                    Lihat semua <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="row g-4">
                @foreach ($related as $item)
                    <div class="col-sm-6 col-lg-4 reveal">@include('partials.equipment-card', ['equipment' => $item])</div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
