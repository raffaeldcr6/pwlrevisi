@extends('layouts.admin')

@section('title', 'Penyewaan')
@section('heading', 'Penyewaan')
@section('subheading', 'Tinjau pengajuan dan perbarui statusnya.')

@section('content')
<div class="filter-pills mb-3">
    <a href="{{ route('admin.rentals.index') }}" class="{{ $status ? '' : 'active' }}">Semua<small>{{ $counts->sum() }}</small></a>
    @foreach (\App\Models\Rental::STATUSES as $key => $label)
        <a href="{{ route('admin.rentals.index', ['status' => $key]) }}" class="{{ $status === $key ? 'active' : '' }}">{{ $label }}<small>{{ $counts[$key] ?? 0 }}</small></a>
    @endforeach
</div>

<form method="GET" class="panel mb-3 py-3">
    @if ($status)<input type="hidden" name="status" value="{{ $status }}">@endif
    <div class="row g-2">
        <div class="col-md-9"><input type="search" name="q" value="{{ $search }}" class="form-control" placeholder="Cari kode atau nama customer" aria-label="Cari penyewaan"></div>
        <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-pine flex-fill" type="submit">Cari</button>
            @if ($search)<a href="{{ route('admin.rentals.index', array_filter(['status' => $status])) }}" class="btn btn-outline-pine">Reset</a>@endif
        </div>
    </div>
</form>

<div class="panel panel-flush">
    <div class="table-responsive">
        <table class="table rv-table">
            <thead><tr><th>Kode</th><th>Customer</th><th>Tanggal sewa</th><th class="text-center">Alat</th><th class="text-end">Total</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                @forelse ($rentals as $r)
                    <tr>
                        <td class="fw-semibold">{{ $r->code }}</td>
                        <td>{{ $r->user->name }}<br><small class="text-stone">{{ $r->user->phone ?: $r->user->email }}</small></td>
                        <td>{{ $r->start_date->translatedFormat('d M') }} - {{ $r->end_date->translatedFormat('d M Y') }}<br><small class="text-stone">{{ $r->days }} hari</small></td>
                        <td class="text-center">{{ $r->items->sum('qty') }}</td>
                        <td class="text-end">{{ $r->total_formatted }}</td>
                        <td><span class="rv-badge st-{{ $r->status }}">{{ $r->status_label }}</span></td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('admin.rentals.show', $r) }}" class="btn btn-sm btn-pine">Kelola</a>
                                @if ($r->isDeletableByAdmin())
                                    <form action="{{ route('admin.rentals.destroy', $r) }}" method="POST" class="js-confirm" data-title="Hapus data penyewaan?" data-text="Riwayat {{ $r->code }} akan dihapus permanen." data-ok="Ya, hapus">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit" aria-label="Hapus {{ $r->code }}"><i class="bi bi-trash"></i></button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7"><div class="empty"><i class="bi bi-inbox"></i><h3>Tidak ada penyewaan</h3><p class="mb-0">Belum ada data untuk filter ini.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@if ($rentals->hasPages())<div class="d-flex justify-content-center mt-4">{{ $rentals->links() }}</div>@endif
@endsection
