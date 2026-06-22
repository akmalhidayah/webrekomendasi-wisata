<?php

namespace Database\Seeders;

use App\Models\KategoriWisata;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KategoriWisataSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [
            'Wisata Pantai' => 'Destinasi pesisir untuk menikmati panorama laut dan aktivitas pantai.',
            'Wisata Bahari' => 'Destinasi kepulauan dan aktivitas wisata berbasis kelautan.',
            'Wisata Sejarah' => 'Destinasi yang menyimpan peninggalan dan cerita sejarah Makassar.',
            'Wisata Budaya' => 'Destinasi untuk mengenal tradisi dan kehidupan budaya masyarakat.',
            'Wisata Religi' => 'Destinasi peribadatan dan peninggalan bernilai religius.',
            'Wisata Alam' => 'Destinasi dengan daya tarik bentang alam dan ekosistem.',
            'Wisata Edukasi' => 'Destinasi yang menawarkan pengalaman belajar dan pengetahuan.',
            'Wisata Rekreasi' => 'Destinasi untuk hiburan, olahraga, dan rekreasi keluarga.',
            'Wisata Buatan' => 'Destinasi hasil pengembangan manusia dengan daya tarik khusus.',
        ];

        foreach ($kategori as $nama => $deskripsi) {
            KategoriWisata::updateOrCreate(
                ['slug' => Str::slug($nama)],
                ['nama_kategori' => $nama, 'deskripsi' => $deskripsi],
            );
        }
    }
}
