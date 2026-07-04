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
        'hotel_id',
        'estimasi_biaya_wisata',
        'estimasi_biaya_hotel',
        'total_estimasi_budget',
        'jarak_km',
        'skor_cf',
        'skor_budget',
        'skor_jarak',
        'skor_preferensi',
        'skor_rating_destinasi',
        'skor_akhir',
        'alasan_rekomendasi',
    ];

    protected function casts(): array
    {
        return [
            'nilai_prediksi' => 'decimal:4',
            'nilai_similarity' => 'decimal:4',
            'ranking' => 'integer',
            'estimasi_biaya_wisata' => 'decimal:2',
            'estimasi_biaya_hotel' => 'decimal:2',
            'total_estimasi_budget' => 'decimal:2',
            'jarak_km' => 'decimal:2',
            'skor_cf' => 'decimal:4',
            'skor_budget' => 'decimal:4',
            'skor_jarak' => 'decimal:4',
            'skor_preferensi' => 'decimal:4',
            'skor_rating_destinasi' => 'decimal:4',
            'skor_akhir' => 'decimal:4',
            'alasan_rekomendasi' => 'array',
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

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }
}
