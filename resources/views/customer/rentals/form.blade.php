@extends('layouts.app')

@php($editing = $rental->exists)

@section('title', $editing ? 'Ubah Penyewaan' : 'Ajukan Sewa')

@section('content')
<div class="container py-5">
    <div class="mb-4">
        <h1 class="display-font" style="font-size: clamp(2rem, 4vw, 2.8rem)">{{ $editing ? 'Ubah penyewaan '.$rental->code : 'Ajukan sewa alat' }}</h1>
        <p class="text-stone mb-0">Tentukan tanggal, lalu atur jumlah alat. Total biaya terhitung otomatis.</p>
    </div>

    <form id="rental-form" method="POST" action="{{ $editing ? route('customer.rentals.update', $rental) : route('customer.rentals.store') }}" novalidate>
        @csrf
        @if ($editing) @method('PUT') @endif

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="panel mb-4">
                    <h2 class="h5 mb-3">1. Tanggal sewa</h2>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label for="start_date" class="form-label">Tanggal mulai</label>
                            <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" min="{{ $editing ? '' : now()->toDateString() }}" class="form-control @error('start_date') is-invalid @enderror" required>
                            @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6">
                            <label for="end_date" class="form-label">Tanggal selesai</label>
                            <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" class="form-control @error('end_date') is-invalid @enderror" required>
                            @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="form-text mt-2">Hari mulai dan hari selesai sama-sama dihitung sebagai hari sewa.</div>
                </div>

                <div class="panel mb-4">
                    <h2 class="h5 mb-1">2. Pilih alat</h2>
                    <p class="text-stone small mb-3">Isi jumlah pada alat yang ingin disewa. Biarkan 0 jika tidak dipakai.</p>

                    @error('items')
                        <div class="rv-alert err mb-3" role="alert"><i class="bi bi-exclamation-triangle-fill"></i><div>{{ $message }}</div></div>
                    @enderror

                    @forelse ($groups as $group)
                        <div class="pick-group">
                            <h3 class="h6 text-stone mb-0"><i class="bi {{ $group->icon }} me-1"></i>{{ $group->name }}</h3>
                            @foreach ($group->equipment as $eq)
                                <div class="pick-row" data-price="{{ $eq->price_per_day }}" data-name="{{ $eq->name }}">
                                    <div>
                                        <div class="nm">{{ $eq->name }}</div>
                                        <div class="sub">{{ $eq->price_formatted }} per hari &middot; stok {{ $eq->stock }}</div>
                                    </div>
                                    <div class="qty">
                                        <button type="button" data-step="-1" aria-label="Kurangi {{ $eq->name }}">&minus;</button>
                                        <input type="number" name="items[{{ $eq->id }}]" value="{{ $quantities[$eq->id] ?? 0 }}" min="0" max="{{ $eq->stock }}" inputmode="numeric" aria-label="Jumlah {{ $eq->name }}">
                                        <button type="button" data-step="1" aria-label="Tambah {{ $eq->name }}">+</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @empty
                        <div class="empty"><i class="bi bi-box-seam"></i><h3>Belum ada alat yang bisa disewa</h3><p class="mb-0">Silakan kembali lagi nanti.</p></div>
                    @endforelse
                </div>

                <div class="panel">
                    <h2 class="h5 mb-3">3. Catatan <span class="text-stone fw-normal fs-6">(opsional)</span></h2>
                    <textarea name="note" rows="3" class="form-control @error('note') is-invalid @enderror" placeholder="Contoh: ambil sore hari, tujuan Gunung Pesagi.">{{ old('note', $rental->note) }}</textarea>
                    @error('note')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="col-lg-4">
                <div class="panel summary">
                    <h2 class="h5 mb-3">Ringkasan</h2>
                    <dl class="d-grid gap-2 mb-3">
                        <div class="d-flex justify-content-between"><dt>Lama sewa</dt><dd class="mb-0 fw-semibold" id="sum-days">-</dd></div>
                        <div class="d-flex justify-content-between"><dt>Jumlah alat</dt><dd class="mb-0 fw-semibold" id="sum-qty">0 unit</dd></div>
                    </dl>
                    <ul class="list-unstyled small border-top border-bottom py-3 mb-3 d-grid gap-1" id="sum-list"></ul>
                    <div class="text-stone small">Total biaya</div>
                    <div class="grand mb-3" id="sum-total">Rp 0</div>
                    <button type="submit" class="btn btn-pine btn-lg w-100" data-busy="Menyimpan...">{{ $editing ? 'Simpan perubahan' : 'Kirim pengajuan' }}</button>
                    <a href="{{ $editing ? route('customer.rentals.show', $rental) : route('catalog.index') }}" class="btn btn-link w-100 mt-1">Batal</a>
                    <p class="small text-stone mb-0 mt-2">Pengajuan akan diperiksa admin. Stok dicek lagi saat dikirim.</p>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
