<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\KategoriWisata;
use App\Models\User;
use App\Models\Wisata;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminWisataTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_wisata_page_requires_authentication(): void
    {
        $this->get(route('admin.wisata.index'))->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_view_wisata_index_after_login(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@gmail.com')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.wisata.index'))
            ->assertOk()
            ->assertSee('Data Wisata')
            ->assertSee('Pantai Losari');

        $this->get(route('admin.dashboard'))->assertOk()->assertSee('Dashboard');
    }

    public function test_admin_can_create_wisata_with_coordinates_and_related_hotels(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@gmail.com')->firstOrFail();
        $kategori = KategoriWisata::firstOrFail();
        $hotels = Hotel::where('status', 'aktif')->take(3)->get();

        $this->actingAs($admin)
            ->post(route('admin.wisata.store'), [
                'kategori_wisata_id' => $kategori->id,
                'nama_wisata' => 'Wisata Koordinat Test',
                'jenis_wisata' => 'Wisata Test',
                'deskripsi' => 'Destinasi untuk pengujian koordinat.',
                'alamat' => 'Kota Makassar',
                'latitude' => -5.1476650,
                'longitude' => 119.4327310,
                'maps_url' => 'https://www.google.com/maps/search/?api=1&query=Wisata+Koordinat+Test',
                'kecamatan' => 'Ujung Pandang',
                'kota' => 'Makassar',
                'provinsi' => 'Sulawesi Selatan',
                'harga_tiket' => 10000,
                'estimasi_transportasi' => 20000,
                'estimasi_biaya_lainnya' => 30000,
                'jam_operasional' => '08.00-17.00',
                'status' => 'aktif',
                'hotel_terkait' => [
                    1 => ['hotel_id' => $hotels[0]->id, 'keterangan' => 'Prioritas pertama'],
                    2 => ['hotel_id' => $hotels[1]->id, 'keterangan' => 'Prioritas kedua'],
                    3 => ['hotel_id' => $hotels[2]->id, 'keterangan' => 'Prioritas ketiga'],
                ],
            ])
            ->assertRedirect();

        $wisata = Wisata::where('slug', 'wisata-koordinat-test')->firstOrFail();

        $this->assertSame('60000.00', $wisata->total_estimasi_biaya);
        $this->assertSame('https://www.google.com/maps/search/?api=1&query=Wisata+Koordinat+Test', $wisata->link_maps);
        $this->assertCount(3, $wisata->hotels);
        $this->assertDatabaseHas('wisata_hotels', [
            'wisata_id' => $wisata->id,
            'hotel_id' => $hotels[0]->id,
            'urutan' => 1,
        ]);
    }
}
