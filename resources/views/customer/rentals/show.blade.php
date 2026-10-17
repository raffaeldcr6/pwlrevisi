@extends('layouts.app')

@section('title', 'Penyewaan '.$rental->code)

@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('customer.rentals.index') }}">Penyewaan saya</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $rental->code }}</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
        <div>
            <h1 class="display-font mb-2" style="font-size: clamp(2rem, 4vw, 2.6rem)">{{ $rental->code }}</h1>
            <span class="rv-badge st-{{ $rental->status }}">{{ $rental->status_label }}</span>
        </div>
        @if ($rental->isPending())
            <div class="d-flex gap-2">
                <a href="{{ route('customer.rentals.edit', $rental) }}" class="btn btn-pine"><i class="bi bi-pencil me-1"></i>Ubah</a>
                <form action="{{ route('customer.rentals.destroy', $rental) }}" method="POST" class="js-confirm" data-title="Batalkan pengajuan?" data-text="Pengajuan {{ $rental->code }} akan dihapus." data-ok="Ya, batalkan">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger" type="submit"><i class="bi bi-x-circle me-1"></i>Batalkan</button>
                </form>
            </div>
        @endif
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="panel panel-flush">
                <div class="panel-head"><h2>Alat yang disewa</h2></div>
                <div class="table-responsive">
                    <table class="table rv-table">
                        <thead><tr><th>Alat</th><th class="text-end">Harga/hari</th><th class="text-center">Jumlah</th><th class="text-end">Subtotal</th></tr></thead>
                        <tbody>
                            @foreach ($rental->items as $line)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="table-thumb tone-{{ $line->equipment->category->tone }}">
                                                @if ($line->equipment->image_url)<img src="{{ $line->equipment->image_url }}" alt="">@else<i class="bi {{ $line->equipment->category->icon }}"></i>@endif
                                            </span>
                                            <div><div class="fw-semibold">{{ $line->equipment->name }}</div><small class="text-stone">{{ $line->equipment->category->name }}</small></div>
                                        </div>
                                    </td>
                                    <td class="text-end">Rp {{ number_format($line->price_per_day, 0, ',', '.') }}</td>
                                    <td class="text-center">{{ $line->qty }}</td>
                                    <td class="text-end fw-semibold">Rp {{ number_format($line->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr><td colspan="3" class="text-end fw-semibold">Total ({{ $rental->days }} hari)</td><td class="text-end price-tag">{{ $rental->total_formatted }}</td></tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="panel">
                <h2 class="h5 mb-3">Detail</h2>
                <dl class="detail-list">
                    <dt>Mulai</dt><dd>{{ $rental->start_date->translatedFormat('l, d F Y') }}</dd>
                    <dt>Selesai</dt><dd>{{ $rental->end_date->translatedFormat('l, d F Y') }}</dd>
                    <dt>Lama sewa</dt><dd>{{ $rental->days }} hari</dd>
                    <dt>Diajukan</dt><dd>{{ $rental->created_at->translatedFormat('d M Y, H:i') }}</dd>
                    @if ($rental->note)<dt>Catatanmu</dt><dd>{{ $rental->note }}</dd>@endif
                    @if ($rental->admin_note)<dt>Catatan admin</dt><dd>{{ $rental->admin_note }}</dd>@endif
                </dl>
            </div>
            @if ($rental->status === 'approved')
                <div class="rv-alert ok mt-3"><i class="bi bi-geo-alt-fill"></i><div>Pengajuan disetujui. Ambil alat di basecamp pada tanggal mulai sewa.</div></div>
            @elseif ($rental->status === 'rejected')
                <div class="rv-alert err mt-3"><i class="bi bi-x-octagon-fill"></i><div>Pengajuan ditolak. Lihat catatan admin, lalu ajukan ulang bila perlu.</div></div>
            @endif
        </div>
    </div>
</div>
@endsection
