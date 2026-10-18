<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipment extends Model
{
    protected $table = 'equipment';

    public const STATUS_AVAILABLE  = 'available';
    public const STATUS_MAINTENANCE = 'maintenance';

    public const STATUSES = [
        self::STATUS_AVAILABLE   => 'Tersedia',
        self::STATUS_MAINTENANCE => 'Perawatan',
    ];

    public const CONDITION_NEW  = 'new';
    public const CONDITION_GOOD = 'good';
    public const CONDITION_FAIR = 'fair';
    public const CONDITION_POOR = 'poor';

    public const CONDITIONS = [
        self::CONDITION_NEW  => 'Baru',
        self::CONDITION_GOOD => 'Baik',
        self::CONDITION_FAIR => 'Cukup Baik',
        self::CONDITION_POOR => 'Perlu Perhatian',
    ];

    protected $fillable = [
        'category_id', 'name', 'description', 'specifications',
        'price_per_day', 'stock', 'image', 'status', 'condition',
    ];

    protected function casts(): array
    {
        return [
            'price_per_day' => 'integer',
            'stock'         => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function rentalItems(): HasMany
    {
        return $this->hasMany(RentalItem::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function getPriceFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->price_per_day, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getConditionLabelAttribute(): string
    {
        return self::CONDITIONS[$this->condition] ?? $this->condition;
    }

    public function isRentable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE && $this->stock > 0;
    }

    /**
     * Jumlah unit yang masih bisa disewa pada rentang tanggal tertentu.
     * Penyewaan berstatus menunggu / disetujui / sedang disewa dianggap menahan stok.
     */
    public function availableBetween(string $start, string $end, ?int $ignoreRentalId = null): int
    {
        $booked = RentalItem::query()
            ->where('equipment_id', $this->id)
            ->whereHas('rental', function ($q) use ($start, $end, $ignoreRentalId) {
                $q->whereIn('status', Rental::HOLDING_STATUSES)
                    ->whereDate('start_date', '<=', $end)
                    ->whereDate('end_date', '>=', $start);

                if ($ignoreRentalId) {
                    $q->where('id', '!=', $ignoreRentalId);
                }
            })
            ->sum('qty');

        return max(0, $this->stock - (int) $booked);
    }
}
