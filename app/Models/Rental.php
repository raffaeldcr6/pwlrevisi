<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rental extends Model
{
    public const PENDING = 'pending';
    public const APPROVED = 'approved';
    public const RENTED = 'rented';
    public const RETURNED = 'returned';
    public const REJECTED = 'rejected';
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

    protected $fillable = [
        'user_id', 'code', 'start_date', 'end_date', 'total_price', 'status', 'note', 'admin_note',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'total_price' => 'integer',
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
        return 'Rp '.number_format($this->total_price, 0, ',', '.');
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
}
