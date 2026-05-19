<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\PaymentLog;

class Booking extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_ACCEPTED = 'diterima';
    public const STATUS_REJECTED = 'ditolak';
    public const PAYMENT_UNPAID = 'belum_bayar';
    public const PAYMENT_PAID = 'sudah_bayar';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'kost_id',
        'tanggal_masuk',
        'tipe_sewa',
        'durasi',
        'durasi_bulan',
        'payment_method',
        'payment_status',
        'payment_proof_data',
        'payment_proof_mime_type',
        'status',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_masuk' => 'date',
            'durasi' => 'integer',
            'durasi_bulan' => 'integer',
            'created_at' => 'datetime',
        ];
    }

    public function getDurasiLabelAttribute(): string
    {
        $duration = (int) ($this->durasi ?: $this->durasi_bulan ?: 1);
        $type = $this->tipe_sewa ?: 'bulanan';

        return $type === 'harian'
            ? $duration.' hari'
            : $duration.' bulan';
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return Kost::paymentMethodLabel($this->payment_method);
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return $this->payment_status === self::PAYMENT_PAID ? 'Sudah bayar' : 'Belum bayar';
    }

    public function getPaymentStatusBadgeClassAttribute(): string
    {
        return $this->payment_status === self::PAYMENT_PAID
            ? 'status-badge status-available'
            : 'status-badge status-limited';
    }

    public function getPaymentProofUrlAttribute(): ?string
    {
        if (! $this->payment_proof_data || ! $this->payment_proof_mime_type) {
            return null;
        }

        return 'data:'.$this->payment_proof_mime_type.';base64,'.$this->payment_proof_data;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kost(): BelongsTo
    {
        return $this->belongsTo(Kost::class);
    }

    public function paymentLogs(): HasMany
    {
        return $this->hasMany(PaymentLog::class)->orderBy('created_at');
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
