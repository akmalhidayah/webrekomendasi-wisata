<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RatingKunjungan extends Model
{
    use HasFactory;

    protected $table = 'rating_kunjungan';

    protected $fillable = [
        'guest_visitor_id',
        'wisata_id',
        'rating',
        'ulasan',
        'pernah_dikunjungi',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'pernah_dikunjungi' => 'boolean',
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
