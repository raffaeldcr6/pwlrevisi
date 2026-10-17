@extends('layouts.app')

@section('title', 'Sewa Alat Camping & Outdoor')

@section('content')
<section class="hero">
    <div class="container py-5">
        <div class="row align-items-center g-5 py-lg-4">
            <div class="col-lg-7">
                <h1>Naik gunung tanpa harus membeli semua perlengkapannya</h1>
                <p class="lead mt-3">Tenda, carrier, sleeping bag, sampai kompor lapangan. Pilih alatnya, ajukan sewa, lalu ambil di basecamp kami.</p>
                <div class="d-flex flex-wrap gap-2 mt-4">
                    <a href="{{ route('catalog.index') }}" class="btn btn-pine btn-lg">Lihat katalog</a>
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-outline-pine btn-lg">Buat akun gratis</a>
                    @endguest
                </div>
                <div class="hero-facts">
                    <div><strong>{{ $totalEquipment }} alat</strong><span>siap disewa</span></div>
                    <div><strong>{{ $categories->count() }} kategori</strong><span>dari tenda sampai navigasi</span></div>
                    <div><strong>Mulai Rp 10.000</strong><span>per hari</span></div>
                </div>
            </div>

            <div class="col-lg-5">
                @if ($packing->isNotEmpty())
                <div class="packing" id="packing">
                    <h2>Daftar bawaan akhir pekan</h2>
                    <p class="text-stone small mb-2">Centang yang kamu butuhkan, lihat perkiraan biaya per hari.</p>
                    @foreach ($packing as $item)
                        <label class="packing-row">
                            <input type="checkbox" value="{{ $item->id }}" data-price="{{ $item->price_per_day }}" {{ $loop->index < 3 ? 'checked' : '' }}>
                            <span class="name">{{ $item->name }}</span>
                            <span class="price">{{ $item->price_formatted }}</span>
                        </label>
                    @endforeach
                    <div class="packing-total">
                        <span class="text-stone">Perkiraan per hari</span>
                        <strong id="packing-total">Rp 0</strong>
                    </div>
                    <a id="packing-link" data-base="{{ route('customer.rentals.create') }}" href="{{ route('customer.rentals.create') }}" class="btn btn-lantern w-100">Sewa daftar ini</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

<section class="section pb-0">
    <div class="container">
        <h2 class="section-title">Cari berdasarkan kebutuhan</h2>
        <p class="section-sub mb-4">Semua perlengkapan dikelompokkan supaya daftar bawaanmu cepat lengkap.</p>
        <div class="row g-3">
            @foreach ($categories as $cat)
                <div class="col-6 col-lg-4">
                    <a href="{{ route('catalog.index', ['kategori' => $cat->id]) }}" class="cat-link tone-{{ $cat->tone }}">
                        <span class="ico"><i class="bi {{ $cat->icon }}"></i></span>
                        <span><strong>{{ $cat->name }}</strong><small>{{ $cat->equipment_count }} alat</small></span>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end flex-wrap gap-2 mb-4">
            <div>
                <h2 class="section-title mb-1">Paling sering disewa</h2>
                <p class="section-sub mb-0">Pilihan yang paling banyak dibawa pendaki bulan ini.</p>
            </div>
            <a href="{{ route('catalog.index') }}" class="fw-semibold">Semua alat</a>
        </div>
        <div class="row g-4">
            @forelse ($popular as $equipment)
                <div class="col-sm-6 col-lg-4">@include('partials.equipment-card')</div>
            @empty
                <div class="col-12"><div class="panel empty"><i class="bi bi-box-seam"></i><h3>Belum ada alat</h3><p class="mb-0">Katalog akan segera diisi.</p></div></div>
            @endforelse
        </div>
    </div>
</section>

<section class="section pt-0" id="cara-sewa">
    <div class="container">
        <div class="panel p-4 p-lg-5">
            <h2 class="section-title">Cara menyewa</h2>
            <p class="section-sub mb-4">Empat langkah, dari memilih alat sampai mengembalikannya.</p>
            <div class="steps">
                <div class="step"><h3>Pilih alat</h3><p>Telusuri katalog, lalu tentukan jumlah tiap alat.</p></div>
                <div class="step"><h3>Ajukan sewa</h3><p>Isi tanggal mulai dan selesai. Total biaya dihitung otomatis.</p></div>
                <div class="step"><h3>Ambil di basecamp</h3><p>Setelah admin menyetujui, ambil alat sesuai tanggal sewa.</p></div>
                <div class="step"><h3>Kembalikan</h3><p>Kembalikan alat bersih dan lengkap di hari terakhir sewa.</p></div>
            </div>
        </div>
    </div>
</section>
@endsection
