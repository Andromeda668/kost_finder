<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Kost extends Model
{
    use HasFactory;

    public const PAYMENT_METHODS = [
        'ewallet_ovo' => 'OVO',
        'ewallet_dana' => 'DANA',
        'ewallet_gopay' => 'GoPay',
        'ewallet_shopeepay' => 'ShopeePay',
        'ewallet_linkaja' => 'LinkAja',
        'bank_bri' => 'BRI',
        'bank_bni' => 'BNI',
        'bank_mandiri' => 'Mandiri',
        'bank_bca' => 'BCA',
        'bank_bsi' => 'BSI',
        'qris' => 'QRIS',
        'cash' => 'Tunai / Cash',
    ];

    public const PAYMENT_GROUPS = [
        'E-Wallet' => ['ewallet_ovo', 'ewallet_dana', 'ewallet_gopay', 'ewallet_shopeepay', 'ewallet_linkaja'],
        'E-Banking / Transfer Bank' => ['bank_bri', 'bank_bni', 'bank_mandiri', 'bank_bca', 'bank_bsi'],
        'QRIS' => ['qris'],
        'Tunai' => ['cash'],
    ];

    protected $fillable = [
        'user_id',
        'nama_kost',
        'alamat',
        'lokasi',
        'google_maps_link',
        'currency',
        'harga',
        'harga_harian',
        'harga_bulanan',
        'deskripsi',
        'fasilitas',
        'payment_methods',
        'payment_details',
        'qris_image_data',
        'qris_mime_type',
        'thumbnail_image_data',
        'thumbnail_image_mime_type',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'integer',
            'harga_harian' => 'integer',
            'harga_bulanan' => 'integer',
            'payment_methods' => 'array',
            'payment_details' => 'array',
        ];
    }

    public function getThumbnailImageUrlAttribute(): ?string
    {
        if (! $this->thumbnail_image_data || ! $this->thumbnail_image_mime_type) {
            return null;
        }

        return 'data:'.$this->thumbnail_image_mime_type.';base64,'.$this->thumbnail_image_data;
    }

    public static function paymentMethodOptions(): array
    {
        return self::PAYMENT_METHODS;
    }

    public static function paymentMethodGroups(): array
    {
        return self::PAYMENT_GROUPS;
    }

    public static function paymentMethodLabel(?string $method): string
    {
        return self::PAYMENT_METHODS[$method] ?? 'Metode tidak diketahui';
    }

    public function getAvailablePaymentMethodsAttribute(): array
    {
        $methods = collect($this->payment_methods ?: [])
            ->filter(fn ($method) => isset(self::PAYMENT_METHODS[$method]))
            ->values()
            ->all();

        return $methods ?: ['cash'];
    }

    public function getPaymentMethodLabelsAttribute(): array
    {
        return collect($this->available_payment_methods)
            ->map(fn ($method) => self::paymentMethodLabel($method))
            ->all();
    }

    public function paymentDetailFor(string $method): array
    {
        return (array) (($this->payment_details ?: [])[$method] ?? []);
    }

    public function getQrisImageUrlAttribute(): ?string
    {
        if (! $this->qris_image_data || ! $this->qris_mime_type) {
            return null;
        }

        return 'data:'.$this->qris_mime_type.';base64,'.$this->qris_image_data;
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(KostImage::class);
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(KostImage::class)->latestOfMany('id');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    public function room(): HasOne
    {
        return $this->hasOne(Room::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function nearbyPlaces(): HasMany
    {
        return $this->hasMany(NearbyPlace::class);
    }

    public function getCurrencySymbolAttribute(): string
    {
        return match (strtoupper((string) $this->currency)) {
            'USD' => '$',
            'EUR' => '€',
            'SGD' => 'S$',
            'MYR' => 'RM',
            default => 'Rp',
        };
    }

    public function priceFor(string $period): ?int
    {
        return match ($period) {
            'harian' => $this->harga_harian,
            'bulanan' => $this->harga_bulanan ?? $this->harga,
            default => $this->harga_bulanan ?? $this->harga,
        };
    }

    public function getPrimaryRentalPeriodAttribute(): string
    {
        if ($this->harga_bulanan) {
            return 'bulanan';
        }

        if ($this->harga_harian) {
            return 'harian';
        }

        return 'bulanan';
    }

    public function formatMoney(?int $amount): string
    {
        $amount = (int) ($amount ?? 0);

        if (strtoupper((string) $this->currency) === 'IDR') {
            return number_format($amount, 0, ',', '.');
        }

        return number_format($amount, 0, '.', ',');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($term): void {
            $builder
                ->where('nama_kost', 'like', '%'.$term.'%')
                ->orWhere('lokasi', 'like', '%'.$term.'%')
                ->orWhere('alamat', 'like', '%'.$term.'%');
        });
    }

    public function scopeMaxPrice(Builder $query, int|string|null $maxPrice): Builder
    {
        if (! $maxPrice) {
            return $query;
        }

        if (is_string($maxPrice)) {
            $maxPrice = preg_replace('/\D+/', '', $maxPrice);
        }

        if (! is_numeric($maxPrice)) {
            return $query;
        }

        $value = (int) $maxPrice;

        return $query->where(function (Builder $builder) use ($value): void {
            $builder
                ->where(function (Builder $inner) use ($value): void {
                    $inner->whereNotNull('harga_bulanan')->where('harga_bulanan', '<=', $value);
                })
                ->orWhere(function (Builder $inner) use ($value): void {
                    $inner->whereNull('harga_bulanan')->where('harga', '<=', $value);
                });
        });
    }

    public function getAvailabilityStatusAttribute(): string
    {
        $available = $this->room?->kamar_tersedia ?? 0;
        $total = max($this->room?->total_kamar ?? 0, 0);

        if ($total === 0 || $available <= 0) {
            return 'Penuh';
        }

        if ($available <= max((int) ceil($total * 0.25), 1)) {
            return 'Hampir penuh';
        }

        return 'Tersedia';
    }

    public function getAvailabilityBadgeClassAttribute(): string
    {
        return match ($this->availability_status) {
            'Tersedia' => 'status-badge status-available',
            'Hampir penuh' => 'status-badge status-limited',
            default => 'status-badge status-full',
        };
    }

    public function getWhatsappLinkAttribute(): string
    {
        $phone = preg_replace('/\D+/', '', $this->owner?->ownerContact?->phone ?? '');

        if (Str::startsWith($phone, '0')) {
            $phone = '62'.substr($phone, 1);
        }

        if (Str::startsWith($phone, '8')) {
            $phone = '62'.$phone;
        }

        return 'https://wa.me/'.$phone.'?text='.rawurlencode('Saya tertarik dengan kost Anda');
    }

    public function getGoogleMapsEmbedLinkAttribute(): string
    {
        $parsedUrl = parse_url($this->google_maps_link);
        $query = [];

        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $query);
        }

        $target = $query['q'] ?? $this->alamat;

        return 'https://www.google.com/maps?q='.rawurlencode($target).'&output=embed';
    }

    public function getDisplayImageUrlAttribute(): ?string
    {
        $uploadedImage = $this->primaryImage?->image_url;

        return $uploadedImage ?: $this->google_street_view_image_url;
    }

    public function getGoogleStreetViewImageUrlAttribute(): ?string
    {
        $apiKey = config('services.google_maps.key');

        if (! $apiKey) {
            return null;
        }

        $location = $this->google_maps_location_query;

        if (! $location) {
            return null;
        }

        return 'https://maps.googleapis.com/maps/api/streetview?'.http_build_query([
            'size' => '900x520',
            'location' => $location,
            'fov' => 80,
            'pitch' => 0,
            'key' => $apiKey,
        ]);
    }

    public function getGoogleMapsLocationQueryAttribute(): string
    {
        $parsedUrl = parse_url($this->google_maps_link);
        $query = [];

        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $query);
        }

        if (! empty($query['q'])) {
            return (string) $query['q'];
        }

        if (! empty($query['query'])) {
            return (string) $query['query'];
        }

        if (isset($parsedUrl['path']) && preg_match('/@(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)/', $parsedUrl['path'], $matches)) {
            return $matches[1].','.$matches[2];
        }

        return trim($this->alamat.' '.$this->lokasi);
    }
}
