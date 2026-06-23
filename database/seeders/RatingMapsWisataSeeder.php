<?php

namespace Database\Seeders;

use App\Models\Wisata;
use Illuminate\Database\Seeder;

class RatingMapsWisataSeeder extends Seeder
{
    public function run(): void
    {
        $ratings = [
            'Pantai Losari' => 4.4,
            'Pantai Akkarena' => 4.2,
            'Pantai Tanjung Bayang' => 4.1,
            'Pantai Layar Putih' => 4.3,
            'Pantai Indah Bosowa' => 4.3,
            'Pantai Biru' => 4.5,
            'Pulau Samalona' => 4.5,
            'Pulau Kodingareng Keke' => 4.6,
            'Pulau Langkai' => 4.8,
            'Pulau Kayangan' => 3.7,
            'Pulau Gusung Lae-Lae Caddi' => 4.4,
            'Pulau Wisata Lakkang' => 4.6,
            'Ekowisata Mangrove Lantebung' => 4.5,
            'Ekowisata Untia' => 4.4,
            'Benteng Rotterdam Makassar' => 4.5,
            'Museum Kota Makassar' => 4.5,
            'Museum La Galigo' => 4.5,
            'Monumen Mandala' => 4.5,
            'Monumen Korban 40.000 Jiwa' => 4.4,
            'Monumen Maha Putera Emmy Saelan' => 4.0,
            'Pelabuhan Paotere' => 4.3,
            'Kompleks Makam Raja-Raja Tallo' => 4.5,
            'Masjid 99 Kubah CPI Makassar' => 4.7,
            'Taman Pakui Sayang' => 4.6,
            'Bugis Waterpark Adventure' => 4.4,
        ];

        foreach ($ratings as $namaWisata => $rating) {
            $wisata = Wisata::where('nama_wisata', $namaWisata)->first();

            if (! $wisata) {
                $wisata = Wisata::where('nama_wisata', 'like', '%'.$namaWisata.'%')->first();
            }

            if ($wisata) {
                $wisata->update([
                    'rating_maps' => $rating,
                    'jumlah_rating_maps' => 0,
                    'rating_maps_updated_at' => now(),
                ]);
            }
        }
    }
}
