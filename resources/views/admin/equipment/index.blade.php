@extends('layouts.admin')

@section('title', 'Data Alat')
@section('heading', 'Data alat')
@section('subheading', 'Kelola stok, harga, dan status alat.')
@section('actions')
    <a href="{{ route('admin.equipment.create') }}" class="btn btn-pine"><i class="bi bi-plus-lg me-1"></i>Tambah alat</a>
@endsection

@section('content')
<form method="GET" class="panel mb-3 py-3">
    <div class="row g-2">
        <div class="col-md-5"><input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama alat" aria-label="Cari nama alat"></div>
        <div class="col-md-4">
            <select name="kategori" class="form-select" aria-label="Filter kategori">
                <option value="">Semua kategori</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" @selected((string) request('kategori') === (string) $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-pine flex-fill" type="submit">Cari</button>
            @if (request()->hasAny(['q', 'kategori']))<a href="{{ route('admin.equipment.index') }}" class="btn btn-outline-pine">Reset</a>@endif
        </div>
    </div>
</form>

<div class="panel panel-flush">
    <div class="table-responsive">
        <table class="table rv-table">
            <thead><tr><th>Alat</th><th>Kategori</th><th class="text-end">Harga/hari</th><th class="text-center">Stok</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <span class="table-thumb tone-{{ $item->category->tone }}">
                                    @if ($item->image_url)<img src="{{ $item->image_url }}" alt="">@else<i class="bi {{ $item->category->icon }}"></i>@endif
                                </span>
                                <span class="fw-semibold">{{ $item->name }}</span>
                            </div>
                        </td>
                        <td>{{ $item->category->name }}</td>
                        <td class="text-end">{{ $item->price_formatted }}</td>
                        <td class="text-center">{{ $item->stock }}</td>
                        <td><span class="rv-badge st-{{ $item->status }}">{{ $item->status_label }}</span></td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('admin.equipment.edit', $item) }}" class="btn btn-sm btn-outline-pine">Ubah</a>
                                <form action="{{ route('admin.equipment.destroy', $item) }}" method="POST" class="js-confirm" data-title="Hapus alat?" data-text="&quot;{{ $item->name }}&quot; akan dihapus permanen." data-ok="Ya, hapus">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6"><div class="empty"><i class="bi bi-box-seam"></i><h3>Alat tidak ditemukan</h3><p>Ubah kata kunci pencarian atau tambahkan alat baru.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if ($items->hasPages())<div class="d-flex justify-content-center mt-4">{{ $items->links() }}</div>@endif
@endsection
