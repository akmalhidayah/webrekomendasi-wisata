<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriWisata extends Model
{
    use HasFactory;

    protected $table = 'kategori_wisata';

    protected $fillable = ['nama_kategori', 'slug', 'deskripsi'];

    public function wisata(): HasMany
    {
        return $this->hasMany(Wisata::class);
    }
}
