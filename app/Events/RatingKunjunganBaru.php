<?php

namespace App\Events;

use App\Models\RatingKunjungan;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RatingKunjunganBaru
{
    use Dispatchable, SerializesModels;

    public function __construct(public RatingKunjungan $ratingKunjungan) {}
}
