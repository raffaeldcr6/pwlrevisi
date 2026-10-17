@extends('errors.layout')
@php
    $status = $status ?? 500;
    $info = match (true) {
        $status === 400 => ['Permintaan tidak valid', 'Permintaan yang dikirim tidak bisa diproses. Periksa data yang dimasukkan lalu coba lagi.'],
        $status === 401 => ['Perlu masuk', 'Masuk ke akunmu terlebih dahulu untuk membuka halaman ini.'],
        $status === 405 => ['Aksi tidak diizinkan', 'Halaman ini tidak bisa dibuka dengan cara tersebut. Kembali ke beranda dan ulangi dari menu.'],
        $status === 413 => ['Berkas terlalu besar', 'Ukuran berkas melebihi batas yang diizinkan. Pilih berkas yang lebih kecil.'],
        $status >= 500 => ['Terjadi kesalahan di server', 'Masalah ini sudah kami catat. Coba lagi beberapa saat lagi.'],
        default => ['Halaman tidak dapat dibuka', 'Terjadi kendala saat membuka halaman ini. Kembali ke beranda dan coba lagi.'],
    };
@endphp
@section('code', $status)
@section('code_html', $status)
@section('title', $info[0])
@section('message', $info[1])
