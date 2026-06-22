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

        $this->post(route('wisatawan.survey.store'), ['ratings' => $ratings])
            ->assertRedirect(route('wisatawan.survey.success'));

        $guest = GuestVisitor::firstOrFail();
        $this->assertDatabaseCount('guest_visitors', 1);
        $this->assertDatabaseCount('survey_preferensi', 10);
        $this->assertSame(10, $guest->surveyPreferensi()->count());
        $this->assertDatabaseHas('log_aktivitas', [
            'guest_visitor_id' => $guest->id,
            'aktivitas' => 'Survey Preferensi Disimpan',
        ]);
    }
}
