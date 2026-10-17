<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('code') @yield('title') | RentVentures</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/favicon.svg') }}">
    @include('partials.head-assets')
</head>
<body>
<main class="err-page">
    <div class="err-card text-center">
        <a href="{{ url('/') }}" class="rv-brand mb-4 justify-content-center">@include('partials.logo') RentVentures</a>
        <div class="err-code" aria-hidden="true">@yield('code_html')</div>
        <h1 class="h3 mt-3 mb-2">@yield('title')</h1>
        <p class="text-stone mx-auto mb-4" style="max-width: 42ch">@yield('message')</p>
        <div class="d-flex flex-wrap justify-content-center gap-2">
            <a href="{{ url('/') }}" class="btn btn-pine btn-lg">Ke beranda</a>
            @hasSection('retry')
                <a href="javascript:location.reload()" class="btn btn-outline-pine btn-lg">Muat ulang</a>
            @else
                <a href="javascript:history.back()" class="btn btn-outline-pine btn-lg">Halaman sebelumnya</a>
            @endif
        </div>
    </div>
</main>
</body>
</html>
