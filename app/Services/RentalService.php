<?php

namespace App\Services;

use App\Exceptions\RentalException;
use App\Models\Equipment;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RentalService
{
    /**
     * Buat penyewaan baru beserta item-itemnya.
     *
     * @param  array{start_date:string,end_date:string,note:?string,items:array<int|string,mixed>}  $data
     */
    public function create(User $user, array $data): Rental
    {
        return DB::transaction(function () use ($user, $data) {
            $rental = new Rental([
                'user_id' => $user->id,
                'code' => $this->generateCode(),
                'status' => Rental::PENDING,
            ]);

            return $this->fill($rental, $data);
        });
    }

    /** Perbarui penyewaan (hanya boleh saat status masih menunggu). */
    public function update(Rental $rental, array $data): Rental
    {
        if (! $rental->isPending()) {
            throw new RentalException('Penyewaan yang sudah diproses admin tidak bisa diubah lagi.');
        }

        return DB::transaction(fn () => $this->fill($rental, $data));
    }

    private function fill(Rental $rental, array $data): Rental
    {
        $start = $data['start_date'];
        $end = $data['end_date'];
        $days = Rental::countDays($start, $end);

        if ($days < 1) {
            throw new RentalException('Tanggal selesai tidak boleh lebih awal dari tanggal mulai.');
        }

        // Ambil hanya alat dengan jumlah > 0
        $requested = collect($data['items'] ?? [])
            ->map(fn ($qty) => (int) $qty)
            ->filter(fn ($qty) => $qty > 0);

        if ($requested->isEmpty()) {
            throw new RentalException('Pilih minimal satu alat dengan jumlah lebih dari 0.');
        }

        // Kunci baris alat agar dua penyewaan bersamaan tidak melebihi stok.
        $equipment = Equipment::query()
            ->whereIn('id', $requested->keys())
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        $lines = [];
        $total = 0;

        foreach ($requested as $equipmentId => $qty) {
            $item = $equipment->get($equipmentId);

            if (! $item) {
                throw new RentalException('Ada alat yang dipilih sudah tidak tersedia di katalog.');
            }

            if ($item->status !== Equipment::STATUS_AVAILABLE) {
                throw new RentalException("{$item->name} sedang dalam perawatan dan belum bisa disewa.");
            }

            $free = $item->availableBetween($start, $end, $rental->exists ? $rental->id : null);

            if ($qty > $free) {
                $msg = $free > 0
                    ? "Stok {$item->name} pada tanggal tersebut tersisa {$free} unit. Kurangi jumlahnya atau ganti tanggal."
                    : "{$item->name} sudah habis disewa pada tanggal tersebut. Pilih tanggal lain.";
                throw new RentalException($msg);
            }

            $subtotal = $item->price_per_day * $qty * $days;
            $total += $subtotal;

            $lines[] = [
                'equipment_id' => $item->id,
                'qty' => $qty,
                'price_per_day' => $item->price_per_day,
                'subtotal' => $subtotal,
            ];
        }

        $rental->fill([
            'start_date' => $start,
            'end_date' => $end,
            'note' => $data['note'] ?? null,
            'total_price' => $total,
        ])->save();

        // Ganti seluruh item dengan yang baru
        $rental->items()->delete();
        $rental->items()->createMany($lines);

        return $rental->load('items.equipment');
    }

    private function generateCode(): string
    {
        do {
            $code = 'RV-'.strtoupper(Str::random(6));
        } while (Rental::where('code', $code)->exists());

        return $code;
    }
}
