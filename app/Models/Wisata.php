<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            'rating_maps' => 'decimal:1',
            'jumlah_rating_maps' => 'integer',
            'rating_maps_updated_at' => 'datetime',
        ];
    }

    /**
     * URL relatif membuat foto tetap memakai host dan port aplikasi yang aktif.
     */
    public function getFotoUrlAttribute(): ?string
    {
        if ($this->foto_utama && Str::startsWith($this->foto_utama, ['http://', 'https://'])) {
            return $this->foto_utama;
        }

        if ($this->foto_utama && Storage::disk('public')->exists($this->foto_utama)) {
            return '/storage/'.ltrim($this->foto_utama, '/');
        }

        return null;
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
    public function getRatingAplikasiRataRataAttribute(): ?float
{
    $avg = $this->ratingKunjungan()
        ->where('status', 'disetujui')
        ->avg('rating');

    return $avg !== null ? round((float) $avg, 1) : null;
}

public function getJumlahRatingAplikasiAttribute(): int
{
    return (int) $this->ratingKunjungan()
        ->where('status', 'disetujui')
        ->count();
}

public function getRatingTampilAttribute(): ?float
{
    $ratingMaps = $this->rating_maps !== null ? (float) $this->rating_maps : null;
    $ratingAplikasi = $this->rating_aplikasi_rata_rata;
    $jumlahRatingAplikasi = $this->jumlah_rating_aplikasi;

    // Jika ada rating Maps dan rating aplikasi, gabungkan.
    // Rating Maps dianggap sebagai nilai awal/baseline.
    if ($ratingMaps !== null && $ratingAplikasi !== null && $jumlahRatingAplikasi > 0) {
        $nilaiGabungan = ($ratingMaps + ($ratingAplikasi * $jumlahRatingAplikasi)) / ($jumlahRatingAplikasi + 1);

        return round($nilaiGabungan, 1);
    }

    // Jika belum ada rating aplikasi, tampilkan rating Maps.
    if ($ratingMaps !== null) {
        return round($ratingMaps, 1);
    }

    // Jika tidak ada rating Maps, tampilkan rating aplikasi.
    if ($ratingAplikasi !== null) {
        return round($ratingAplikasi, 1);
    }

    return null;
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
