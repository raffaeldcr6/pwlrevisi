@if (session('success'))
    <div class="rv-alert ok alert-autodismiss mb-3" role="alert">
        <i class="bi bi-check-circle-fill flex-none" style="font-size:1.2rem;margin-top:.05rem"></i>
        <div>{{ session('success') }}</div>
    </div>
@endif
@if (session('error'))
    <div class="rv-alert err alert-autodismiss mb-3" role="alert">
        <i class="bi bi-exclamation-octagon-fill flex-none" style="font-size:1.2rem;margin-top:.05rem"></i>
        <div>{{ session('error') }}</div>
    </div>
@endif
@if (session('info'))
    <div class="rv-alert info alert-autodismiss mb-3" role="alert">
        <i class="bi bi-info-circle-fill flex-none" style="font-size:1.2rem;margin-top:.05rem"></i>
        <div>{{ session('info') }}</div>
    </div>
@endif
@if ($errors->any() && !$errors->has('items'))
    <div class="rv-alert err mb-3" role="alert">
        <i class="bi bi-exclamation-octagon-fill flex-none" style="font-size:1.2rem;margin-top:.05rem"></i>
        <div>
            <strong>Mohon perbaiki kesalahan berikut:</strong>
            <ul class="mb-0 mt-1 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
