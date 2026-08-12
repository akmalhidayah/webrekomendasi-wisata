<?php

namespace Tests\Feature;

use App\Models\GuestVisitor;
use App\Models\HasilRekomendasi;
use App\Models\KategoriWisata;
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

    public function test_result_page_uses_saved_ranking_instead_of_resorting_by_current_maps_rating(): void
    {
        $kategori = KategoriWisata::create(['nama_kategori' => 'Pantai', 'slug' => 'pantai']);
        $guest = GuestVisitor::create(['kode_guest' => 'GST-20260623-RANK']);
        $firstRank = Wisata::create([
            'kategori_wisata_id' => $kategori->id,
            'nama_wisata' => 'Wisata Ranking Satu',
            'slug' => 'wisata-ranking-satu',
            'jenis_wisata' => 'Pantai',
            'alamat' => 'Makassar',
            'status' => 'aktif',
            'rating_maps' => 3.5,
        ]);
        $secondRank = Wisata::create([
            'kategori_wisata_id' => $kategori->id,
            'nama_wisata' => 'Wisata Ranking Dua',
            'slug' => 'wisata-ranking-dua',
            'jenis_wisata' => 'Pantai',
            'alamat' => 'Makassar',
            'status' => 'aktif',
            'rating_maps' => 5.0,
        ]);

        HasilRekomendasi::create([
            'guest_visitor_id' => $guest->id,
            'wisata_id' => $firstRank->id,
            'nilai_prediksi' => 4.0,
            'nilai_similarity' => 0.8,
            'ranking' => 1,
            'metode' => 'Hybrid Collaborative Filtering',
        ]);
        HasilRekomendasi::create([
            'guest_visitor_id' => $guest->id,
            'wisata_id' => $secondRank->id,
            'nilai_prediksi' => 4.5,
            'nilai_similarity' => 0.9,
            'ranking' => 2,
            'metode' => 'Hybrid Collaborative Filtering',
        ]);

        $this->withSession(['kode_guest' => $guest->kode_guest])
            ->get(route('wisatawan.rekomendasi.hasil'))
            ->assertOk()
            ->assertSeeInOrder(['Wisata Ranking Satu', 'Wisata Ranking Dua']);
    }

    public function test_legacy_all_low_survey_is_rejected_before_recommendation_generation(): void
    {
        $this->seed();
        $guest = GuestVisitor::create(['kode_guest' => 'GST-LEGACY-LOW']);
        $wisata = Wisata::where('status', 'aktif')->limit(10)->get();
        foreach ($wisata as $item) {
            SurveyPreferensi::create([
                'guest_visitor_id' => $guest->id,
                'wisata_id' => $item->id,
                'rating_awal' => 2,
            ]);
        }
        HasilRekomendasi::create([
            'guest_visitor_id' => $guest->id,
            'wisata_id' => Wisata::whereNotIn('id', $wisata->pluck('id'))->firstOrFail()->id,
            'nilai_prediksi' => 5,
            'ranking' => 1,
            'metode' => 'Stale',
        ]);

        $this->withSession(['kode_guest' => $guest->kode_guest])
            ->post(route('wisatawan.rekomendasi.proses'))
            ->assertRedirect(route('wisatawan.survey.index'))
            ->assertSessionHasErrors('preference_pattern');

        $this->assertSame(10, $guest->surveyPreferensi()->count());
        $this->assertSame(0, $guest->hasilRekomendasi()->count());
    }
}
