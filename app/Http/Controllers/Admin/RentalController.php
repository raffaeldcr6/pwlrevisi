<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class RentalController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $search = trim((string) $request->query('q'));

        $rentals = Rental::with(['user', 'items'])
            ->when($status && isset(Rental::STATUSES[$status]), fn ($q) => $q->where('status', $status))
            ->when($search !== '', fn ($q) => $q->where(function ($w) use ($search) {
                $w->where('code', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            }))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $counts = Rental::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('admin.rentals.index', compact('rentals', 'status', 'search', 'counts'));
    }

    public function show(Rental $rental): View
    {
        $rental->load(['user', 'items.equipment.category']);

        return view('admin.rentals.show', compact('rental'));
    }

    /** UPDATE: ubah status penyewaan sesuai alur yang diizinkan. */
    public function updateStatus(Request $request, Rental $rental): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Rental::STATUSES))],
            'admin_note' => ['nullable', 'string', 'max:500'],
        ]);

        if (! in_array($data['status'], $rental->nextStatuses(), true)) {
            return back()->with('error', 'Perubahan status tersebut tidak diizinkan dari status saat ini.');
        }

        try {
            $rental->update([
                'status' => $data['status'],
                'admin_note' => $data['admin_note'] ?? $rental->admin_note,
            ]);
        } catch (Throwable $e) {
            report($e);

            return back()->with('error', 'Status gagal diperbarui. Silakan coba lagi.');
        }

        return back()->with('success', "Status {$rental->code} diubah menjadi ".Rental::STATUSES[$data['status']].'.');
    }

    /** DELETE: hapus riwayat yang sudah selesai / ditolak / dibatalkan. */
    public function destroy(Rental $rental): RedirectResponse
    {
        if (! $rental->isDeletableByAdmin()) {
            return back()->with('error', 'Penyewaan yang masih berjalan tidak bisa dihapus. Selesaikan atau batalkan dulu.');
        }

        try {
            $code = $rental->code;
            $rental->delete();
        } catch (Throwable $e) {
            report($e);

            return back()->with('error', 'Penyewaan gagal dihapus. Silakan coba lagi.');
        }

        return redirect()->route('admin.rentals.index')->with('success', "Data penyewaan {$code} dihapus.");
    }
}
