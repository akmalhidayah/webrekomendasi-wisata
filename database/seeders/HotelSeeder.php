<?php

namespace Database\Seeders;

use App\Models\Hotel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class HotelSeeder extends Seeder
{
    public function run(): void
    {
        $hotels = [
            ['Aston Inn Pantai Losari Makassar', 'Kota Makassar', 650000, 950000, 4.5],
            ['Swiss-Belhotel Makassar', 'Kota Makassar', 700000, 1100000, 4.6],
            ['Aryaduta Makassar', 'Kota Makassar', 750000, 1200000, 4.5],
            ['Gammara Hotel Makassar', 'Kota Makassar', 650000, 1000000, 4.4],
            ['The Rinra Makassar', 'Kota Makassar', 850000, 1300000, 4.7],
            ['Swiss-Belhotel Waterfront Makassar', 'Kota Makassar', 650000, 1050000, 4.5],
            ["d'primahotel Pattimura Makassar", 'Kota Makassar', 350000, 550000, 4.2],
            ['Dalton Makassar', 'Kota Makassar', 450000, 700000, 4.3],
            ['Harper Perintis by ASTON', 'Kota Makassar', 450000, 750000, 4.4],
            ['Arbor Biz Hotel Makassar', 'Kota Makassar', 300000, 500000, 4.1],
            ['Cordia Hotel Makassar Airport', 'Kota Makassar', 350000, 600000, 4.2],
            ['Melia Makassar', 'Kota Makassar', 700000, 1150000, 4.6],
            ['Whiz Prime Hotel Sudirman Makassar', 'Kota Makassar', 350000, 550000, 4.2],
            ['CLARO Makassar', 'Kota Makassar', 650000, 1100000, 4.6],
            ['Mercure Makassar Nexa Pettarani', 'Kota Makassar', 500000, 850000, 4.4],
            ['MaxOneHotels.com @ Resort Makassar', 'Kota Makassar', 350000, 600000, 4.2],
            ['Hotel Grand Puri Perintis', 'Kota Makassar', 250000, 450000, 4.0],
            ['UNHAS Hotel & Convention', 'Kota Makassar', 400000, 700000, 4.3],
        ];

        foreach ($hotels as [$name, $address, $minPrice, $maxPrice, $rating]) {
            $query = urlencode($name);

            Hotel::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'nama_hotel' => $name,
                    'alamat' => $address,
                    'deskripsi' => "Hotel terkait untuk paket wisata di sekitar {$address}.",
                    'harga_min' => $minPrice,
                    'harga_max' => $maxPrice,
                    'rating_hotel' => $rating,
                    'gambar' => 'https://placehold.co/800x520/e0f2fe/075985?text='.urlencode($name),
                    'traveloka_url' => "https://www.traveloka.com/id-id/search/hotel?query={$query}",
                    'maps_url' => "https://www.google.com/maps/search/?api=1&query={$query}",
                    'status' => 'aktif',
                ],
            );
        }
    }
}
