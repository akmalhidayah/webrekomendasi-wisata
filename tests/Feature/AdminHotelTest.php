<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminHotelTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_hotel_crud(): void
    {
        $this->seed();
        $admin = User::where('email', 'admin@gmail.com')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.hotels.index'))
            ->assertOk()
            ->assertSee('Data Hotel');

        $this->post(route('admin.hotels.store'), [
            'nama_hotel' => 'Hotel Test Makassar',
            'alamat' => 'Kota Makassar',
            'deskripsi' => 'Hotel untuk pengujian.',
            'harga_min' => 300000,
            'harga_max' => 500000,
            'traveloka_url' => 'https://www.traveloka.com/id-id/search/hotel?query=Hotel+Test+Makassar',
            'maps_url' => 'https://www.google.com/maps/search/?api=1&query=Hotel+Test+Makassar',
            'rating_hotel' => 4.2,
            'status' => 'aktif',
        ])->assertRedirect();

        $hotel = Hotel::where('slug', 'hotel-test-makassar')->firstOrFail();
        $this->assertSame('Hotel Test Makassar', $hotel->nama_hotel);

        $this->get(route('admin.hotels.edit', $hotel))
            ->assertOk()
            ->assertSee('Hotel Test Makassar');

        $this->put(route('admin.hotels.update', $hotel), [
            'nama_hotel' => 'Hotel Test Makassar Update',
            'alamat' => 'Kota Makassar',
            'deskripsi' => 'Hotel untuk pengujian update.',
            'harga_min' => 350000,
            'harga_max' => 550000,
            'traveloka_url' => 'https://www.traveloka.com/id-id/search/hotel?query=Hotel+Test+Makassar+Update',
            'maps_url' => 'https://www.google.com/maps/search/?api=1&query=Hotel+Test+Makassar+Update',
            'rating_hotel' => 4.4,
            'status' => 'aktif',
        ])->assertRedirect(route('admin.hotels.show', $hotel));

        $this->assertDatabaseHas('hotels', [
            'id' => $hotel->id,
            'nama_hotel' => 'Hotel Test Makassar Update',
        ]);

        $this->delete(route('admin.hotels.destroy', $hotel))
            ->assertRedirect(route('admin.hotels.index'));

        $this->assertSoftDeleted('hotels', ['id' => $hotel->id]);
    }
}
