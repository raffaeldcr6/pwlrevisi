@extends('layouts.admin')

@php($editing = $category->exists)

@section('title', $editing ? 'Ubah Kategori' : 'Tambah Kategori')
@section('heading', $editing ? 'Ubah kategori' : 'Tambah kategori')

@section('content')
<div class="row">
    <div class="col-xl-7">
        <form method="POST" action="{{ $editing ? route('admin.categories.update', $category) : route('admin.categories.store') }}" class="panel" novalidate>
            @csrf
            @if ($editing) @method('PUT') @endif

            <div class="mb-3">
                <label for="name" class="form-label">Nama kategori</label>
                <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" class="form-control @error('name') is-invalid @enderror" required autofocus>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label d-block">Ikon</label>
                <div class="d-flex flex-wrap gap-2">
                    @foreach (\App\Models\Category::ICONS as $icon => $label)
                        <div>
                            <input type="radio" class="btn-check" name="icon" id="icon-{{ $icon }}" value="{{ $icon }}" @checked(old('icon', $category->icon ?: 'bi-compass') === $icon)>
                            <label class="btn btn-outline-pine" for="icon-{{ $icon }}" title="{{ $label }}"><i class="bi {{ $icon }} me-1"></i>{{ $label }}</label>
                        </div>
                    @endforeach
                </div>
                @error('icon')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>

            <div class="mb-4">
                <label for="description" class="form-label">Deskripsi singkat <span class="text-stone fw-normal">(opsional)</span></label>
                <input type="text" id="description" name="description" value="{{ old('description', $category->description) }}" class="form-control @error('description') is-invalid @enderror">
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-pine" data-busy="Menyimpan...">{{ $editing ? 'Simpan perubahan' : 'Simpan kategori' }}</button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-pine">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
