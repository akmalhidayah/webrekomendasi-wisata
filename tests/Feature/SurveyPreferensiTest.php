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

    public function test_survey_contains_hidden_location_map_and_optional_budget_fields(): void
    {
        $this->seed();

        $this->get(route('wisatawan.survey.index'))
            ->assertOk()
            ->assertSee('id="surveyMapWrapper" hidden', false)
            ->assertSee('id="surveyLocationMap"', false)
            ->assertSee('id="userLatitude"', false)
            ->assertSee('id="userLongitude"', false)
            ->assertSee('id="isLocationAllowed"', false)
            ->assertSee('Budget minimum (opsional)')
            ->assertSee('Budget maksimum (opsional)')
            ->assertDontSee('name="budget_min" value="100000"', false)
            ->assertDontSee('name="budget_max" value="1000000"', false);
    }

    public function test_all_low_and_all_middle_ratings_are_rejected_without_replacing_survey(): void
    {
        $this->seed();

        foreach ([1, 3] as $ratingValue) {
            $this->get(route('wisatawan.survey.index'))->assertOk();
            $ratings = collect(session('survey_wisata_ids'))->map(fn ($id) => [
                'wisata_id' => $id,
                'rating_awal' => $ratingValue,
            ])->all();

            $response = $this->post(route('wisatawan.survey.store'), [
                'ratings' => $ratings,
                'budget_min' => null,
                'budget_max' => null,
                'butuh_hotel' => 0,
                'jumlah_malam' => 1,
                'is_location_allowed' => 0,
            ]);

            $response->assertRedirect()
                ->assertSessionHasErrors('preference_pattern')
                ->assertSessionHasInput('ratings.0.rating_awal', $ratingValue);
            $this->assertDatabaseCount('survey_preferensi', 0);
            $this->assertDatabaseCount('hasil_rekomendasi', 0);

            $this->get(route('wisatawan.survey.index'))
                ->assertOk()
                ->assertSee('value="'.$ratingValue.'" selected', false);
        }
    }

    public function test_broad_interest_accepts_empty_budget_and_uses_special_method(): void
    {
        $this->seed();
        $this->get(route('wisatawan.survey.index'))->assertOk();
        $ratings = collect(session('survey_wisata_ids'))->values()->map(fn ($id, $index) => [
            'wisata_id' => $id,
            'rating_awal' => $index % 2 === 0 ? 4 : 5,
        ])->all();

        $this->post(route('wisatawan.survey.store'), [
            'ratings' => $ratings,
            'budget_min' => null,
            'budget_max' => null,
            'butuh_hotel' => 0,
            'jumlah_malam' => 1,
            'is_location_allowed' => 0,
        ])->assertRedirect(route('wisatawan.rekomendasi.hasil'));

        $guest = GuestVisitor::firstOrFail();
        $this->assertNull($guest->budget_min);
        $this->assertNull($guest->budget_max);
        $this->assertSame(5, $guest->hasilRekomendasi()->count());
        $this->assertSame(['Broad Interest'], $guest->hasilRekomendasi()->distinct()->pluck('metode')->all());
        $this->assertTrue($guest->hasilRekomendasi()->whereNotNull('skor_cf')->doesntExist());

        $this->get(route('wisatawan.rekomendasi.hasil'))
            ->assertOk()
            ->assertSee('Mode Minat Luas')
            ->assertSee('Skor CF')
            ->assertSee('Mode Minat Luas:')
            ->assertDontSee('Formula: 50% CF');
    }

    public function test_saved_survey_and_guest_preferences_are_restored_on_budget_step(): void
    {
        $this->seed();
        $guest = GuestVisitor::create([
            'kode_guest' => 'GST-PRESERVE-SURVEY',
            'budget_min' => 125000,
            'budget_max' => 700000,
            'butuh_hotel' => true,
            'jumlah_malam' => 2,
            'user_latitude' => -5.1477,
            'user_longitude' => 119.4327,
            'is_location_allowed' => true,
        ]);
        $wisata = Wisata::where('status', 'aktif')->limit(10)->get();
        foreach ($wisata as $index => $item) {
            $guest->surveyPreferensi()->create([
                'wisata_id' => $item->id,
                'rating_awal' => ($index % 5) + 1,
            ]);
        }

        $response = $this->withSession(['kode_guest' => $guest->kode_guest])
            ->get(route('wisatawan.survey.index', ['step' => 2]));

        $response->assertOk()
            ->assertSee('value="125000.00"', false)
            ->assertSee('value="700000.00"', false)
            ->assertSee('value="-5.1477000"', false)
            ->assertSee('value="119.4327000"', false);
        $this->assertSame(2, $response->viewData('initialStep'));
        $this->assertSame($wisata->pluck('id')->all(), $response->viewData('wisata')->pluck('id')->all());
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
