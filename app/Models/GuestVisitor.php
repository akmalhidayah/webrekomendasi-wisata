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
    ];

    protected function casts(): array
    {
        return ['tanggal_akses' => 'date'];
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
