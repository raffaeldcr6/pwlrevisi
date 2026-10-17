@extends('layouts.admin')

@section('title', 'Kategori')
@section('heading', 'Kategori')
@section('subheading', 'Kelompok alat yang tampil di katalog.')
@section('actions')
    <a href="{{ route('admin.categories.create') }}" class="btn btn-pine"><i class="bi bi-plus-lg me-1"></i>Tambah kategori</a>
@endsection

@section('content')
<div class="panel panel-flush">
    <div class="table-responsive">
        <table class="table rv-table">
            <thead><tr><th>Kategori</th><th>Deskripsi</th><th class="text-center">Jumlah alat</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                @forelse ($categories as $cat)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <span class="table-thumb tone-{{ $cat->tone }}"><i class="bi {{ $cat->icon }}"></i></span>
                                <span class="fw-semibold">{{ $cat->name }}</span>
                            </div>
                        </td>
                        <td class="text-stone" style="max-width: 340px">{{ $cat->description ?: '-' }}</td>
                        <td class="text-center">{{ $cat->equipment_count }}</td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('admin.categories.edit', $cat) }}" class="btn btn-sm btn-outline-pine">Ubah</a>
                                <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" class="js-confirm" data-title="Hapus kategori?" data-text="Kategori &quot;{{ $cat->name }}&quot; akan dihapus." data-ok="Ya, hapus">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4"><div class="empty"><i class="bi bi-tags"></i><h3>Belum ada kategori</h3><p>Tambahkan kategori pertama sebelum menambah alat.</p><a href="{{ route('admin.categories.create') }}" class="btn btn-pine">Tambah kategori</a></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
