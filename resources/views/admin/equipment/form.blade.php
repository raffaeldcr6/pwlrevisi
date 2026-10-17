@extends('layouts.admin')

@php($editing = $equipment->exists)

@section('title', $editing ? 'Ubah Alat' : 'Tambah Alat')
@section('heading', $editing ? 'Ubah alat' : 'Tambah alat')

@section('content')
<form method="POST" action="{{ $editing ? route('admin.equipment.update', $equipment) : route('admin.equipment.store') }}" enctype="multipart/form-data" novalidate>
    @csrf
    @if ($editing) @method('PUT') @endif

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="panel">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label for="name" class="form-label">Nama alat</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $equipment->name) }}" class="form-control @error('name') is-invalid @enderror" required autofocus>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="category_id" class="form-label">Kategori</label>
                        <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="">Pilih kategori</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" @selected((string) old('category_id', $equipment->category_id) === (string) $cat->id)>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="price_per_day" class="form-label">Harga per hari (Rp)</label>
                        <input type="number" id="price_per_day" name="price_per_day" value="{{ old('price_per_day', $equipment->price_per_day) }}" min="1000" step="500" class="form-control @error('price_per_day') is-invalid @enderror" required>
                        @error('price_per_day')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="stock" class="form-label">Stok (unit)</label>
                        <input type="number" id="stock" name="stock" value="{{ old('stock', $equipment->stock) }}" min="0" class="form-control @error('stock') is-invalid @enderror" required>
                        @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
                            @foreach (\App\Models\Equipment::STATUSES as $key => $label)
                                <option value="{{ $key }}" @selected(old('status', $equipment->status) === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea id="description" name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $equipment->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="panel">
                <label for="image-input" class="form-label">Foto alat <span class="text-stone fw-normal">(opsional)</span></label>
                @if ($editing && $equipment->image_url)
                    <img src="{{ $equipment->image_url }}" alt="Foto {{ $equipment->name }}" class="img-fluid rounded-3 mb-2 border">
                @endif
                <img id="image-preview" src="" alt="Pratinjau foto baru" class="img-fluid rounded-3 mb-2 border d-none">
                <input type="file" id="image-input" name="image" accept="image/png,image/jpeg,image/webp" class="form-control @error('image') is-invalid @enderror">
                @error('image')<div class="invalid-feedback">{{ $message }}</div>@else<div class="form-text">JPG, PNG, atau WebP. Maksimal 2 MB. Tanpa foto, katalog memakai ikon kategori.</div>@enderror

                @if ($editing && $equipment->image)
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="remove_image" id="remove_image" value="1">
                        <label class="form-check-label" for="remove_image">Hapus foto saat ini</label>
                    </div>
                @endif
            </div>

            <div class="d-grid gap-2 mt-3">
                <button type="submit" class="btn btn-pine btn-lg" data-busy="Menyimpan...">{{ $editing ? 'Simpan perubahan' : 'Simpan alat' }}</button>
                <a href="{{ route('admin.equipment.index') }}" class="btn btn-outline-pine">Batal</a>
            </div>
        </div>
    </div>
</form>
@endsection
