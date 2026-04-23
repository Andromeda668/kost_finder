<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_ACCEPTED = 'diterima';
    public const STATUS_REJECTED = 'ditolak';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'kost_id',
        'tanggal_masuk',
        'durasi_bulan',
        'status',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_masuk' => 'date',
            'durasi_bulan' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kost(): BelongsTo
    {
        return $this->belongsTo(Kost::class);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_ACCEPTED => 'status-badge status-available',
            self::STATUS_REJECTED => 'status-badge status-full',
            default => 'status-badge status-limited',
        };
    }
}
