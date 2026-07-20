<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GuestVisitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_guest',
        'session_id',
        'nama_opsional',
        'asal_kota',
        'tanggal_akses',
        'ip_address',
        'user_agent',
        'budget_min',
        'budget_max',
        'butuh_hotel',
        'jumlah_malam',
        'user_latitude',
        'user_longitude',
        'is_location_allowed',
        'location_captured_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_akses' => 'date',
            'budget_min' => 'decimal:2',
            'budget_max' => 'decimal:2',
            'butuh_hotel' => 'boolean',
            'jumlah_malam' => 'integer',
            'user_latitude' => 'decimal:7',
            'user_longitude' => 'decimal:7',
            'is_location_allowed' => 'boolean',
            'location_captured_at' => 'datetime',
        ];
    }

    public function hasLocation(): bool
    {
        return $this->is_location_allowed && $this->user_latitude !== null && $this->user_longitude !== null;
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

    public function logAktivitas(): HasMany
    {
        return $this->hasMany(LogAktivitas::class);
    }
}
