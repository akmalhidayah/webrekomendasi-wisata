<?php

namespace Tests\Feature;

use App\Models\Hotel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_admin_can_upload_and_replace_hotel_image(): void
    {
        Storage::fake('public');
        $this->seed();
        $admin = User::where('email', 'admin@gmail.com')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.hotels.store'), [
            'nama_hotel' => 'Hotel Dengan Gambar',
            'harga_min' => 300000,
            'gambar' => UploadedFile::fake()->image('hotel-awal.jpg', 1200, 800)->size(500),
            'status' => 'aktif',
        ])->assertRedirect();

        $hotel = Hotel::where('slug', 'hotel-dengan-gambar')->firstOrFail();
        $this->assertNotNull($hotel->gambar);
        $this->assertSame('300000.00', $hotel->harga_max);
        Storage::disk('public')->assertExists($hotel->gambar);
        $oldPath = $hotel->gambar;

        $this->put(route('admin.hotels.update', $hotel), [
            'nama_hotel' => $hotel->nama_hotel,
            'harga_min' => 300000,
            'harga_max' => 500000,
            'gambar' => UploadedFile::fake()->image('hotel-baru.webp', 1200, 800)->size(600),
            'status' => 'aktif',
        ])->assertRedirect(route('admin.hotels.show', $hotel));

        $hotel->refresh();
        $this->assertNotSame($oldPath, $hotel->gambar);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($hotel->gambar);
    }

    public function test_invalid_or_oversized_hotel_image_is_rejected_with_clear_error(): void
    {
        Storage::fake('public');
        $this->seed();
        $admin = User::where('email', 'admin@gmail.com')->firstOrFail();

        $this->actingAs($admin)->from(route('admin.hotels.create'))->post(route('admin.hotels.store'), [
            'nama_hotel' => 'Hotel Gambar Terlalu Besar',
            'harga_min' => 300000,
            'harga_max' => 500000,
            'gambar' => UploadedFile::fake()->image('besar.jpg')->size(2049),
            'status' => 'aktif',
        ])->assertRedirect(route('admin.hotels.create'))
            ->assertSessionHasErrors(['gambar' => 'Ukuran gambar hotel maksimal 2 MB.']);

        $this->assertDatabaseMissing('hotels', ['slug' => 'hotel-gambar-terlalu-besar']);
    }
}
