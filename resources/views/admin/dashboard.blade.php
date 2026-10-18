@extends('layouts.admin')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')
@section('subheading', 'Ringkasan penyewaan dan stok hari ini.')

@section('content')
{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="stat hl">
            <div class="label"><i class="bi bi-cash-stack"></i> Pendapatan (active)</div>
            <div class="value">Rp {{ number_format($stats['revenue'] / 1000, 0, ',', '.') }}rb</div>
            <div class="trend" style="color:#BFD3C4"><i class="bi bi-graph-up-arrow me-1"></i>Disetujui + aktif + selesai</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat" style="border-top: 3px solid var(--rv-lantern)">
            <div class="label"><i class="bi bi-hourglass-split"></i> Menunggu</div>
            <div class="value" style="color: #8A5E00">{{ $stats['pending'] }}</div>
            <div class="trend text-stone">Perlu persetujuan admin</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat" style="border-top: 3px solid var(--rv-pine)">
            <div class="label"><i class="bi bi-calendar-check"></i> Aktif</div>
            <div class="value">{{ $stats['active'] }}</div>
            <div class="trend text-stone">Disetujui + sedang disewa</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat" style="border-top: 3px solid var(--rv-lake)">
            <div class="label"><i class="bi bi-people"></i> Customer</div>
            <div class="value">{{ $stats['customers'] }}</div>
            <div class="trend text-stone">{{ $stats['equipment'] }} alat · {{ $stats['categories'] }} kategori</div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    {{-- Chart pendapatan --}}
    <div class="col-xl-7">
        <div class="panel h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="h5 mb-0">Pendapatan 6 bulan terakhir</h2>
                    <small class="text-stone">Dari penyewaan disetujui, aktif, & selesai</small>
                </div>
                <span class="rv-badge st-rented">
                    Rp {{ number_format($stats['revenue'], 0, ',', '.') }}
                </span>
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

    {{-- Top alat --}}
    <div class="col-xl-5">
        <div class="panel panel-flush h-100">
            <div class="panel-head">
                <h2>Alat paling sering disewa</h2>
                <a href="{{ route('admin.equipment.index') }}" class="btn btn-sm btn-outline-pine">Semua</a>
            </div>
            @forelse ($top as $i => $item)
                <div class="d-flex align-items-center gap-3 px-3 py-2 border-bottom">
                    <span class="fw-bold text-stone" style="min-width:1.5rem;font-size:.9rem">{{ $i+1 }}</span>
                    <span class="table-thumb tone-{{ $item->category->tone }}">
                        @if($item->image_url)
                            <img src="{{ $item->image_url }}" alt="">
                        @else
                            <i class="bi {{ $item->category->icon }}"></i>
                        @endif
                    </span>
                    <div class="flex-fill min-w-0">
                        <div class="fw-semibold text-truncate">{{ $item->name }}</div>
                        <small class="text-stone">{{ $item->category->name }}</small>
                    </div>
                    <span class="fw-bold text-pine text-nowrap">
                        {{ (int) $item->rental_items_sum_qty }}<small class="text-stone fw-normal"> unit</small>
                    </span>
                </div>
            @empty
                <div class="empty py-4"><i class="bi bi-inbox"></i><p class="mb-0">Belum ada data.</p></div>
            @endforelse
        </div>
    </div>
</div>

{{-- Status summary + Recent --}}
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="panel h-100">
            <h2 class="h6 mb-3 text-stone text-uppercase" style="letter-spacing:.06em">Status penyewaan</h2>
            @foreach (\App\Models\Rental::STATUSES as $key => $label)
                @php($cnt = $counts->get($key, 0))
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <span class="rv-badge st-{{ $key }}">{{ $label }}</span>
                    <a href="{{ route('admin.rentals.index', ['status' => $key]) }}" class="fw-semibold">{{ $cnt }}</a>
                </div>
            @endforeach
        </div>
    </div>
    <div class="col-md-8">
        <div class="panel panel-flush h-100">
            <div class="panel-head">
                <h2>Pengajuan terbaru</h2>
                <a href="{{ route('admin.rentals.index') }}" class="btn btn-sm btn-outline-pine">Lihat semua</a>
            </div>
            <div class="table-responsive">
                <table class="table rv-table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Customer</th>
                            <th>Tanggal sewa</th>
                            <th class="text-end">Total</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recent as $r)
                            <tr>
                                <td class="fw-semibold">{{ $r->code }}</td>
                                <td>{{ $r->user->name }}</td>
                                <td>{{ $r->start_date->translatedFormat('d M') }} – {{ $r->end_date->translatedFormat('d M Y') }}</td>
                                <td class="text-end">{{ $r->total_formatted }}</td>
                                <td><span class="rv-badge st-{{ $r->status }}">{{ $r->status_label }}</span></td>
                                <td class="text-end">
                                    <a href="{{ route('admin.rentals.show', $r) }}" class="btn btn-sm btn-outline-pine">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6"><div class="empty py-4"><i class="bi bi-inbox"></i>Belum ada pengajuan.</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
