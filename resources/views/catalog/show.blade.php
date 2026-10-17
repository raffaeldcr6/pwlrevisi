@extends('layouts.app')

@section('title', $equipment->name)

@section('content')
@php
    $isAdmin = auth()->check() && auth()->user()->isAdmin();
    $rentable = $equipment->isRentable();
@endphp
<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('catalog.index') }}">Katalog</a></li>
            <li class="breadcrumb-item"><a href="{{ route('catalog.index', ['kategori' => $equipment->category_id]) }}">{{ $equipment->category->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $equipment->name }}</li>
        </ol>
    </nav>

    <div class="row g-5">
        <div class="col-lg-6">
            <div class="thumb tone-{{ $equipment->category->tone }}" style="border-radius: 18px; aspect-ratio: 5/4;">
                @if ($equipment->image_url)
                    <img src="{{ $equipment->image_url }}" alt="{{ $equipment->name }}">
                @else
                    <i class="bi {{ $equipment->category->icon }}" style="font-size: 6rem"></i>
                @endif
            </div>
        </div>
        <div class="col-lg-6">
            <span class="rv-badge st-{{ $equipment->status }} mb-3">{{ $equipment->status_label }}</span>
            <h1 style="font-size: clamp(1.9rem, 3.6vw, 2.6rem)">{{ $equipment->name }}</h1>
            <p class="text-stone">{{ $equipment->category->name }} &middot; Stok {{ $equipment->stock }} unit</p>

            <div class="price-tag my-3" style="font-size: 2.1rem">{{ $equipment->price_formatted }} <small style="font-size:1rem">/hari</small></div>

            <p style="max-width: 60ch">{{ $equipment->description ?: 'Belum ada deskripsi untuk alat ini.' }}</p>

            <div class="d-flex flex-wrap gap-2 mt-4">
                @if ($isAdmin)
                    <a href="{{ route('admin.equipment.edit', $equipment) }}" class="btn btn-pine btn-lg">Ubah data alat</a>
                @elseif ($rentable)
                    <a href="{{ route('customer.rentals.create', ['alat' => $equipment->id]) }}" class="btn btn-pine btn-lg">Sewa alat ini</a>
                @else
                    <button class="btn btn-secondary btn-lg" disabled>{{ $equipment->status === 'maintenance' ? 'Sedang perawatan' : 'Stok habis' }}</button>
                @endif
                <a href="{{ route('catalog.index') }}" class="btn btn-outline-pine btn-lg">Kembali ke katalog</a>
            </div>
            @guest
                <p class="small text-stone mt-3 mb-0">Kamu perlu <a href="{{ route('login') }}">masuk</a> atau <a href="{{ route('register') }}">daftar</a> untuk mengajukan sewa.</p>
            @endguest
        </div>
    </div>

    @if ($related->isNotEmpty())
        <h2 class="mt-5 mb-4" style="font-size:1.6rem">Alat lain di kategori {{ $equipment->category->name }}</h2>
        <div class="row g-4">
            @foreach ($related as $item)
                <div class="col-sm-6 col-lg-4">@include('partials.equipment-card', ['equipment' => $item])</div>
            @endforeach
        </div>
    @endif
</div>
@endsection
