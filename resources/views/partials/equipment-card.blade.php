@php
    $unavailable = $equipment->status !== \App\Models\Equipment::STATUS_AVAILABLE || $equipment->stock < 1;
    $isAdmin = auth()->check() && auth()->user()->isAdmin();
@endphp
<article class="eq-card">
    <a href="{{ route('catalog.show', $equipment) }}" class="thumb tone-{{ $equipment->category->tone }}" aria-label="Lihat {{ $equipment->name }}">
        @if ($equipment->image_url)
            <img src="{{ $equipment->image_url }}" alt="{{ $equipment->name }}" loading="lazy">
        @else
            <i class="bi {{ $equipment->category->icon }}"></i>
        @endif
        @if ($equipment->status === 'maintenance')
            <span class="rv-badge st-maintenance badge-stock">Perawatan</span>
        @elseif ($equipment->stock < 1)
            <span class="rv-badge st-rejected badge-stock">Habis</span>
        @elseif ($equipment->stock <= 3)
            <span class="rv-badge st-low badge-stock">Sisa {{ $equipment->stock }}</span>
        @endif
    </a>
    <div class="eq-body">
        <div class="eq-meta">{{ $equipment->category->name }}</div>
        <h3><a href="{{ route('catalog.show', $equipment) }}">{{ $equipment->name }}</a></h3>
        <div class="eq-meta">Stok {{ $equipment->stock }} unit</div>
        <div class="eq-foot">
            <div class="price-tag">{{ $equipment->price_formatted }} <small>/hari</small></div>
            @if ($unavailable || $isAdmin)
                <a href="{{ route('catalog.show', $equipment) }}" class="btn btn-sm btn-outline-pine">Detail</a>
            @else
                <a href="{{ route('customer.rentals.create', ['alat' => $equipment->id]) }}" class="btn btn-sm btn-pine">Sewa</a>
            @endif
        </div>
    </div>
</article>
