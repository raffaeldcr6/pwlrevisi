<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalItem extends Model
{
    protected $fillable = ['rental_id', 'equipment_id', 'qty', 'price_per_day', 'subtotal'];

    public function rental(): BelongsTo
    {
        return $this->belongsTo(Rental::class);
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }
}
