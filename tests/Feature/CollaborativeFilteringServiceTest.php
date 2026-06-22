<?php

namespace Tests\Feature;

use App\Models\GuestVisitor;
use App\Models\KategoriWisata;
use App\Models\SurveyPreferensi;
use App\Models\Wisata;
use App\Services\CollaborativeFilteringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollaborativeFilteringServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_calculates_cosine_similarity_correctly(): void
    {
        $similarity = app(CollaborativeFilteringService::class)->calculateCosineSimilarity(
            [1 => 5, 2 => 4, 3 => 3],
            [1 => 5, 2 => 4, 3 => 2, 4 => 5],
        );

        $this->assertEqualsWithDelta(0.9908, $similarity, 0.0001);
        $this->assertSame(0.0, app(CollaborativeFilteringService::class)
            ->calculateCosineSimilarity([1 => 5], [2 => 5]));
    }

    public function test_it_generates_and_saves_recommendations_when_surveys_are_sufficient(): void
    {
        [$first, $second, $third, $fourth] = $this->createWisata(4);
        $target = $this->createGuest('TARGET');
        $neighbor = $this->createGuest('NEIGHBOR');
        $this->rate($target, [$first->id => 5, $second->id => 4]);
        $this->rate($neighbor, [$first->id => 5, $second->id => 4, $third->id => 5, $fourth->id => 2]);

        $recommendations = app(CollaborativeFilteringService::class)->generateRecommendations($target, 2);

        $this->assertCount(2, $recommendations);
        $this->assertSame($third->id, $recommendations[0]['wisata_id']);
        $this->assertSame('Collaborative Filtering', $recommendations[0]['metode']);
        $this->assertDatabaseCount('hasil_rekomendasi', 2);
    }

    public function test_it_uses_and_saves_fallback_when_neighbor_data_is_insufficient(): void
    {
        [$first] = $this->createWisata(3);
        $target = $this->createGuest('TARGET');
        $this->rate($target, [$first->id => 5]);

        $recommendations = app(CollaborativeFilteringService::class)->generateRecommendations($target, 2);

        $this->assertCount(2, $recommendations);
        $this->assertSame('Collaborative Filtering - Fallback', $recommendations[0]['metode']);
        $this->assertSame(3.0, $recommendations[0]['nilai_prediksi']);
        $this->assertDatabaseCount('hasil_rekomendasi', 2);
    }

    private function createGuest(string $suffix): GuestVisitor
    {
        return GuestVisitor::create(['kode_guest' => "GST-20260623-{$suffix}"]);
    }

    private function createWisata(int $count): array
    {
        $kategori = KategoriWisata::create(['nama_kategori' => 'Test', 'slug' => 'test']);

        return collect(range(1, $count))->map(fn ($number) => Wisata::create([
            'kategori_wisata_id' => $kategori->id,
            'nama_wisata' => "Wisata {$number}",
            'slug' => "wisata-{$number}",
            'jenis_wisata' => 'Test',
            'alamat' => 'Makassar',
            'status' => 'aktif',
        ]))->all();
    }

    private function rate(GuestVisitor $guest, array $ratings): void
    {
        foreach ($ratings as $wisataId => $rating) {
            SurveyPreferensi::create([
                'guest_visitor_id' => $guest->id,
                'wisata_id' => $wisataId,
                'rating_awal' => $rating,
            ]);
        }
    }
}
