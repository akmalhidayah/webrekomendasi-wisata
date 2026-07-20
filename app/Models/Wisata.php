<?php

namespace App\Models;

use App\Support\MediaUrl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wisata extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'wisata';

    protected $fillable = [
        'kategori_wisata_id',
        'nama_wisata',
        'slug',
        'jenis_wisata',
        'deskripsi',
        'alamat',
        'latitude',
        'longitude',
        'maps_url',
        'kecamatan',
        'kota',
        'provinsi',
        'link_maps',
        'harga_tiket',
        'estimasi_transportasi',
        'estimasi_biaya_lainnya',
        'total_estimasi_biaya',
        'jam_operasional',
        'status',
        'foto_utama',
        'rating_maps',
        'jumlah_rating_maps',
        'rating_maps_updated_at',
    ];

    protected function casts(): array
    {
        return [
            'harga_tiket' => 'decimal:2',
            'estimasi_transportasi' => 'decimal:2',
            'estimasi_biaya_lainnya' => 'decimal:2',
            'total_estimasi_biaya' => 'decimal:2',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'rating_maps' => 'decimal:1',
            'jumlah_rating_maps' => 'integer',
            'rating_maps_updated_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Wisata $wisata) {
            if ($wisata->isDirty(['rating_maps', 'jumlah_rating_maps'])) {
                $wisata->rating_maps_updated_at = now();
            }
        });
    }

    /**
     * URL relatif membuat foto tetap memakai host dan port aplikasi yang aktif.
     */
    public function getFotoUrlAttribute(): ?string
    {
        return MediaUrl::fromPublicDisk($this->foto_utama);
    }

    public function kategoriWisata(): BelongsTo
    {
        return $this->belongsTo(KategoriWisata::class);
    }

    public function fasilitasWisata(): HasMany
    {
        return $this->hasMany(FasilitasWisata::class);
    }

    public function fotoWisata(): HasMany
    {
        return $this->hasMany(FotoWisata::class);
    }

    public function surveyPreferensi(): HasMany
    {
        return $this->hasMany(SurveyPreferensi::class);
    }

    public function ratingKunjungan(): HasMany
    {
        return $this->hasMany(RatingKunjungan::class);
    }

    public function hasilRekomendasi(): HasMany
    {
        return $this->hasMany(HasilRekomendasi::class);
    }

    public function hotels(): BelongsToMany
    {
        return $this->belongsToMany(Hotel::class, 'wisata_hotels')
            ->withPivot(['urutan', 'keterangan'])
            ->withTimestamps()
            ->orderBy('wisata_hotels.urutan');
    }

    public function getCoordinateLabelAttribute(): string
    {
        if ($this->latitude !== null && $this->longitude !== null) {
            return $this->latitude.', '.$this->longitude;
        }

        return 'Koordinat belum diisi';
    }

    public function getRatingAplikasiRataRataAttribute(): ?float
    {
        $avg = $this->attributes['rating_aplikasi'] ?? null;
        $avg ??= $this->ratingKunjungan()->where('status', 'approved')->avg('rating');

        return $avg !== null ? round((float) $avg, 1) : null;
    }

    public function getJumlahRatingAplikasiAttribute(): int
    {
        $count = $this->attributes['jumlah_rating_aplikasi'] ?? null;

        return $count !== null ? (int) $count : $this->ratingKunjungan()->where('status', 'approved')->count();
    }

    public function getRatingTampilAttribute(): ?float
    {
        $ratingMaps = $this->rating_maps !== null ? max(0, min(5, (float) $this->rating_maps)) : null;
        $jumlahRatingMaps = max(0, (int) ($this->jumlah_rating_maps ?? 0));
        $ratingAplikasi = $this->rating_aplikasi_rata_rata;
        $jumlahRatingAplikasi = $this->jumlah_rating_aplikasi;

        // Jika ada rating Maps dan rating aplikasi, gabungkan.
        // Rating Maps dianggap sebagai nilai awal/baseline.
        $totalCount = $jumlahRatingMaps + $jumlahRatingAplikasi;
        if ($totalCount === 0) {
            return null;
        }

        return max(0, min(5, (($ratingMaps ?? 0) * $jumlahRatingMaps + ($ratingAplikasi ?? 0) * $jumlahRatingAplikasi) / $totalCount));
    }

    public function getLabelRatingTampilAttribute(): string
    {
        if ($this->rating_maps !== null && $this->jumlah_rating_aplikasi > 0) {
            return 'Rating Maps + Aplikasi';
        }

        if ($this->rating_maps !== null) {
            return 'Rating Maps';
        }

        if ($this->jumlah_rating_aplikasi > 0) {
            return 'Rating Aplikasi';
        }

        return 'Belum ada rating';
    }
}
