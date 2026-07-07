<?php

namespace Tests\Feature;

use App\Models\KategoriWisata;
use App\Models\Wisata;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WisataIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_wisata_index_renders_bootstrap_pagination_and_location_prompt(): void
    {
        $this->seed();

        $this->get(route('wisatawan.wisata.index'))
            ->assertOk()
            ->assertSee('Daftar Wisata Makassar')
            ->assertSee('Aktifkan lokasi untuk melihat jarak destinasi')
            ->assertSee('pagination', false)
            ->assertSee('page-link', false);
    }

    public function test_wisata_index_keeps_location_query_and_shows_distance(): void
    {
        $this->seed();

        $kategori = KategoriWisata::firstOrFail();
        Wisata::create([
            'kategori_wisata_id' => $kategori->id,
            'nama_wisata' => 'A Test Destinasi Koordinat',
            'slug' => 'a-test-destinasi-koordinat',
            'jenis_wisata' => 'Wisata Test',
            'deskripsi' => 'Destinasi untuk pengujian jarak.',
            'alamat' => 'Kota Makassar',
            'latitude' => -5.1400000,
            'longitude' => 119.4000000,
            'kecamatan' => 'Ujung Pandang',
            'kota' => 'Makassar',
            'provinsi' => 'Sulawesi Selatan',
            'harga_tiket' => 0,
            'estimasi_transportasi' => 0,
            'estimasi_biaya_lainnya' => 0,
            'total_estimasi_biaya' => 0,
            'status' => 'aktif',
        ]);

        $this->get(route('wisatawan.wisata.index', [
            'lat' => -5.14,
            'lng' => 119.40,
        ]))
            ->assertOk()
            ->assertSee('Jarak dari lokasi Anda: 0.0 km')
            ->assertSee('lat=-5.14', false)
            ->assertSee('lng=119.4', false)
            ->assertSee('page=2', false);
    }
}
