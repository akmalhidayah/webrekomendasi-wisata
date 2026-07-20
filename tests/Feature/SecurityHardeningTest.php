<?php

namespace Tests\Feature;

use App\Models\GuestVisitor;
use App\Models\HasilRekomendasi;
use App\Models\KategoriWisata;
use App\Models\User;
use App\Models\Wisata;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_seeder_is_idempotent_and_preserves_existing_password(): void
    {
        $this->seed(AdminSeeder::class);
        $admin = User::where('email', config('admin.email'))->firstOrFail();
        $hash = $admin->password;
        $this->assertTrue(Hash::check(config('admin.password'), $hash));

        $this->seed(AdminSeeder::class);
        $this->assertSame(1, User::where('email', config('admin.email'))->count());
        $this->assertSame($hash, $admin->fresh()->password);
    }

    public function test_rating_is_updated_and_stays_approved(): void
    {
        $wisata = $this->wisata();
        $guest = GuestVisitor::create(['kode_guest' => 'GST-TEST-RATING']);
        $session = ['kode_guest' => $guest->kode_guest];

        $this->withSession($session)->post(route('wisatawan.rating-kunjungan.store'), [
            'wisata_id' => $wisata->id, 'pernah_dikunjungi' => 1, 'rating' => 3,
        ])->assertRedirect();
        $this->withSession($session)->post(route('wisatawan.rating-kunjungan.store'), [
            'wisata_id' => $wisata->id, 'pernah_dikunjungi' => 1, 'rating' => 5,
        ])->assertRedirect();

        $this->assertDatabaseCount('rating_kunjungan', 1);
        $this->assertDatabaseHas('rating_kunjungan', ['rating' => 5, 'status' => 'approved']);
    }

    public function test_recommendation_get_is_read_only_and_security_headers_exist(): void
    {
        $guest = GuestVisitor::create(['kode_guest' => 'GST-TEST-READ']);
        $wisata = $this->wisata();
        HasilRekomendasi::create(['guest_visitor_id' => $guest->id, 'wisata_id' => $wisata->id, 'ranking' => 1]);
        $before = HasilRekomendasi::first()->updated_at;

        $this->withSession(['kode_guest' => $guest->kode_guest])
            ->get(route('wisatawan.rekomendasi.hasil'))
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN');

        $this->assertDatabaseCount('hasil_rekomendasi', 1);
        $this->assertTrue($before->equalTo(HasilRekomendasi::first()->updated_at));
    }

    public function test_admin_login_is_rate_limited(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('admin.login.process'), ['email' => 'nobody@example.com', 'password' => 'wrong']);
        }
        $this->post(route('admin.login.process'), ['email' => 'nobody@example.com', 'password' => 'wrong'])
            ->assertSessionHasErrors('email');
    }

    private function wisata(): Wisata
    {
        $kategori = KategoriWisata::create(['nama_kategori' => 'Aman', 'slug' => 'aman']);

        return Wisata::create([
            'kategori_wisata_id' => $kategori->id, 'nama_wisata' => 'Wisata Aman',
            'slug' => 'wisata-aman', 'jenis_wisata' => 'Alam', 'alamat' => 'Makassar', 'status' => 'aktif',
        ]);
    }
}
