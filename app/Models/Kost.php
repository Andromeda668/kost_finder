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

    protected $fillable = [
        'user_id',
        'nama_kost',
        'alamat',
        'lokasi',
        'google_maps_link',
        'harga',
        'deskripsi',
        'fasilitas',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'integer',
        ];
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

        // Parse if it's a formatted string like "Rp 1.000.000"
        if (is_string($maxPrice) && str_starts_with($maxPrice, 'Rp ')) {
            $maxPrice = str_replace(['Rp ', '.'], '', $maxPrice);
        }

        if (! is_numeric($maxPrice)) {
            return $query;
        }

        return $query->where('harga', '<=', (int) $maxPrice);
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
