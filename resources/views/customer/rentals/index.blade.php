@extends('layouts.app')

@section('title', 'Penyewaan Saya')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
        <div>
            <h1 class="display-font" style="font-size: clamp(2rem, 4vw, 2.8rem)">Penyewaan saya</h1>
            <p class="text-stone mb-0">Pantau status pengajuan dan riwayat sewa.</p>
        </div>
        <a href="{{ route('customer.rentals.create') }}" class="btn btn-pine btn-lg"><i class="bi bi-plus-lg me-1"></i>Ajukan sewa baru</a>
    </div>

    <div class="filter-pills mb-4">
        <a href="{{ route('customer.rentals.index') }}" class="{{ $status ? '' : 'active' }}">Semua</a>
        @foreach (\App\Models\Rental::STATUSES as $key => $label)
            <a href="{{ route('customer.rentals.index', ['status' => $key]) }}" class="{{ $status === $key ? 'active' : '' }}">{{ $label }}</a>
        @endforeach
    </div>

    @forelse ($rentals as $rental)
        <div class="panel mb-3">
            <div class="row g-3 align-items-center">
                <div class="col-lg-5">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <a href="{{ route('customer.rentals.show', $rental) }}" class="fw-bold text-decoration-none text-pine">{{ $rental->code }}</a>
                        <span class="rv-badge st-{{ $rental->status }}">{{ $rental->status_label }}</span>
                    </div>
                    <div class="text-stone small">
                        <i class="bi bi-calendar-event me-1"></i>{{ $rental->start_date->translatedFormat('d M Y') }} - {{ $rental->end_date->translatedFormat('d M Y') }} ({{ $rental->days }} hari)
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="small">
                        @foreach ($rental->items->take(3) as $line)
                            <div>{{ $line->qty }}&times; {{ $line->equipment->name }}</div>
                        @endforeach
                        @if ($rental->items->count() > 3)
                            <div class="text-stone">dan {{ $rental->items->count() - 3 }} alat lainnya</div>
                        @endif
                    </div>
                </div>
                <div class="col-lg-3 text-lg-end">
                    <div class="price-tag mb-2">{{ $rental->total_formatted }}</div>
                    <div class="d-flex gap-2 justify-content-lg-end flex-wrap">
                        <a href="{{ route('customer.rentals.show', $rental) }}" class="btn btn-sm btn-outline-pine">Detail</a>
                        @if ($rental->isPending())
                            <a href="{{ route('customer.rentals.edit', $rental) }}" class="btn btn-sm btn-pine">Ubah</a>
                            <form action="{{ route('customer.rentals.destroy', $rental) }}" method="POST" class="js-confirm" data-title="Batalkan pengajuan?" data-text="Pengajuan {{ $rental->code }} akan dihapus." data-ok="Ya, batalkan">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Batalkan</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="panel empty">
            <i class="bi bi-backpack"></i>
            <h3>{{ $status ? 'Tidak ada penyewaan dengan status ini' : 'Kamu belum pernah menyewa' }}</h3>
            <p>Pilih alat di katalog, lalu ajukan sewa pertamamu.</p>
            <a href="{{ route('catalog.index') }}" class="btn btn-pine">Buka katalog</a>
        </div>
    @endforelse

    @if ($rentals->hasPages())
        <div class="d-flex justify-content-center mt-4">{{ $rentals->links() }}</div>
    @endif
</div>
@endsection
