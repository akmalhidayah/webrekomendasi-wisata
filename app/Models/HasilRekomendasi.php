<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HasilRekomendasi extends Model
{
    use HasFactory;

    protected $table = 'hasil_rekomendasi';

    protected $fillable = [
        'guest_visitor_id',
        'wisata_id',
        'nilai_prediksi',
        'nilai_similarity',
        'ranking',
        'metode',
    ];

    protected function casts(): array
    {
        return [
            'nilai_prediksi' => 'decimal:4',
            'nilai_similarity' => 'decimal:4',
            'ranking' => 'integer',
        ];
    }

    public function guestVisitor(): BelongsTo
    {
        return $this->belongsTo(GuestVisitor::class);
    }

    public function wisata(): BelongsTo
    {
        return $this->belongsTo(Wisata::class);
    }
}
