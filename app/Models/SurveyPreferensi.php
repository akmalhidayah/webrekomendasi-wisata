<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyPreferensi extends Model
{
    use HasFactory;

    protected $table = 'survey_preferensi';

    protected $fillable = ['guest_visitor_id', 'wisata_id', 'rating_awal'];

    protected function casts(): array
    {
        return ['rating_awal' => 'integer'];
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
