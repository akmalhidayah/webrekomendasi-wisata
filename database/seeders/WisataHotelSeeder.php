<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\Wisata;
use Illuminate\Database\Seeder;

class WisataHotelSeeder extends Seeder
{
    public function run(): void
    {
        $mapping = [
            'Pantai Losari' => ['Aston Inn Pantai Losari Makassar', 'Swiss-Belhotel Makassar', 'Aryaduta Makassar'],
            'Pantai Akkarena' => ['Gammara Hotel Makassar', 'The Rinra Makassar', 'Swiss-Belhotel Waterfront Makassar'],
            'Pantai Tanjung Bayang' => ['Gammara Hotel Makassar', 'The Rinra Makassar', 'Swiss-Belhotel Waterfront Makassar'],
            'Pantai Layar Putih' => ['Gammara Hotel Makassar', 'The Rinra Makassar', 'Swiss-Belhotel Waterfront Makassar'],
            'Pantai Indah Bosowa' => ['Gammara Hotel Makassar', 'The Rinra Makassar', 'Swiss-Belhotel Waterfront Makassar'],
            'Pantai Biru' => ['Gammara Hotel Makassar', 'The Rinra Makassar', 'Swiss-Belhotel Waterfront Makassar'],
            'Pulau Samalona' => ['Swiss-Belhotel Makassar', "d'primahotel Pattimura Makassar", 'Aston Inn Pantai Losari Makassar'],
            'Pulau Kodingareng Keke' => ['Swiss-Belhotel Makassar', "d'primahotel Pattimura Makassar", 'Aston Inn Pantai Losari Makassar'],
            'Pulau Langkai' => ['Swiss-Belhotel Makassar', "d'primahotel Pattimura Makassar", 'Aston Inn Pantai Losari Makassar'],
            'Pulau Kayangan' => ['Swiss-Belhotel Makassar', "d'primahotel Pattimura Makassar", 'Aston Inn Pantai Losari Makassar'],
            'Pulau Gusung Lae-Lae Caddi' => ['Swiss-Belhotel Makassar', "d'primahotel Pattimura Makassar", 'Aston Inn Pantai Losari Makassar'],
            'Pulau Wisata Lakkang' => ['Dalton Makassar', 'Harper Perintis by ASTON', 'Arbor Biz Hotel Makassar'],
            'Ekowisata Mangrove Lantebung' => ['Harper Perintis by ASTON', 'Dalton Makassar', 'Arbor Biz Hotel Makassar'],
            'Ekowisata Untia' => ['Cordia Hotel Makassar Airport', 'Dalton Makassar', 'Harper Perintis by ASTON'],
            'Benteng Rotterdam Makassar' => ['Swiss-Belhotel Makassar', 'Aston Inn Pantai Losari Makassar', "d'primahotel Pattimura Makassar"],
            'Museum Kota Makassar' => ['Aston Inn Pantai Losari Makassar', 'Swiss-Belhotel Makassar', 'Melia Makassar'],
            'Museum La Galigo' => ['Swiss-Belhotel Makassar', 'Aston Inn Pantai Losari Makassar', "d'primahotel Pattimura Makassar"],
            'Monumen Mandala' => ['Melia Makassar', 'Aston Inn Pantai Losari Makassar', 'Whiz Prime Hotel Sudirman Makassar'],
            'Monumen Korban 40.000 Jiwa' => ['Swiss-Belhotel Makassar', 'Aston Inn Pantai Losari Makassar', "d'primahotel Pattimura Makassar"],
            'Monumen Maha Putera Emmy Saelan' => ['CLARO Makassar', 'Mercure Makassar Nexa Pettarani', 'MaxOneHotels.com @ Resort Makassar'],
            'Pelabuhan Paotere' => ['Swiss-Belhotel Makassar', 'Aston Inn Pantai Losari Makassar', "d'primahotel Pattimura Makassar"],
            'Kompleks Makam Raja-Raja Tallo' => ['Swiss-Belhotel Makassar', 'Aston Inn Pantai Losari Makassar', "d'primahotel Pattimura Makassar"],
            'Masjid 99 Kubah CPI Makassar' => ['The Rinra Makassar', 'Gammara Hotel Makassar', 'Swiss-Belhotel Waterfront Makassar'],
            'Taman Pakui Sayang' => ['CLARO Makassar', 'Mercure Makassar Nexa Pettarani', 'MaxOneHotels.com @ Resort Makassar'],
            'Bugis Waterpark Adventure' => ['Hotel Grand Puri Perintis', 'UNHAS Hotel & Convention', 'CLARO Makassar'],
        ];

        foreach ($mapping as $wisataName => $hotelNames) {
            $wisata = Wisata::where('nama_wisata', $wisataName)->first()
                ?? Wisata::where('nama_wisata', 'like', "%{$wisataName}%")->first();

            if (! $wisata) {
                $this->command?->warn("Wisata tidak ditemukan: {$wisataName}");
                continue;
            }

            $syncData = [];

            foreach ($hotelNames as $index => $hotelName) {
                $hotel = Hotel::where('nama_hotel', $hotelName)->first();

                if (! $hotel) {
                    $this->command?->warn("Hotel tidak ditemukan: {$hotelName}");
                    continue;
                }

                $syncData[$hotel->id] = [
                    'urutan' => $index + 1,
                    'keterangan' => 'Hotel terkait yang dipilih manual oleh admin.',
                ];
            }

            $wisata->hotels()->sync($syncData);
        }
    }
}
