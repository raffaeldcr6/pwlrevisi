<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppNotification extends Model
{
    protected $table = 'notifications';

    public const TYPE_RENTAL_CREATED  = 'rental_created';
    public const TYPE_RENTAL_APPROVED = 'rental_approved';
    public const TYPE_RENTAL_REJECTED = 'rental_rejected';
    public const TYPE_RENTAL_RENTED   = 'rental_rented';
    public const TYPE_RENTAL_RETURNED = 'rental_returned';

    protected $fillable = ['user_id', 'type', 'title', 'body', 'rental_id', 'read_at'];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rental(): BelongsTo
    {
        return $this->belongsTo(Rental::class);
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }
}
