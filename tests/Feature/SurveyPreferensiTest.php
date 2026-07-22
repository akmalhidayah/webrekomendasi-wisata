<?php

namespace Tests\Feature;

use App\Models\GuestVisitor;
use App\Models\Wisata;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SurveyPreferensiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_survey_without_login_and_is_created_automatically(): void
    {
        $this->seed();

        $this->get(route('wisatawan.survey.index'))
            ->assertOk()
            ->assertSee('Survei Preferensi Wisata');

        $this->assertGuest();
        $this->assertDatabaseCount('guest_visitors', 1);
        $this->assertNotNull(session('kode_guest'));
    }

    public function test_survey_uses_simple_responsive_icon_loader(): void
    {
        $this->seed();

        $this->get(route('wisatawan.survey.index'))
            ->assertOk()
            ->assertSee('class="loading-visual"', false)
            ->assertSee('bi-compass-fill', false)
            ->assertSee('aria-label="Memproses rekomendasi"', false)
            ->assertSeeInOrder(['Preferensi', 'Kecocokan', 'Biaya', 'Hasil'])
            ->assertSee('Mencari destinasi cocok...', false)
            ->assertSee('Menyiapkan hasil...', false)
            ->assertDontSee('Sedang Menghitung Rekomendasi')
            ->assertDontSee('Membentuk matriks rating pengguna')
            ->assertDontSee('loading-steps', false)
            ->assertDontSee('skeleton-card', false);
    }

    public function test_valid_ratings_are_saved_for_automatically_created_guest(): void
    {
        $this->seed();
        $this->get(route('wisatawan.survey.index'))->assertOk();
        $ratings = Wisata::whereIn('id', session('survey_wisata_ids'))->get()->map(
            fn (Wisata $wisata, int $index) => [
                'wisata_id' => $wisata->id,
                'rating_awal' => ($index % 5) + 1,
            ]
        )->all();

        $this->post(route('wisatawan.survey.store'), [
            'ratings' => $ratings,
            'budget_min' => 100000,
            'budget_max' => 1000000,
            'butuh_hotel' => 0,
            'jumlah_malam' => 1,
            'is_location_allowed' => 0,
        ])
            ->assertRedirect(route('wisatawan.rekomendasi.hasil'));

        $guest = GuestVisitor::firstOrFail();
        $this->assertDatabaseCount('guest_visitors', 1);
        $this->assertDatabaseCount('survey_preferensi', 10);
        $this->assertSame(10, $guest->surveyPreferensi()->count());
        $this->assertSame('100000.00', $guest->budget_min);
        $this->assertFalse($guest->butuh_hotel);
        $this->assertDatabaseHas('log_aktivitas', [
            'guest_visitor_id' => $guest->id,
            'aktivitas' => 'Survey Preferensi Disimpan',
        ]);
        $this->assertSame(5, $guest->hasilRekomendasi()->count());
    }
}
