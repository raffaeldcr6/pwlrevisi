@extends('layouts.admin')

@section('title', 'Penyewaan '.$rental->code)
@section('heading', 'Penyewaan '.$rental->code)
@section('actions')
    <a href="{{ route('admin.rentals.index') }}" class="btn btn-outline-pine">Kembali ke daftar</a>
@endsection

@section('content')
@php
    $next = $rental->nextStatuses();
    $labels = [
        'approved' => ['Setujui pengajuan', 'btn-pine'],
        'rejected' => ['Tolak pengajuan', 'btn-outline-danger'],
        'rented' => ['Alat sudah diambil', 'btn-pine'],
        'returned' => ['Alat sudah dikembalikan', 'btn-pine'],
        'cancelled' => ['Batalkan penyewaan', 'btn-outline-danger'],
    ];
@endphp
<div class="row g-4">
    <div class="col-xl-8">
        <div class="panel panel-flush mb-4">
            <div class="panel-head"><h2>Alat yang disewa</h2><span class="rv-badge st-{{ $rental->status }}">{{ $rental->status_label }}</span></div>
            <div class="table-responsive">
                <table class="table rv-table">
                    <thead><tr><th>Alat</th><th class="text-end">Harga/hari</th><th class="text-center">Jumlah</th><th class="text-end">Subtotal</th></tr></thead>
                    <tbody>
                        @foreach ($rental->items as $line)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="table-thumb tone-{{ $line->equipment->category->tone }}"><i class="bi {{ $line->equipment->category->icon }}"></i></span>
                                        <div><div class="fw-semibold">{{ $line->equipment->name }}</div><small class="text-stone">{{ $line->equipment->category->name }}</small></div>
                                    </div>
                                </td>
                                <td class="text-end">Rp {{ number_format($line->price_per_day, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $line->qty }}</td>
                                <td class="text-end fw-semibold">Rp {{ number_format($line->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot><tr><td colspan="3" class="text-end fw-semibold">Total ({{ $rental->days }} hari)</td><td class="text-end price-tag">{{ $rental->total_formatted }}</td></tr></tfoot>
                </table>
            </div>
        </div>

        <div class="panel">
            <h2 class="h5 mb-3">Perbarui status</h2>
            @if (count($next))
                <form method="POST" action="{{ route('admin.rentals.status', $rental) }}" novalidate>
                    @csrf @method('PATCH')
                    <div class="mb-3">
                        <label for="admin_note" class="form-label">Catatan untuk customer <span class="text-stone fw-normal">(opsional)</span></label>
                        <textarea id="admin_note" name="admin_note" rows="2" class="form-control @error('admin_note') is-invalid @enderror" placeholder="Contoh: alat bisa diambil mulai jam 09.00.">{{ old('admin_note', $rental->admin_note) }}</textarea>
                        @error('admin_note')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @error('status')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach ($next as $st)
                            <button type="submit" name="status" value="{{ $st }}" class="btn {{ $labels[$st][1] }}">{{ $labels[$st][0] }}</button>
                        @endforeach
                    </div>
                </form>
            @else
                <p class="text-stone mb-0">Penyewaan ini sudah final ({{ strtolower($rental->status_label) }}). Status tidak bisa diubah lagi.</p>
            @endif
        </div>
    </div>

    <div class="col-xl-4">
        <div class="panel mb-4">
            <h2 class="h5 mb-3">Customer</h2>
            <dl class="detail-list">
                <dt>Nama</dt><dd>{{ $rental->user->name }}</dd>
                <dt>Email</dt><dd>{{ $rental->user->email }}</dd>
                <dt>Telepon</dt><dd>{{ $rental->user->phone ?: '-' }}</dd>
                <dt>Alamat</dt><dd>{{ $rental->user->address ?: '-' }}</dd>
            </dl>
        </div>
        <div class="panel">
            <h2 class="h5 mb-3">Detail sewa</h2>
            <dl class="detail-list">
                <dt>Mulai</dt><dd>{{ $rental->start_date->translatedFormat('d M Y') }}</dd>
                <dt>Selesai</dt><dd>{{ $rental->end_date->translatedFormat('d M Y') }}</dd>
                <dt>Lama</dt><dd>{{ $rental->days }} hari</dd>
                <dt>Diajukan</dt><dd>{{ $rental->created_at->translatedFormat('d M Y, H:i') }}</dd>
                <dt>Catatan</dt><dd>{{ $rental->note ?: '-' }}</dd>
            </dl>
            @if ($rental->isDeletableByAdmin())
                <form action="{{ route('admin.rentals.destroy', $rental) }}" method="POST" class="js-confirm mt-3" data-title="Hapus data penyewaan?" data-text="Riwayat {{ $rental->code }} akan dihapus permanen." data-ok="Ya, hapus">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger w-100" type="submit"><i class="bi bi-trash me-1"></i>Hapus riwayat</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
