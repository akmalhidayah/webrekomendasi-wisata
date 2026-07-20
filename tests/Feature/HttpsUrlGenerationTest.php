<?php

namespace Tests\Feature;

use App\Models\GuestVisitor;
use App\Models\KategoriWisata;
use App\Models\SurveyPreferensi;
use App\Models\User;
use App\Models\Wisata;
use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class HttpsUrlGenerationTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        URL::forceHttps(false);
        URL::forceRootUrl(null);
        parent::tearDown();
    }

    public function test_global_url_generators_assets_storage_forms_and_pagination_use_https(): void
    {
        $this->enableProductionHttps();
        $this->seed();
        $kategori = KategoriWisata::create([
            'nama_kategori' => 'HTTPS Pagination',
            'slug' => 'https-pagination',
        ]);
        foreach (range(1, 13) as $number) {
            Wisata::create([
                'kategori_wisata_id' => $kategori->id,
                'nama_wisata' => "HTTPS Pagination {$number}",
                'slug' => "https-pagination-{$number}",
                'jenis_wisata' => 'Test',
                'alamat' => 'Makassar',
                'status' => 'aktif',
            ]);
        }

        $this->assertSame('https://wisataku.web.id', route('wisatawan.home'));
        $this->assertSame('https://wisataku.web.id/wisata', route('wisatawan.wisata.index'));
        $this->assertSame('https://wisataku.web.id/rekomendasi/proses', route('wisatawan.rekomendasi.proses'));
        $this->assertSame('https://wisataku.web.id/admin/login', route('admin.login'));
        $this->assertStringStartsWith('https://wisataku.web.id/', asset('images/default-wisata.svg'));
        $this->assertSame('https://wisataku.web.id/storage/wisata/test.jpg', Storage::disk('public')->url('wisata/test.jpg'));
        $legacyInternalImage = new Wisata(['foto_utama' => 'http://wisataku.web.id/storage/wisata/legacy.jpg']);
        $this->assertSame('https://wisataku.web.id/storage/wisata/legacy.jpg', $legacyInternalImage->foto_url);

        $response = $this->get('https://wisataku.web.id/wisata?lat=-5.1&lng=119.4&search=HTTPS%20Pagination&kategori='.$kategori->id.'&page=1');
        $response->assertOk()
            ->assertDontSee('http://wisataku.web.id', false)
            ->assertSee('https://wisataku.web.id/wisata?', false)
            ->assertSee('lat=-5.1', false)
            ->assertSee('lng=119.4', false)
            ->assertSee('search=HTTPS%20Pagination', false)
            ->assertSee('kategori='.$kategori->id, false)
            ->assertSee('page=2', false);

        $this->get('https://wisataku.web.id/rekomendasi/survey')
            ->assertOk()
            ->assertSee('action="https://wisataku.web.id/rekomendasi/survey"', false)
            ->assertDontSee('http://wisataku.web.id', false);
    }

    public function test_forwarded_https_headers_are_trusted(): void
    {
        Route::get('/_https-proxy-check', fn (Request $request) => response()->json([
            'secure' => $request->isSecure(),
            'url' => route('wisatawan.wisata.index'),
        ]));

        $this->withServerVariables(['REMOTE_ADDR' => '127.0.0.1'])
            ->withHeaders([
                'X-Forwarded-Proto' => 'https',
                'X-Forwarded-Host' => 'wisataku.web.id',
                'X-Forwarded-Port' => '443',
            ])
            ->get('/_https-proxy-check')
            ->assertOk()
            ->assertJson([
                'secure' => true,
                'url' => 'https://wisataku.web.id/wisata',
            ]);
    }

    public function test_generate_recommendation_and_admin_logout_redirect_to_https(): void
    {
        $this->enableProductionHttps();
        $this->seed();
        $guest = GuestVisitor::create(['kode_guest' => 'GST-HTTPS-REDIRECT']);

        Wisata::where('status', 'aktif')->limit(10)->get()->each(function (Wisata $wisata) use ($guest) {
            SurveyPreferensi::create([
                'guest_visitor_id' => $guest->id,
                'wisata_id' => $wisata->id,
                'rating_awal' => 4,
            ]);
        });

        $this->withSession(['kode_guest' => $guest->kode_guest])
            ->post('https://wisataku.web.id/rekomendasi/proses')
            ->assertRedirect('https://wisataku.web.id/rekomendasi/hasil');

        $admin = User::where('role', 'admin')->firstOrFail();
        $this->actingAs($admin)
            ->post('https://wisataku.web.id/admin/logout')
            ->assertRedirect('https://wisataku.web.id/admin/login');
    }

    public function test_local_environment_can_continue_generating_http_urls(): void
    {
        config()->set('app.force_https', false);
        config()->set('app.url', 'http://localhost');
        URL::forceHttps(false);
        URL::forceRootUrl('http://localhost');

        $this->assertSame('http://localhost/wisata', route('wisatawan.wisata.index'));
    }

    private function enableProductionHttps(): void
    {
        config()->set('app.force_https', true);
        config()->set('app.url', 'https://wisataku.web.id');
        config()->set('filesystems.disks.public.url', 'https://wisataku.web.id/storage');
        (new AppServiceProvider($this->app))->boot();
    }
}
