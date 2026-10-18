@extends('layouts.app')

@section('title', 'Katalog Alat')
@section('description', 'Temukan tenda, carrier, sleeping bag, dan perlengkapan camping lainnya di RentVentures. Harga terjangkau, stok selalu updated.')

@section('content')
<div class="container py-5">
    <div class="mb-4">
        <h1 class="display-font" style="font-size: clamp(2rem, 4vw, 2.8rem)">Katalog alat</h1>
        <p class="text-stone mb-0">Pilih perlengkapan yang kamu butuhkan, lalu ajukan sewa.</p>
    </div>

    {{-- Filter & Search --}}
    <form method="GET" action="{{ route('catalog.index') }}" id="catalog-filter" class="panel mb-4 py-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-5">
                <label for="q" class="form-label">Cari alat</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0" style="border-radius:10px 0 0 10px;border-color:#bccabb"><i class="bi bi-search text-stone"></i></span>
                    <input type="search" id="q" name="q" value="{{ request('q') }}"
                           class="form-control border-start-0 ps-1" style="border-radius:0 10px 10px 0"
                           placeholder="Contoh: tenda, carrier, headlamp">
                </div>
            </div>
            <div class="col-6 col-md-2">
                <label for="kondisi" class="form-label">Kondisi</label>
                <select id="kondisi" name="kondisi" class="form-select">
                    <option value="">Semua</option>
                    @foreach ($conditions as $val => $label)
                        <option value="{{ $val }}" @selected(request('kondisi') === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label for="urut" class="form-label">Urutkan</label>
                <select id="urut" name="urut" class="form-select">
                    <option value="terbaru" @selected($sort === 'terbaru')>Terbaru</option>
                    <option value="termurah" @selected($sort === 'termurah')>Harga termurah</option>
                    <option value="termahal" @selected($sort === 'termahal')>Harga termahal</option>
                    <option value="nama" @selected($sort === 'nama')>Nama A–Z</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-pine flex-fill" type="submit">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
                @if (request()->hasAny(['q', 'kategori', 'urut', 'kondisi', 'ketersediaan', 'harga_min', 'harga_max']))
                    <a href="{{ route('catalog.index') }}" class="btn btn-outline-pine" title="Reset filter">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </div>

        {{-- Range harga --}}
        <div class="row g-2 mt-2">
            <div class="col-6 col-md-3">
                <label for="harga_min" class="form-label">Harga min/hari</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">Rp</span>
                    <input type="number" id="harga_min" name="harga_min" class="form-control" min="0" step="1000"
                           value="{{ request('harga_min') }}" placeholder="0">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <label for="harga_max" class="form-label">Harga maks/hari</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">Rp</span>
                    <input type="number" id="harga_max" name="harga_max" class="form-control" min="0" step="1000"
                           value="{{ request('harga_max') }}" placeholder="Bebas">
                </div>
            </div>
            <div class="col-md-3">
                <label for="ketersediaan" class="form-label">Ketersediaan</label>
                <select id="ketersediaan" name="ketersediaan" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    <option value="available" @selected(request('ketersediaan') === 'available')>Tersedia saja</option>
                    <option value="maintenance" @selected(request('ketersediaan') === 'maintenance')>Perawatan</option>
                </select>
            </div>
        </div>
    </form>

    {{-- Filter kategori pills --}}
    <div class="filter-pills mb-4">
        <a href="{{ route('catalog.index', request()->except('kategori', 'page')) }}" class="{{ request('kategori') ? '' : 'active' }}">
            Semua
        </a>
        @foreach ($categories as $cat)
            <a href="{{ route('catalog.index', array_merge(request()->except('page'), ['kategori' => $cat->id])) }}"
               class="{{ (string) request('kategori') === (string) $cat->id ? 'active' : '' }}">
                <i class="bi {{ $cat->icon }} me-1"></i>{{ $cat->name }}<small>{{ $cat->equipment_count }}</small>
            </a>
        @endforeach
    </div>

    {{-- Hasil --}}
    @if ($equipment->total() > 0)
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-stone mb-0 small">
            Menampilkan <strong>{{ $equipment->firstItem() }}–{{ $equipment->lastItem() }}</strong> dari <strong>{{ $equipment->total() }}</strong> alat
        </p>
    </div>
    @endif

    <div class="row g-4">
        @forelse ($equipment as $item)
            <div class="col-sm-6 col-lg-4 reveal">
                @include('partials.equipment-card', ['equipment' => $item])
            </div>
        @empty
            <div class="col-12">
                <div class="panel empty">
                    <i class="bi bi-search"></i>
                    <h3>Alat tidak ditemukan</h3>
                    <p>Coba kata kunci lain, atau hapus beberapa filter.</p>
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
