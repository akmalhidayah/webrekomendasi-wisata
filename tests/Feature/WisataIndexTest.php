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

    public function test_seeded_destinations_show_distance_even_when_user_is_far_away(): void
    {
        $this->seed();

        $response = $this->get(route('wisatawan.wisata.index', [
            'search' => 'Pantai Losari',
            'lat' => -6.2088,
            'lng' => 106.8456,
        ]));

        $response->assertOk()
            ->assertSee('Pantai Losari')
            ->assertSee('Jarak dari lokasi Anda:')
            ->assertDontSee('Koordinat destinasi belum tersedia');

        $distance = $response->viewData('wisata')->firstOrFail()->distance_km;

        $this->assertIsFloat($distance);
        $this->assertGreaterThan(1000, $distance);
    }

    public function test_location_seeder_fills_coordinates_for_every_seeded_destination(): void
    {
        $this->seed();

        $this->assertSame(25, Wisata::query()->count());
        $this->assertSame(
            0,
            Wisata::query()
                ->where(fn ($query) => $query->whereNull('latitude')->orWhereNull('longitude'))
                ->count(),
        );

        $losari = Wisata::query()->where('slug', 'pantai-losari')->firstOrFail();
        $bugisWaterpark = Wisata::query()->where('slug', 'bugis-waterpark-adventure')->firstOrFail();

        $this->assertEqualsWithDelta(-5.1434649, (float) $losari->latitude, 0.0000001);
        $this->assertEqualsWithDelta(119.4074607, (float) $losari->longitude, 0.0000001);
        $this->assertEqualsWithDelta(-5.1536319, (float) $bugisWaterpark->latitude, 0.0000001);
        $this->assertEqualsWithDelta(119.4946028, (float) $bugisWaterpark->longitude, 0.0000001);
    }

    public function test_invalid_coordinates_are_ignored_and_zero_coordinates_are_valid(): void
    {
        $this->seed();

        $this->get('/wisata?lat=invalid&lng=119.4')
            ->assertOk()
            ->assertSee('Aktifkan lokasi untuk melihat jarak destinasi');

        $response = $this->get('/wisata?lat=0&lng=0');
        $response->assertOk()->assertSee('Hapus lokasi');
        $this->assertSame(['lat' => 0.0, 'lng' => 0.0], $response->viewData('userLocation'));
    }

    public function test_filters_and_pagination_keep_valid_location_query(): void
    {
        $kategori = KategoriWisata::create([
            'nama_kategori' => 'Pantai Test',
            'slug' => 'pantai-test',
        ]);

        foreach (range(1, 13) as $number) {
            Wisata::create([
                'kategori_wisata_id' => $kategori->id,
                'nama_wisata' => "Tujuan Filter {$number}",
                'slug' => "tujuan-filter-{$number}",
                'jenis_wisata' => 'Pantai',
                'alamat' => 'Makassar',
                'latitude' => 0,
                'longitude' => 0,
                'status' => 'aktif',
            ]);
        }

        $response = $this->get(route('wisatawan.wisata.index', [
            'search' => 'Tujuan Filter',
            'kategori' => $kategori->id,
            'lat' => 0,
            'lng' => 0,
        ]));

        $response->assertOk()
            ->assertSee('page=2', false)
            ->assertSee('search=Tujuan%20Filter', false)
            ->assertSee('kategori='.$kategori->id, false)
            ->assertSee('lat=0', false)
            ->assertSee('lng=0', false);

        $paginator = $response->viewData('wisata');
        $this->assertFalse($paginator->first()->relationLoaded('hotels'));
        $this->assertArrayHasKey('harga_hotel_termurah', $paginator->first()->getAttributes());
    }

    public function test_location_script_is_guarded_and_missing_photo_has_fallback(): void
    {
        $kategori = KategoriWisata::create([
            'nama_kategori' => 'Tanpa Foto',
            'slug' => 'tanpa-foto',
        ]);
        Wisata::create([
            'kategori_wisata_id' => $kategori->id,
            'nama_wisata' => 'Wisata Tanpa Foto',
            'slug' => 'wisata-tanpa-foto',
            'jenis_wisata' => 'Museum',
            'alamat' => 'Makassar',
            'status' => 'aktif',
        ]);

        $this->get('/wisata')
            ->assertOk()
            ->assertSee('images/default-wisata.svg', false)
            ->assertSee('location-manager', false);

        $legacyViewScript = file_get_contents(resource_path('views/wisatawan/wisata/index.blade.php'));
        $locationManager = file_get_contents(resource_path('js/location-manager.js'));
        $this->assertStringNotContainsString('window.location.href', $legacyViewScript);
        $this->assertStringNotContainsString('location.reload()', $legacyViewScript);
        $this->assertStringContainsString('LOCATION_REDIRECT_GUARD_KEY', $locationManager);
        $this->assertStringContainsString('window.location.replace', $locationManager);
    }
}
