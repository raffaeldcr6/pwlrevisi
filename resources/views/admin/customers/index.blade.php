@extends('layouts.admin')

@section('title', 'Pelanggan')
@section('heading', 'Pelanggan')
@section('subheading', 'Daftar semua customer yang terdaftar.')

@section('actions')
    {{-- export button bisa ditambahkan di sini --}}
@endsection

@section('content')
<form method="GET" class="panel mb-3 py-3">
    <div class="row g-2">
        <div class="col-md-9">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0" style="border-radius:10px 0 0 10px;border-color:#bccabb"><i class="bi bi-search text-stone"></i></span>
                <input type="search" name="q" value="{{ $search }}" class="form-control border-start-0 ps-1"
                       style="border-radius:0 10px 10px 0" placeholder="Cari nama, email, atau telepon...">
            </div>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-pine flex-fill" type="submit"><i class="bi bi-search me-1"></i>Cari</button>
            @if ($search)
                <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-pine"><i class="bi bi-x-lg"></i></a>
            @endif
        </div>
    </div>
</form>

<div class="panel panel-flush">
    <div class="table-responsive">
        <table class="table rv-table">
            <thead>
                <tr>
                    <th>Pelanggan</th>
                    <th>Telepon</th>
                    <th class="text-center">Total sewa</th>
                    <th class="text-end">Total spend</th>
                    <th>Bergabung</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $c)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="avatar" style="width:36px;height:36px;font-size:.85rem">
                                    {{ strtoupper(substr($c->name, 0, 1)) }}
                                </span>
                                <div>
                                    <div class="fw-semibold">{{ $c->name }}</div>
                                    <small class="text-stone">{{ $c->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>{{ $c->phone ?: '—' }}</td>
                        <td class="text-center">
                            <span class="fw-semibold">{{ $c->rentals_count }}</span>
                            <small class="text-stone d-block">penyewaan</small>
                        </td>
                        <td class="text-end">Rp {{ number_format($c->rentals_sum_total_price ?? 0, 0, ',', '.') }}</td>
                        <td>{{ $c->created_at->translatedFormat('d M Y') }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.customers.show', $c) }}" class="btn btn-sm btn-outline-pine">
                                <i class="bi bi-person me-1"></i>Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty py-4">
                                <i class="bi bi-people"></i>
                                <h3>Belum ada pelanggan</h3>
                                <p class="mb-0">{{ $search ? 'Coba kata kunci lain.' : 'Customer akan muncul setelah mendaftar.' }}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if ($customers->hasPages())
    <div class="d-flex justify-content-center mt-4">{{ $customers->links() }}</div>
@endif
@endsection
