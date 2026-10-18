@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-end flex-wrap gap-3 mb-4">
        <div>
            <h1 class="display-font" style="font-size: clamp(2rem, 4vw, 2.6rem)">Notifikasi</h1>
            <p class="text-stone mb-0">Semua pemberitahuan terkait penyewaan kamu.</p>
        </div>
    </div>

    @forelse ($notifications as $notif)
        <div class="panel mb-3 {{ $notif->isRead() ? 'opacity-75' : '' }}" style="{{ !$notif->isRead() ? 'border-left: 3px solid var(--rv-pine)' : '' }}">
            <div class="d-flex gap-3 align-items-start">
                <div style="width:40px;height:40px;border-radius:50%;background:{{ $notif->isRead() ? 'var(--rv-mist)' : 'var(--rv-pine)' }};color:{{ $notif->isRead() ? 'var(--rv-stone)' : '#fff' }};display:grid;place-items:center;flex:none">
                    <i class="bi bi-bell{{ $notif->isRead() ? '' : '-fill' }}"></i>
                </div>
                <div class="flex-fill">
                    <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                        <strong class="{{ $notif->isRead() ? 'text-stone' : '' }}">{{ $notif->title }}</strong>
                        <small class="text-stone text-nowrap">{{ $notif->created_at->diffForHumans() }}</small>
                    </div>
                    <p class="mb-0 mt-1 text-stone" style="font-size:.95rem">{{ $notif->body }}</p>
                    @if ($notif->rental_id)
                        <a href="{{ route('customer.rentals.show', $notif->rental_id) }}" class="btn btn-sm btn-outline-pine mt-2">
                            <i class="bi bi-arrow-right me-1"></i>Lihat penyewaan
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="panel empty">
            <i class="bi bi-bell-slash"></i>
            <h3>Belum ada notifikasi</h3>
            <p>Notifikasi akan muncul saat admin memproses penyewaanmu.</p>
            <a href="{{ route('catalog.index') }}" class="btn btn-pine">Buka katalog</a>
        </div>
    @endforelse

    @if ($notifications->hasPages())
        <div class="d-flex justify-content-center mt-4">{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
