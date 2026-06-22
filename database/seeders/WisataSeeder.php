<?php

namespace Database\Seeders;

use App\Models\KategoriWisata;
use App\Models\Wisata;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use RuntimeException;

class WisataSeeder extends Seeder
{
    public function run(): void
    {
        $destinasi = [
            ['Pantai Losari', 'Wisata Pantai', 'Wisata alam, tirta, dan buatan', 'Jl. Metro Tanjung Bunga, Maloku, Kecamatan Ujung Pandang, Kota Makassar', 'Ujung Pandang', 'https://www.google.com/maps/search/?api=1&query=Pantai+Losari+Makassar', 0, 25000, 20000, '06.00-22.00', 'Ikon tepi laut Makassar dengan area pedestrian, panorama matahari terbenam, dan pusat kuliner khas.'],
            ['Pantai Akkarena', 'Wisata Pantai', 'Wisata pantai dan rekreasi', 'Jl. Tanjung Bunga, Tanjung Merdeka, Kecamatan Tamalate, Kota Makassar', 'Tamalate', 'https://www.google.com/maps/search/?api=1&query=Pantai+Akkarena+Makassar', 15000, 30000, 25000, '06.00-22.00', 'Pantai rekreasi keluarga dengan dermaga, taman, area bermain, dan pemandangan matahari terbenam.'],
            ['Pantai Tanjung Bayang', 'Wisata Pantai', 'Wisata pantai', 'Kawasan Tanjung Bayang, Kecamatan Tamalate, Kota Makassar', 'Tamalate', 'https://www.google.com/maps/search/?api=1&query=Pantai+Tanjung+Bayang+Makassar', 10000, 35000, 20000, '06.00-20.00', 'Pantai berpasir yang populer untuk berenang, bersantai, dan rekreasi bersama keluarga.'],
            ['Pantai Layar Putih', 'Wisata Pantai', 'Wisata pantai', 'Jl. Tanjung Merdeka, Kecamatan Tamalate, Kota Makassar', 'Tamalate', 'https://www.google.com/maps/search/?api=1&query=Pantai+Layar+Putih+Makassar', 10000, 35000, 20000, '06.00-19.00', 'Kawasan pantai di Tanjung Merdeka dengan suasana santai dan pemandangan pesisir Makassar.'],
            ['Pantai Indah Bosowa', 'Wisata Pantai', 'Wisata pantai', 'Kawasan Tanjung Merdeka, Kecamatan Tamalate, Kota Makassar', 'Tamalate', 'https://www.google.com/maps/search/?api=1&query=Pantai+Indah+Bosowa+Makassar', 10000, 35000, 25000, '06.00-21.00', 'Pantai rekreasi dengan ruang terbuka untuk menikmati laut dan matahari terbenam.'],
            ['Pantai Biru', 'Wisata Pantai', 'Wisata pantai', 'Kota Makassar', null, 'https://www.google.com/maps/search/?api=1&query=Pantai+Biru+Makassar', 10000, 30000, 20000, '06.00-19.00', 'Destinasi pesisir untuk menikmati suasana laut, bersantai, dan berfoto.'],
            ['Pulau Samalona', 'Wisata Bahari', 'Wisata bahari', 'Wilayah kepulauan Kota Makassar', 'Kepulauan Sangkarrang', 'https://www.google.com/maps/search/?api=1&query=Pulau+Samalona+Makassar', 10000, 150000, 75000, '07.00-17.00', 'Pulau kecil dengan air jernih yang cocok untuk snorkeling, berenang, dan wisata sehari.'],
            ['Pulau Kodingareng Keke', 'Wisata Bahari', 'Wisata bahari', 'Kelurahan Kodingareng, Kecamatan Kepulauan Sangkarrang, Kota Makassar', 'Kepulauan Sangkarrang', 'https://www.google.com/maps/search/?api=1&query=Pulau+Kodingareng+Keke+Makassar', 10000, 250000, 100000, '07.00-17.00', 'Pulau tak berpenghuni dengan pasir putih dan perairan jernih untuk snorkeling.'],
            ['Pulau Langkai', 'Wisata Bahari', 'Wisata bahari', 'Kecamatan Kepulauan Sangkarrang, Kota Makassar', 'Kepulauan Sangkarrang', 'https://www.google.com/maps/search/?api=1&query=Pulau+Langkai+Makassar', 0, 300000, 100000, '07.00-17.00', 'Pulau terluar Makassar yang menawarkan suasana kampung nelayan dan panorama bahari.'],
            ['Pulau Kayangan', 'Wisata Bahari', 'Wisata bahari', 'Wilayah Kecamatan Ujung Pandang, Kota Makassar', 'Ujung Pandang', 'https://www.google.com/maps/search/?api=1&query=Pulau+Kayangan+Makassar', 20000, 100000, 50000, '08.00-17.00', 'Pulau wisata dekat pusat kota dengan panorama Makassar dan fasilitas rekreasi.'],
            ['Pulau Gusung Lae-Lae Caddi', 'Wisata Bahari', 'Wisata bahari', 'Wilayah kepulauan Kota Makassar', 'Kepulauan Sangkarrang', 'https://www.google.com/maps/search/?api=1&query=Pulau+Gusung+Lae+Lae+Caddi+Makassar', 0, 150000, 75000, '07.00-17.00', 'Gusung pasir dekat Lae-Lae yang menarik untuk berenang dan menikmati pemandangan laut.'],
            ['Pulau Wisata Lakkang', 'Wisata Alam', 'Wisata alam dan edukasi', 'Kelurahan Lakkang, Kecamatan Tallo, Kota Makassar', 'Tallo', 'https://www.google.com/maps/search/?api=1&query=Pulau+Wisata+Lakkang+Makassar', 5000, 50000, 30000, '07.00-18.00', 'Kawasan hijau di delta Sungai Tallo dengan suasana pedesaan, tambak, dan peninggalan sejarah.'],
            ['Ekowisata Mangrove Lantebung', 'Wisata Alam', 'Wisata alam dan ekowisata', 'Jl. Lantebung, Bira, Kecamatan Tamalanrea, Kota Makassar', 'Tamalanrea', 'https://www.google.com/maps/search/?api=1&query=Ekowisata+Mangrove+Lantebung+Makassar', 5000, 40000, 25000, '07.00-18.00', 'Kawasan konservasi mangrove dengan jalur kayu, spot foto, dan wisata edukasi pesisir.'],
            ['Ekowisata Untia', 'Wisata Alam', 'Wisata alam dan edukasi', 'Kelurahan Untia, Kecamatan Biringkanaya, Kota Makassar', 'Biringkanaya', 'https://www.google.com/maps/search/?api=1&query=Ekowisata+Untia+Makassar', 5000, 45000, 25000, '07.00-18.00', 'Kawasan pesisir dan mangrove yang mengenalkan lingkungan serta kehidupan masyarakat nelayan.'],
            ['Benteng Rotterdam Makassar', 'Wisata Sejarah', 'Wisata sejarah dan budaya', 'Jl. Ujung Pandang, Kecamatan Ujung Pandang, Kota Makassar', 'Ujung Pandang', 'https://www.google.com/maps/search/?api=1&query=Benteng+Rotterdam+Makassar', 0, 25000, 20000, '08.00-18.00', 'Benteng peninggalan Kerajaan Gowa dan kolonial yang menjadi pusat sejarah serta budaya Makassar.'],
            ['Museum Kota Makassar', 'Wisata Edukasi', 'Wisata sejarah dan edukasi', 'Jl. Balaikota No. 11, Baru, Kecamatan Ujung Pandang, Kota Makassar', 'Ujung Pandang', 'https://www.google.com/maps/search/?api=1&query=Museum+Kota+Makassar', 5000, 25000, 15000, '09.00-15.30', 'Museum yang menyajikan koleksi perkembangan Kota Makassar, pemerintahan, dan kehidupan masyarakatnya.'],
            ['Museum La Galigo', 'Wisata Sejarah', 'Wisata sejarah dan budaya', 'Kompleks Benteng Rotterdam, Jl. Ujung Pandang, Kota Makassar', 'Ujung Pandang', 'https://www.google.com/maps/search/?api=1&query=Museum+La+Galigo+Makassar', 10000, 25000, 15000, '08.00-16.00', 'Museum di Benteng Rotterdam dengan koleksi sejarah, arkeologi, dan kebudayaan Sulawesi Selatan.'],
            ['Monumen Mandala', 'Wisata Sejarah', 'Wisata sejarah', 'Jl. Jenderal Sudirman, Kecamatan Ujung Pandang, Kota Makassar', 'Ujung Pandang', 'https://www.google.com/maps/search/?api=1&query=Monumen+Mandala+Makassar', 10000, 25000, 15000, '09.00-17.00', 'Monumen perjuangan pembebasan Irian Barat yang dilengkapi relief dan ruang pamer sejarah.'],
            ['Monumen Korban 40.000 Jiwa', 'Wisata Sejarah', 'Wisata sejarah', 'Kota Makassar', 'Tallo', 'https://www.google.com/maps/search/?api=1&query=Monumen+Korban+40000+Jiwa+Makassar', 0, 30000, 15000, '08.00-18.00', 'Monumen peringatan bagi para korban operasi militer di Sulawesi Selatan pada masa revolusi.'],
            ['Monumen Maha Putera Emmy Saelan', 'Wisata Sejarah', 'Wisata sejarah', 'Kota Makassar', 'Rappocini', 'https://www.google.com/maps/search/?api=1&query=Monumen+Maha+Putera+Emmy+Saelan+Makassar', 0, 30000, 15000, '08.00-18.00', 'Monumen penghormatan kepada Emmy Saelan dan perjuangan mempertahankan kemerdekaan di Makassar.'],
            ['Pelabuhan Paotere', 'Wisata Budaya', 'Wisata budaya dan sejarah', 'Kawasan Paotere, Kecamatan Ujung Tanah, Kota Makassar', 'Ujung Tanah', 'https://www.google.com/maps/search/?api=1&query=Pelabuhan+Paotere+Makassar', 5000, 35000, 25000, '06.00-18.00', 'Pelabuhan tradisional dengan deretan kapal pinisi dan aktivitas maritim masyarakat Makassar.'],
            ['Kompleks Makam Raja-Raja Tallo', 'Wisata Religi', 'Wisata sejarah dan religi', 'Jl. Sultan Abdullah Raya, Kecamatan Tallo, Kota Makassar', 'Tallo', 'https://www.google.com/maps/search/?api=1&query=Kompleks+Makam+Raja+Raja+Tallo+Makassar', 0, 30000, 20000, '08.00-17.00', 'Kompleks makam bangsawan Kerajaan Tallo dengan nilai sejarah, arsitektur, dan ziarah.'],
            ['Masjid 99 Kubah CPI Makassar', 'Wisata Religi', 'Wisata religi dan buatan', 'Kawasan Center Point of Indonesia, Kota Makassar', 'Mariso', 'https://www.google.com/maps/search/?api=1&query=Masjid+99+Kubah+CPI+Makassar', 0, 30000, 25000, '04.30-22.00', 'Masjid ikonik berkubah warna-warni di kawasan CPI yang menjadi tujuan ibadah dan wisata arsitektur.'],
            ['Taman Pakui Sayang', 'Wisata Rekreasi', 'Wisata buatan dan rekreasi', 'Kota Makassar', 'Panakkukang', 'https://www.google.com/maps/search/?api=1&query=Taman+Pakui+Sayang+Makassar', 0, 25000, 15000, '05.00-22.00', 'Ruang terbuka hijau untuk berolahraga, bersantai, dan rekreasi keluarga di tengah kota.'],
            ['Bugis Waterpark Adventure', 'Wisata Rekreasi', 'Wisata buatan dan rekreasi air', 'Kawasan Bukit Baruga, Kecamatan Manggala, Kota Makassar', 'Manggala', 'https://www.google.com/maps/search/?api=1&query=Bugis+Waterpark+Adventure+Makassar', 150000, 50000, 75000, '09.00-17.00', 'Taman rekreasi air keluarga dengan beragam kolam, seluncuran, dan wahana permainan.'],
        ];

        $kategoriIds = KategoriWisata::pluck('id', 'nama_kategori');

        foreach ($destinasi as [$nama, $kategori, $jenis, $alamat, $kecamatan, $maps, $tiket, $transportasi, $lainnya, $jam, $deskripsi]) {
            $kategoriId = $kategoriIds->get($kategori)
                ?? throw new RuntimeException("Kategori {$kategori} belum tersedia.");
            $slug = Str::slug($nama);

            Wisata::updateOrCreate(
                ['slug' => $slug],
                [
                    'kategori_wisata_id' => $kategoriId,
                    'nama_wisata' => $nama,
                    'jenis_wisata' => $jenis,
                    'deskripsi' => $deskripsi,
                    'alamat' => $alamat,
                    'kecamatan' => $kecamatan,
                    'kota' => 'Makassar',
                    'provinsi' => 'Sulawesi Selatan',
                    'link_maps' => $maps,
                    'harga_tiket' => $tiket,
                    'estimasi_transportasi' => $transportasi,
                    'estimasi_biaya_lainnya' => $lainnya,
                    'total_estimasi_biaya' => $tiket + $transportasi + $lainnya,
                    'jam_operasional' => $jam,
                    'status' => 'aktif',
                ],
            );
        }
    }
}
