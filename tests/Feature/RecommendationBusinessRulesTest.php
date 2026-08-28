<?php

namespace Tests\Feature;

use App\Models\GuestVisitor;
use App\Models\Hotel;
use App\Models\KategoriWisata;
use App\Models\SurveyPreferensi;
use App\Models\Wisata;
use App\Services\CollaborativeFilteringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class RecommendationBusinessRulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_preference_pattern_classifier_does_not_create_fake_similarity(): void
    {
        $service = app(CollaborativeFilteringService::class);

        $this->assertSame('all_low', $service->classifyPreferencePattern([1, 1, 2, 1, 2, 2, 1, 1, 2, 1]));
        $this->assertSame('all_middle', $service->classifyPreferencePattern(array_fill(0, 10, 3)));
        $this->assertSame('broad_interest', $service->classifyPreferencePattern([4, 5, 5, 4, 5, 4, 5, 5, 4, 5]));
        $this->assertSame('varied', $service->classifyPreferencePattern([1, 2, 3, 4, 5, 1, 3, 5, 2, 4]));
    }

    public function test_hard_budget_excludes_every_over_budget_candidate(): void
    {
        [$guest] = $this->createScenario(budgetMax: 220000, candidateCosts: [175000, 220000, 220001, 350000]);

        $outcome = app(CollaborativeFilteringService::class)->generateRecommendationOutcome($guest);

        $this->assertSame('success', $outcome['status']);
        $this->assertNotEmpty($outcome['recommendations']);
        foreach ($outcome['recommendations'] as $recommendation) {
            $this->assertLessThanOrEqual(220000, $recommendation['total_estimasi_budget']);
        }
        $this->assertSame(0, $guest->hasilRekomendasi()->where('total_estimasi_budget', '>', 220000)->count());
    }

    public function test_uniform_ratings_use_quality_and_popularity_for_ranking(): void
    {
        $category = KategoriWisata::create([
            'nama_kategori' => 'Kategori Rating Seragam',
            'slug' => 'kategori-rating-seragam',
        ]);
        $guest = GuestVisitor::create([
            'kode_guest' => 'GST-UNIFORM-RANKING',
            'butuh_hotel' => false,
        ]);
        $rated = collect(range(1, 10))->map(fn ($number) => Wisata::create([
            'kategori_wisata_id' => $category->id,
            'nama_wisata' => "Wisata Dinilai Seragam {$number}",
            'slug' => "wisata-dinilai-seragam-{$number}",
            'jenis_wisata' => 'Uji',
            'alamat' => 'Makassar',
            'status' => 'aktif',
        ]));
        foreach ($rated as $wisata) {
            $guest->surveyPreferensi()->create([
                'wisata_id' => $wisata->id,
                'rating_awal' => 1,
            ]);
        }

        $popular = Wisata::create([
            'kategori_wisata_id' => $category->id,
            'nama_wisata' => 'Destinasi Rating Tinggi Populer',
            'slug' => 'destinasi-rating-tinggi-populer',
            'jenis_wisata' => 'Uji',
            'alamat' => 'Makassar',
            'rating_maps' => 5.0,
            'jumlah_rating_maps' => 100,
            'status' => 'aktif',
        ]);
        $lessPopular = Wisata::create([
            'kategori_wisata_id' => $category->id,
            'nama_wisata' => 'Destinasi Rating Tinggi Kurang Populer',
            'slug' => 'destinasi-rating-tinggi-kurang-populer',
            'jenis_wisata' => 'Uji',
            'alamat' => 'Makassar',
            'rating_maps' => 5.0,
            'jumlah_rating_maps' => 1,
            'status' => 'aktif',
        ]);
        $lowerRated = Wisata::create([
            'kategori_wisata_id' => $category->id,
            'nama_wisata' => 'Destinasi Rating Lebih Rendah',
            'slug' => 'destinasi-rating-lebih-rendah',
            'jenis_wisata' => 'Uji',
            'alamat' => 'Makassar',
            'rating_maps' => 4.0,
            'jumlah_rating_maps' => 1000,
            'status' => 'aktif',
        ]);

        $outcome = app(CollaborativeFilteringService::class)->generateRecommendationOutcome($guest, 3);

        $this->assertSame('success', $outcome['status']);
        $this->assertSame('all_low', $outcome['pattern']);
        $this->assertSame(
            [$popular->id, $lessPopular->id, $lowerRated->id],
            collect($outcome['recommendations'])->pluck('wisata_id')->all(),
        );
        $this->assertSame(
            ['Quality Budget & Popularity'],
            $guest->hasilRekomendasi()->distinct()->pluck('metode')->all(),
        );
    }

    public function test_budget_insufficient_clears_stale_results_and_reports_minimum(): void
    {
        [$guest, $rated, $candidates] = $this->createScenario(budgetMax: 100000, candidateCosts: [175000, 250000]);
        $guest->hasilRekomendasi()->create([
            'wisata_id' => $candidates->last()->id,
            'nilai_prediksi' => 5,
            'ranking' => 1,
            'metode' => 'Stale',
        ]);

        $outcome = app(CollaborativeFilteringService::class)->generateRecommendationOutcome($guest);

        $this->assertSame('budget_insufficient', $outcome['status']);
        $this->assertSame(175000.0, $outcome['minimum_required_budget']);
        $this->assertSame([], $outcome['recommendations']);
        $this->assertDatabaseCount('hasil_rekomendasi', 0);

        $this->withSession([
            'kode_guest' => $guest->kode_guest,
            'recommendation_issue' => [
                'type' => 'budget_insufficient',
                'budget_max' => 100000,
                'minimum_required_budget' => 175000,
                'hotel_required' => false,
            ],
        ])->get(route('wisatawan.rekomendasi.hasil'))
            ->assertOk()
            ->assertSee('Budget belum mencukupi')
            ->assertSee('Rp 100.000')
            ->assertSee('Rp 175.000')
            ->assertSee('Ubah Budget');
    }

    public function test_hotel_is_mandatory_and_combined_cost_obeys_budget(): void
    {
        [$guest, $rated, $candidates] = $this->createScenario(
            budgetMax: 300000,
            candidateCosts: [100000, 120000],
            hotelRequired: true,
            nights: 2,
        );
        $hotel = Hotel::create([
            'nama_hotel' => 'Hotel Hemat',
            'slug' => 'hotel-hemat',
            'harga_min' => 75000,
            'harga_max' => 100000,
            'status' => 'aktif',
        ]);
        $candidates->first()->hotels()->attach($hotel->id, ['urutan' => 1]);

        $outcome = app(CollaborativeFilteringService::class)->generateRecommendationOutcome($guest);

        $this->assertSame('success', $outcome['status']);
        $this->assertCount(1, $outcome['recommendations']);
        $recommendation = $outcome['recommendations'][0];
        $this->assertSame($hotel->id, $recommendation['hotel_id']);
        $this->assertSame(250000.0, $recommendation['total_estimasi_budget']);
        $this->assertLessThanOrEqual(300000, $recommendation['total_estimasi_budget']);
        $this->assertNotContains($candidates->last()->id, collect($outcome['recommendations'])->pluck('wisata_id')->all());
    }

    public function test_missing_active_hotel_has_a_distinct_outcome(): void
    {
        [$guest] = $this->createScenario(
            budgetMax: null,
            candidateCosts: [100000, 120000],
            hotelRequired: true,
        );

        $outcome = app(CollaborativeFilteringService::class)->generateRecommendationOutcome($guest);

        $this->assertSame('hotel_unavailable', $outcome['status']);
        $this->assertNull($outcome['minimum_required_budget']);
        $this->assertDatabaseCount('hasil_rekomendasi', 0);
    }

    public function test_without_hotel_action_preserves_survey_budget_and_location(): void
    {
        [$guest] = $this->createScenario(
            budgetMax: 300000,
            candidateCosts: [100000, 150000],
            hotelRequired: true,
            nights: 3,
        );
        $guest->update([
            'budget_min' => 50000,
            'user_latitude' => -5.1477,
            'user_longitude' => 119.4327,
            'is_location_allowed' => true,
        ]);
        $surveyIds = $guest->surveyPreferensi()->pluck('wisata_id')->all();

        $this->withSession(['kode_guest' => $guest->kode_guest])
            ->post(route('wisatawan.rekomendasi.tanpa-hotel'))
            ->assertRedirect(route('wisatawan.rekomendasi.hasil'));

        $guest->refresh();
        $this->assertFalse($guest->butuh_hotel);
        $this->assertSame(1, $guest->jumlah_malam);
        $this->assertSame('50000.00', $guest->budget_min);
        $this->assertSame('300000.00', $guest->budget_max);
        $this->assertSame('-5.1477000', $guest->user_latitude);
        $this->assertSame('119.4327000', $guest->user_longitude);
        $this->assertSame($surveyIds, $guest->surveyPreferensi()->pluck('wisata_id')->all());
        $this->assertSame(10, $guest->surveyPreferensi()->count());
        $this->assertGreaterThan(0, $guest->hasilRekomendasi()->count());
    }

    /**
     * @return array{GuestVisitor, Collection, Collection}
     */
    private function createScenario(
        ?float $budgetMax,
        array $candidateCosts,
        bool $hotelRequired = false,
        int $nights = 1,
    ): array {
        $category = KategoriWisata::create([
            'nama_kategori' => 'Kategori Aturan Bisnis',
            'slug' => 'kategori-aturan-bisnis',
        ]);
        $guest = GuestVisitor::create([
            'kode_guest' => 'GST-BUSINESS-'.str()->random(6),
            'budget_max' => $budgetMax,
            'butuh_hotel' => $hotelRequired,
            'jumlah_malam' => $nights,
        ]);
        $rated = collect(range(1, 10))->map(fn ($number) => Wisata::create([
            'kategori_wisata_id' => $category->id,
            'nama_wisata' => "Wisata Dinilai {$number}",
            'slug' => "wisata-dinilai-{$number}",
            'jenis_wisata' => 'Uji',
            'alamat' => 'Makassar',
            'total_estimasi_biaya' => 50000,
            'status' => 'aktif',
        ]));
        foreach ($rated as $index => $wisata) {
            SurveyPreferensi::create([
                'guest_visitor_id' => $guest->id,
                'wisata_id' => $wisata->id,
                'rating_awal' => ($index % 5) + 1,
            ]);
        }
        $candidates = collect($candidateCosts)->values()->map(fn ($cost, $index) => Wisata::create([
            'kategori_wisata_id' => $category->id,
            'nama_wisata' => 'Kandidat '.($index + 1),
            'slug' => 'kandidat-'.($index + 1),
            'jenis_wisata' => 'Uji',
            'alamat' => 'Makassar',
            'total_estimasi_biaya' => $cost,
            'rating_maps' => 4.5,
            'jumlah_rating_maps' => 100,
            'status' => 'aktif',
        ]));

        return [$guest, $rated, $candidates];
    }
}
