<?php

namespace Database\Seeders;

use App\Models\Wisata;
use Illuminate\Database\Seeder;

class WisataLocationSeeder extends Seeder
{
    public function run(): void
    {
        Wisata::query()->each(function (Wisata $wisata) {
            // Koordinat sengaja dibiarkan null sampai admin mengisi angka yang valid dari Google Maps.
            $query = urlencode($wisata->nama_wisata.' Makassar');

            $wisata->update([
                'maps_url' => $wisata->maps_url ?: "https://www.google.com/maps/search/?api=1&query={$query}",
            ]);
        });
    }
}
