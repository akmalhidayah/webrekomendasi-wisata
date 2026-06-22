<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FasilitasWisata extends Model
{
    use HasFactory;

    protected $table = 'fasilitas_wisata';

    protected $fillable = ['wisata_id', 'nama_fasilitas', 'keterangan'];

    public function wisata(): BelongsTo
    {
        return $this->belongsTo(Wisata::class);
    }
}
