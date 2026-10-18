@extends('layouts.admin')

@section('title', $customer->name)
@section('heading', $customer->name)
@section('subheading', 'Detail & riwayat penyewaan pelanggan.')

@section('actions')
    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-pine">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
@endsection

@section('content')
{{-- Stat cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat">
            <div class="label"><i class="bi bi-calendar3"></i> Total sewa</div>
            <div class="value">{{ $stats['total'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat" style="border-top:3px solid var(--rv-pine)">
            <div class="label"><i class="bi bi-calendar-check"></i> Aktif</div>
            <div class="value">{{ $stats['active'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat" style="border-top:3px solid var(--rv-lantern)">
            <div class="label"><i class="bi bi-check-circle"></i> Selesai</div>
            <div class="value">{{ $stats['returned'] }}</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat hl">
            <div class="label"><i class="bi bi-cash-stack"></i> Total belanja</div>
            <div class="value" style="font-size:1.3rem">Rp {{ number_format($stats['spend'] / 1000, 0, ',', '.') }}rb</div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Info pelanggan --}}
    <div class="col-md-4">
        <div class="panel h-100">
            <div class="d-flex align-items-center gap-3 mb-4">
                <span class="avatar" style="width:52px;height:52px;font-size:1.3rem">
                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                </span>
                <div>
                    <div class="fw-bold">{{ $customer->name }}</div>
                    <span class="rv-badge st-approved">Customer</span>
                </div>
            </div>
            <dl class="detail-list">
                <dt>Email</dt>
                <dd>{{ $customer->email }}</dd>
                <dt>Telepon</dt>
                <dd>{{ $customer->phone ?: '—' }}</dd>
                <dt>Alamat</dt>
                <dd>{{ $customer->address ?: '—' }}</dd>
                <dt>Bergabung</dt>
                <dd>{{ $customer->created_at->translatedFormat('d F Y') }}</dd>
            </dl>
        </div>
    </div>

    {{-- Riwayat sewa --}}
    <div class="col-md-8">
        <div class="panel panel-flush">
            <div class="panel-head">
                <h2>Riwayat penyewaan</h2>
                <span class="rv-badge">{{ $stats['total'] }} transaksi</span>
            </div>
            <div class="table-responsive">
                <table class="table rv-table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Tanggal</th>
                            <th class="text-center">Unit</th>
                            <th class="text-end">Total</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rentals as $r)
                            <tr>
                                <td class="fw-semibold">{{ $r->code }}</td>
                                <td>
                                    {{ $r->start_date->translatedFormat('d M Y') }}<br>
                                    <small class="text-stone">{{ $r->days }} hari</small>
                                </td>
                                <td class="text-center">{{ $r->items->sum('qty') }}</td>
                                <td class="text-end">{{ $r->total_formatted }}</td>
                                <td><span class="rv-badge st-{{ $r->status }}">{{ $r->status_label }}</span></td>
                                <td class="text-end">
                                    <a href="{{ route('admin.rentals.show', $r) }}" class="btn btn-sm btn-outline-pine">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty py-3"><i class="bi bi-inbox"></i><p class="mb-0">Belum ada riwayat sewa.</p></div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($rentals->hasPages())
            <div class="d-flex justify-content-center mt-3">{{ $rentals->links() }}</div>
        @endif
    </div>
</div>
@endsection
