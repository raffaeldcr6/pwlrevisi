@extends('layouts.admin')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')
@section('subheading', 'Ringkasan penyewaan dan stok hari ini.')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3"><div class="stat hl"><div class="label"><i class="bi bi-cash-stack"></i>Pendapatan</div><div class="value">Rp {{ number_format($stats['revenue'], 0, ',', '.') }}</div></div></div>
    <div class="col-6 col-xl-3"><div class="stat"><div class="label"><i class="bi bi-hourglass-split"></i>Menunggu persetujuan</div><div class="value">{{ $stats['pending'] }}</div></div></div>
    <div class="col-6 col-xl-3"><div class="stat"><div class="label"><i class="bi bi-calendar-check"></i>Penyewaan aktif</div><div class="value">{{ $stats['active'] }}</div></div></div>
    <div class="col-6 col-xl-3"><div class="stat"><div class="label"><i class="bi bi-people"></i>Customer</div><div class="value">{{ $stats['customers'] }}</div></div></div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-7">
        <div class="panel h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h2 class="h5 mb-0">Pendapatan 6 bulan terakhir</h2>
                <small class="text-stone">{{ $stats['equipment'] }} alat &middot; {{ $stats['categories'] }} kategori</small>
            </div>
            <div class="bars" role="img" aria-label="Grafik pendapatan per bulan">
                @foreach ($chart as $bar)
                    <div class="bar" title="Rp {{ number_format($bar['value'], 0, ',', '.') }}">
                        <span class="val">{{ $bar['value'] > 0 ? number_format($bar['value'] / 1000, 0, ',', '.').'rb' : '' }}</span>
                        <div class="fill" style="height: {{ max(2, round($bar['value'] / $chartMax * 100)) }}%"></div>
                        <small>{{ $bar['label'] }}</small>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="col-xl-5">
        <div class="panel panel-flush h-100">
            <div class="panel-head"><h2>Alat paling sering disewa</h2></div>
            <ul class="list-unstyled mb-0">
                @foreach ($top as $item)
                    <li class="d-flex align-items-center gap-3 px-3 py-2 border-bottom">
                        <span class="table-thumb tone-{{ $item->category->tone }}"><i class="bi {{ $item->category->icon }}"></i></span>
                        <div class="flex-fill"><div class="fw-semibold">{{ $item->name }}</div><small class="text-stone">{{ $item->category->name }}</small></div>
                        <span class="fw-bold text-pine">{{ (int) $item->rental_items_sum_qty }}<small class="text-stone fw-normal"> unit</small></span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

<div class="panel panel-flush">
    <div class="panel-head">
        <h2>Pengajuan terbaru</h2>
        <a href="{{ route('admin.rentals.index') }}" class="btn btn-sm btn-outline-pine">Lihat semua</a>
    </div>
    <div class="table-responsive">
        <table class="table rv-table">
            <thead><tr><th>Kode</th><th>Customer</th><th>Tanggal sewa</th><th class="text-end">Total</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse ($recent as $r)
                    <tr>
                        <td class="fw-semibold">{{ $r->code }}</td>
                        <td>{{ $r->user->name }}</td>
                        <td>{{ $r->start_date->translatedFormat('d M') }} - {{ $r->end_date->translatedFormat('d M Y') }}</td>
                        <td class="text-end">{{ $r->total_formatted }}</td>
                        <td><span class="rv-badge st-{{ $r->status }}">{{ $r->status_label }}</span></td>
                        <td class="text-end"><a href="{{ route('admin.rentals.show', $r) }}" class="btn btn-sm btn-outline-pine">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6"><div class="empty py-4"><i class="bi bi-inbox"></i>Belum ada pengajuan.</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
