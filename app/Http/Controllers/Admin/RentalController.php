<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Rental;
use App\Models\User;
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
            'status'        => ['required', Rule::in(array_keys(Rental::STATUSES))],
            'admin_note'    => ['nullable', 'string', 'max:500'],
            'condition_out' => ['nullable', 'string', 'max:500'],
            'condition_in'  => ['nullable', 'string', 'max:500'],
            'returned_at'   => ['nullable', 'date'],
        ]);

        if (! in_array($data['status'], $rental->nextStatuses(), true)) {
            return back()->with('error', 'Perubahan status tersebut tidak diizinkan dari status saat ini.');
        }

        try {
            $updateData = [
                'status'     => $data['status'],
                'admin_note' => $data['admin_note'] ?? $rental->admin_note,
            ];

            // Catat kondisi saat diantar (RENTED)
            if ($data['status'] === Rental::RENTED && isset($data['condition_out'])) {
                $updateData['condition_out'] = $data['condition_out'];
            }

            // Catat kondisi saat dikembalikan + hitung denda
            if ($data['status'] === Rental::RETURNED) {
                if (isset($data['condition_in'])) {
                    $updateData['condition_in'] = $data['condition_in'];
                }

                $returnedAt = $data['returned_at'] ?? now()->toDateString();
                $updateData['returned_at'] = $returnedAt;

                // Hitung denda keterlambatan
                $lateFee = $rental->calculateLateFee($returnedAt);
                $updateData['late_fee'] = $lateFee;
            }

            $rental->update($updateData);

            // Kirim notifikasi ke customer
            $this->sendNotification($rental, $data['status']);

        } catch (Throwable $e) {
            report($e);
            return back()->with('error', 'Status gagal diperbarui. Silakan coba lagi.');
        }

        $label = Rental::STATUSES[$data['status']];
        return back()->with('success', "Status {$rental->code} diubah menjadi {$label}.");
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

    private function sendNotification(Rental $rental, string $newStatus): void
    {
        $map = [
            Rental::APPROVED  => [AppNotification::TYPE_RENTAL_APPROVED, 'Pengajuan Disetujui 🎉', "Pengajuan sewa {$rental->code} kamu telah disetujui. Ambil alat di basecamp pada tanggal mulai sewa."],
            Rental::REJECTED  => [AppNotification::TYPE_RENTAL_REJECTED, 'Pengajuan Ditolak', "Maaf, pengajuan sewa {$rental->code} tidak dapat disetujui. Lihat catatan admin untuk keterangan lebih lanjut."],
            Rental::RENTED    => [AppNotification::TYPE_RENTAL_RENTED, 'Barang Sudah Diambil', "Alat pada penyewaan {$rental->code} sudah dalam perjalanan bersamamu. Selamat mendaki!"],
            Rental::RETURNED  => [AppNotification::TYPE_RENTAL_RETURNED, 'Penyewaan Selesai ✅', "Penyewaan {$rental->code} sudah selesai. Terima kasih sudah menyewa di RentVentures!"],
        ];

        if (! isset($map[$newStatus])) return;

        [$type, $title, $body] = $map[$newStatus];

        AppNotification::create([
            'user_id'   => $rental->user_id,
            'type'      => $type,
            'title'     => $title,
            'body'      => $body,
            'rental_id' => $rental->id,
        ]);
    }
}
