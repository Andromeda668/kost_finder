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
    ];

    public function kost(): BelongsTo
    {
        return $this->belongsTo(Kost::class);
    }
}
