<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KostImage extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'kost_id',
        'image_path',
        'image_data',
        'mime_type',
    ];

    public function kost(): BelongsTo
    {
        return $this->belongsTo(Kost::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        if ($this->image_data && $this->mime_type) {
            return 'data:'.$this->mime_type.';base64,'.$this->image_data;
        }

        return $this->image_path ? asset('storage/'.$this->image_path) : null;
    }
}
