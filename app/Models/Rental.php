<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rental extends Model
{
    public const PENDING   = 'pending';
    public const APPROVED  = 'approved';
    public const RENTED    = 'rented';
    public const RETURNED  = 'returned';
    public const REJECTED  = 'rejected';
    public const CANCELLED = 'cancelled';

    public const STATUSES = [
        self::PENDING   => 'Menunggu',
        self::APPROVED  => 'Disetujui',
        self::RENTED    => 'Sedang disewa',
        self::RETURNED  => 'Dikembalikan',
        self::REJECTED  => 'Ditolak',
        self::CANCELLED => 'Dibatalkan',
    ];

    /** Status yang masih menahan stok alat. */
    public const HOLDING_STATUSES = [self::PENDING, self::APPROVED, self::RENTED];

    /** Perubahan status yang diizinkan untuk admin. */
    public const TRANSITIONS = [
        self::PENDING  => [self::APPROVED, self::REJECTED],
        self::APPROVED => [self::RENTED, self::CANCELLED],
        self::RENTED   => [self::RETURNED],
    ];

    /** Denda keterlambatan per hari dalam persen dari total harga. */
    public const LATE_FEE_PERCENT_PER_DAY = 10;

    protected $fillable = [
        'user_id', 'code', 'start_date', 'end_date', 'total_price', 'status',
        'note', 'admin_note', 'condition_out', 'condition_in', 'late_fee', 'returned_at',
    ];

    protected function casts(): array
    {
        return [
            'start_date'  => 'date',
            'end_date'    => 'date',
            'returned_at' => 'date',
            'total_price' => 'integer',
            'late_fee'    => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(RentalItem::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(AppNotification::class);
    }

    /** Jumlah hari sewa (hari mulai dan hari selesai sama-sama dihitung). */
    public static function countDays($start, $end): int
    {
        $a = Carbon::parse($start)->startOfDay();
        $b = Carbon::parse($end)->startOfDay();

        return (int) round($a->diffInDays($b, false)) + 1;
    }

    public function getDaysAttribute(): int
    {
        return self::countDays($this->start_date, $this->end_date);
    }

    public function getTotalFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }

    public function getLateFeeFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->late_fee, 0, ',', '.');
    }

    public function getGrandTotalAttribute(): int
    {
        return $this->total_price + $this->late_fee;
    }

    public function getGrandTotalFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->grand_total, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getTotalQtyAttribute(): int
    {
        return (int) $this->items->sum('qty');
    }

    public function isPending(): bool
    {
        return $this->status === self::PENDING;
    }

    /** Status berikutnya yang boleh dipilih admin. */
    public function nextStatuses(): array
    {
        return self::TRANSITIONS[$this->status] ?? [];
    }

    /** Hanya riwayat selesai yang boleh dihapus. */
    public function isDeletableByAdmin(): bool
    {
        return in_array($this->status, [self::RETURNED, self::REJECTED, self::CANCELLED], true);
    }

    /**
     * Hitung denda keterlambatan berdasarkan tanggal pengembalian aktual.
     * Denda = LATE_FEE_PERCENT_PER_DAY% dari total_price per hari terlambat.
     */
    public function calculateLateFee(?string $returnedAt = null): int
    {
        $returned = Carbon::parse($returnedAt ?? now());
        $due      = Carbon::parse($this->end_date)->startOfDay();

        $lateDays = (int) max(0, $due->diffInDays($returned->startOfDay(), false) * -1);
        // diffInDays dengan false: positif jika returned sebelum due, negatif jika setelah due
        // Kita pakai: jika returned > due, maka terlambat
        $actualLate = $returned->startOfDay()->gt($due)
            ? (int) $due->diffInDays($returned->startOfDay())
            : 0;

        if ($actualLate <= 0) {
            return 0;
        }

        return (int) round($this->total_price * (self::LATE_FEE_PERCENT_PER_DAY / 100) * $actualLate);
    }
}
