<?php

namespace Database\Seeders;

use App\Models\Wisata;
use Illuminate\Database\Seeder;

class WisataLocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            'pantai-losari' => [-5.143464860458962, 119.40746066114595],
            'pantai-akkarena' => [-5.170121958036661, 119.38902932562621],
            'pantai-tanjung-bayang' => [-5.182441429269444, 119.38891218013912],
            'pantai-layar-putih' => [-5.191122698194006, 119.38249605143983],
            'pantai-indah-bosowa' => [-5.164472294413307, 119.38808297116421],
            'pantai-biru' => [-5.175062873091979, 119.38734949238817],
            'pulau-samalona' => [-5.12321662958251, 119.34325933414755],
            'pulau-kodingareng-keke' => [-5.105058878416114, 119.28901035767161],
            'pulau-langkai' => [-5.032241765937907, 119.09426211869953],
            'pulau-kayangan' => [-5.106369099004695, 119.4011693913378],
            'pulau-gusung-lae-lae-caddi' => [-5.12217904371034, 119.39429304656788],
            'pulau-wisata-lakkang' => [-5.121553311993199, 119.4671372],
            'ekowisata-mangrove-lantebung' => [-5.07808346553287, 119.46623629994764],
            'ekowisata-untia' => [-5.0677279358900185, 119.46992895576862],
            'benteng-rotterdam-makassar' => [-5.134001062692211, 119.4055546508115],
            'museum-kota-makassar' => [-5.134468570588068, 119.40866901534321],
            'museum-la-galigo' => [-5.133505155601187, 119.40530181349256],
            'monumen-mandala' => [-5.137575099871098, 119.41372402883579],
            'monumen-korban-40000-jiwa' => [-5.129572725096951, 119.432478844179],
            'monumen-maha-putera-emmy-saelan' => [-5.168545303517223, 119.4508702153432],
            'pelabuhan-paotere' => [-5.1106465592410615, 119.42151049205926],
            'kompleks-makam-raja-raja-tallo' => [-5.102737277913327, 119.4455681288358],
            'masjid-99-kubah-cpi-makassar' => [-5.143813872469098, 119.40414109999998],
            'taman-pakui-sayang' => [-5.151633687019611, 119.43696081349258],
            'bugis-waterpark-adventure' => [-5.153631947143655, 119.4946028423284],
        ];

        foreach ($locations as $slug => [$latitude, $longitude]) {
            $wisata = Wisata::query()->where('slug', $slug)->first();

            if ($wisata === null) {
                $this->command?->warn("Destinasi dengan slug {$slug} tidak ditemukan; koordinat dilewati.");

                continue;
            }

            $query = urlencode($wisata->nama_wisata.' Makassar');

            $wisata->update([
                'latitude' => $latitude,
                'longitude' => $longitude,
                'maps_url' => $wisata->maps_url ?: "https://www.google.com/maps/search/?api=1&query={$query}",
            ]);
        }
    }
}
