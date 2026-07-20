<?php

namespace App\Models;

use App\Support\MediaUrl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hotel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nama_hotel',
        'slug',
        'alamat',
        'deskripsi',
        'harga_min',
        'harga_max',
        'gambar',
        'traveloka_url',
        'maps_url',
        'rating_hotel',
        'status',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'harga_min' => 'decimal:2',
            'harga_max' => 'decimal:2',
            'rating_hotel' => 'decimal:1',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function getGambarUrlAttribute(): ?string
    {
        return MediaUrl::fromPublicDisk($this->gambar);
    }

    public function wisata(): BelongsToMany
    {
        return $this->belongsToMany(Wisata::class, 'wisata_hotels')
            ->withPivot(['urutan', 'keterangan'])
            ->withTimestamps();
    }
}
