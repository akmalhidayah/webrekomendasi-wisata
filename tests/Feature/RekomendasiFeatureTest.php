<?php

namespace Tests\Feature;

use App\Models\GuestVisitor;
use App\Models\SurveyPreferensi;
use App\Models\User;
use App\Models\Wisata;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RekomendasiFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_without_survey_is_redirected_to_survey(): void
    {
        $this->seed();

        $this->get(route('wisatawan.rekomendasi.index'))
            ->assertRedirect(route('wisatawan.survey.index'));
    }

    public function test_guest_with_survey_can_process_and_save_recommendations(): void
    {
        $this->seed();
        $guest = GuestVisitor::create(['kode_guest' => 'GST-20260623-ABCDEF']);
        Wisata::where('status', 'aktif')->limit(10)->get()->each(function (Wisata $wisata) use ($guest) {
            SurveyPreferensi::create([
                'guest_visitor_id' => $guest->id,
                'wisata_id' => $wisata->id,
                'rating_awal' => 4,
            ]);
        });

        $this->withSession(['kode_guest' => $guest->kode_guest])
            ->post(route('wisatawan.rekomendasi.proses'))
            ->assertRedirect(route('wisatawan.rekomendasi.hasil'));

        $this->assertSame(5, $guest->hasilRekomendasi()->count());
        $this->assertDatabaseHas('log_aktivitas', [
            'guest_visitor_id' => $guest->id,
            'aktivitas' => 'Rekomendasi Diproses',
        ]);
        $this->get(route('wisatawan.rekomendasi.hasil'))
            ->assertOk()
            ->assertSee('Hasil Rekomendasi Wisata');

        $admin = User::where('email', 'admin@gmail.com')->firstOrFail();
        $this->actingAs($admin)->get(route('admin.hasil-rekomendasi.index'))->assertOk();
        $this->get(route('admin.hasil-rekomendasi.show', $guest))->assertOk();
    }
}
