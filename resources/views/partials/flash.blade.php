@if (session('success'))
    <div class="rv-alert ok mb-4" role="status">
        <i class="bi bi-check-circle-fill"></i>
        <div>{{ session('success') }}</div>
    </div>
@endif
@if (session('error'))
    <div class="rv-alert err mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <div>{{ session('error') }}</div>
    </div>
@endif
