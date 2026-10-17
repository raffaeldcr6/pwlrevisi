<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'icon', 'description'];

    /** Pilihan ikon (Bootstrap Icons) untuk kategori. */
    public const ICONS = [
        'bi-triangle'    => 'Tenda',
        'bi-backpack'    => 'Tas',
        'bi-moon-stars'  => 'Tidur',
        'bi-fire'        => 'Masak / api',
        'bi-lightbulb'   => 'Penerangan',
        'bi-compass'     => 'Navigasi',
        'bi-umbrella'    => 'Pelindung cuaca',
        'bi-tools'       => 'Peralatan',
    ];

    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class);
    }

    /** Nomor warna (1-6) untuk thumbnail, konsisten per kategori. */
    public function getToneAttribute(): int
    {
        return (($this->id ?? 1) % 6) + 1;
    }
}
