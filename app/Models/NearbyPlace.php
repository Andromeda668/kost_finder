<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NearbyPlace extends Model
{
    use HasFactory;

    protected $fillable = [
        'kost_id',
        'label',
        'category',
        'distance_km',
        'google_maps_link',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'distance_km' => 'decimal:2',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    public function kost(): BelongsTo
    {
        return $this->belongsTo(Kost::class);
    }
}

