<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        if (Str::startsWith($this->path_foto, ['http://', 'https://'])) {
            return $this->path_foto;
        }

        if ($this->path_foto && Storage::disk('public')->exists($this->path_foto)) {
            return '/storage/'.ltrim($this->path_foto, '/');
        }

        return null;
    }

    public function wisata(): BelongsTo
    {
        return $this->belongsTo(Wisata::class);
    }
}
