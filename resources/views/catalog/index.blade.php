@extends('layouts.app')

@section('title', 'Katalog Alat')

@section('content')
<div class="container py-5">
    <div class="mb-4">
        <h1 class="display-font" style="font-size: clamp(2rem, 4vw, 2.8rem)">Katalog alat</h1>
        <p class="text-stone mb-0">Pilih perlengkapan yang kamu butuhkan, lalu ajukan sewa.</p>
    </div>

    <form method="GET" action="{{ route('catalog.index') }}" class="panel mb-4 py-3">
        @if (request('kategori'))<input type="hidden" name="kategori" value="{{ request('kategori') }}">@endif
        <div class="row g-2 align-items-end">
            <div class="col-md-6">
                <label for="q" class="form-label">Cari alat</label>
                <input type="search" id="q" name="q" value="{{ request('q') }}" class="form-control" placeholder="Contoh: tenda, carrier, headlamp">
            </div>
            <div class="col-md-3">
                <label for="urut" class="form-label">Urutkan</label>
                <select id="urut" name="urut" class="form-select">
                    <option value="terbaru" @selected($sort === 'terbaru')>Terbaru</option>
                    <option value="termurah" @selected($sort === 'termurah')>Harga termurah</option>
                    <option value="termahal" @selected($sort === 'termahal')>Harga termahal</option>
                    <option value="nama" @selected($sort === 'nama')>Nama A-Z</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-pine flex-fill" type="submit"><i class="bi bi-search me-1"></i>Cari</button>
                @if (request()->hasAny(['q', 'kategori', 'urut']))
                    <a href="{{ route('catalog.index') }}" class="btn btn-outline-pine">Reset</a>
                @endif
            </div>
        </div>
    </form>

    <div class="filter-pills mb-4">
        <a href="{{ route('catalog.index', request()->except('kategori', 'page')) }}" class="{{ request('kategori') ? '' : 'active' }}">Semua</a>
        @foreach ($categories as $cat)
            <a href="{{ route('catalog.index', array_merge(request()->except('page'), ['kategori' => $cat->id])) }}" class="{{ (string) request('kategori') === (string) $cat->id ? 'active' : '' }}">
                {{ $cat->name }}<small>{{ $cat->equipment_count }}</small>
            </a>
        @endforeach
    </div>

    <div class="row g-4">
        @forelse ($equipment as $item)
            <div class="col-sm-6 col-lg-4">
                @include('partials.equipment-card', ['equipment' => $item])
            </div>
        @empty
            <div class="col-12">
                <div class="panel empty">
                    <i class="bi bi-search"></i>
                    <h3>Alat tidak ditemukan</h3>
                    <p>Coba kata kunci lain, atau tampilkan semua kategori.</p>
                    <a href="{{ route('catalog.index') }}" class="btn btn-outline-pine">Tampilkan semua alat</a>
                </div>
            </div>
        @endforelse
    </div>

    @if ($equipment->hasPages())
        <div class="d-flex justify-content-center mt-5">{{ $equipment->links() }}</div>
    @endif
</div>
@endsection
