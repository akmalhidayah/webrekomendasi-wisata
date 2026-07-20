<?php

namespace App\Models;

use App\Support\MediaUrl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FotoWisata extends Model
{
    use HasFactory;

    protected $table = 'foto_wisata';

    protected $fillable = ['wisata_id', 'path_foto', 'caption', 'is_utama'];

    protected function casts(): array
    {
        return ['is_utama' => 'boolean'];
    }

    public function getFotoUrlAttribute(): ?string
    {
        return MediaUrl::fromPublicDisk($this->path_foto);
    }

    public function wisata(): BelongsTo
    {
        return $this->belongsTo(Wisata::class);
    }
}
