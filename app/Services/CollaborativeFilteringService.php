<?php

namespace App\Services;

use App\Models\GuestVisitor;
use App\Models\HasilRekomendasi;
use App\Models\SurveyPreferensi;
use App\Models\Wisata;
use Illuminate\Support\Facades\DB;

class CollaborativeFilteringService
{
    /**
     * Menghasilkan rekomendasi User-Based Collaborative Filtering untuk satu guest.
     */
    public function generateRecommendations(GuestVisitor $guestVisitor, int $limit = 5): array
    {
        $targetRatings = $this->getTargetRatings($guestVisitor);

        if ($targetRatings === []) {
            return $this->getFallbackRecommendations($guestVisitor, $limit);
        }

        $matrix = $this->buildUserItemMatrix();
        $similarities = $this->calculateSimilarities($guestVisitor->id, $matrix);

        if ($similarities === []) {
            return $this->getFallbackRecommendations($guestVisitor, $limit);
        }

        $predictions = [];
        foreach ($this->getCandidateWisataIds($targetRatings) as $wisataId) {
            $prediction = $this->predictRatingForWisata($wisataId, $similarities, $matrix);

            if ($prediction['nilai_prediksi'] > 0) {
                $predictions[] = ['wisata_id' => $wisataId, ...$prediction];
            }
        }

        if ($predictions === []) {
            return $this->getFallbackRecommendations($guestVisitor, $limit);
        }

        usort($predictions, fn (array $first, array $second) => $second['nilai_prediksi'] <=> $first['nilai_prediksi']);
        $recommendations = array_slice($predictions, 0, max(1, $limit));
        $wisata = Wisata::with('kategoriWisata')->whereIn('id', array_column($recommendations, 'wisata_id'))->get()->keyBy('id');

        foreach ($recommendations as $index => &$recommendation) {
            $recommendation['ranking'] = $index + 1;
            $recommendation['metode'] = 'Collaborative Filtering';
            $recommendation['wisata'] = $wisata->get($recommendation['wisata_id']);
        }
        unset($recommendation);

        $this->saveRecommendations($guestVisitor, $recommendations);

        return $recommendations;
    }

    /** @return array<int, array<int, int>> */
    public function buildUserItemMatrix(): array
    {
        $matrix = [];

        SurveyPreferensi::query()
            ->select(['guest_visitor_id', 'wisata_id', 'rating_awal'])
            ->orderBy('guest_visitor_id')
            ->get()
            ->each(function (SurveyPreferensi $rating) use (&$matrix) {
                $matrix[$rating->guest_visitor_id][$rating->wisata_id] = $rating->rating_awal;
            });

        return $matrix;
    }

    /** @return array<int, int> */
    public function getTargetRatings(GuestVisitor $guestVisitor): array
    {
        return $guestVisitor->surveyPreferensi()
            ->pluck('rating_awal', 'wisata_id')
            ->map(fn ($rating) => (int) $rating)
            ->all();
    }

    /**
     * Cosine similarity dihitung hanya pada wisata yang dinilai oleh kedua guest:
     * sum(u_i * v_i) / (sqrt(sum(u_i^2)) * sqrt(sum(v_i^2))).
     */
    public function calculateCosineSimilarity(array $targetRatings, array $otherRatings): float
    {
        $commonWisataIds = array_intersect(array_keys($targetRatings), array_keys($otherRatings));

        if ($commonWisataIds === []) {
            return 0.0;
        }

        $dotProduct = 0.0;
        $targetMagnitude = 0.0;
        $otherMagnitude = 0.0;

        foreach ($commonWisataIds as $wisataId) {
            $target = (float) $targetRatings[$wisataId];
            $other = (float) $otherRatings[$wisataId];
            $dotProduct += $target * $other;
            $targetMagnitude += $target ** 2;
            $otherMagnitude += $other ** 2;
        }

        $denominator = sqrt($targetMagnitude) * sqrt($otherMagnitude);

        return $denominator > 0 ? round($dotProduct / $denominator, 4) : 0.0;
    }

    /** @return array<int, float> */
    public function calculateSimilarities(int $targetGuestId, array $matrix): array
    {
        $targetRatings = $matrix[$targetGuestId] ?? [];
        $similarities = [];

        foreach ($matrix as $guestId => $ratings) {
            if ((int) $guestId === $targetGuestId) {
                continue;
            }

            $similarity = $this->calculateCosineSimilarity($targetRatings, $ratings);
            if ($similarity > 0) {
                $similarities[(int) $guestId] = $similarity;
            }
        }

        arsort($similarities);

        return $similarities;
    }

    /**
     * Prediksi adalah weighted average: sum(similarity * rating) / sum(abs(similarity)).
     * Similarity keluaran memakai nilai terbesar dari tetangga yang berkontribusi.
     *
     * @return array{nilai_prediksi: float, nilai_similarity: float}
     */
    public function predictRatingForWisata(int $wisataId, array $similarities, array $matrix): array
    {
        $weightedRating = 0.0;
        $similarityTotal = 0.0;
        $contributingSimilarities = [];

        foreach ($similarities as $guestId => $similarity) {
            if ($similarity <= 0 || ! isset($matrix[$guestId][$wisataId])) {
                continue;
            }

            $weightedRating += $similarity * $matrix[$guestId][$wisataId];
            $similarityTotal += abs($similarity);
            $contributingSimilarities[] = $similarity;
        }

        if ($similarityTotal <= 0) {
            return ['nilai_prediksi' => 0.0, 'nilai_similarity' => 0.0];
        }

        return [
            'nilai_prediksi' => round($weightedRating / $similarityTotal, 4),
            'nilai_similarity' => round(max($contributingSimilarities), 4),
        ];
    }

    /** @return array<int, int> */
    public function getCandidateWisataIds(array $targetRatings): array
    {
        return Wisata::where('status', 'aktif')
            ->whereNotIn('id', array_keys($targetRatings))
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    public function saveRecommendations(GuestVisitor $guestVisitor, array $recommendations): void
    {
        DB::transaction(function () use ($guestVisitor, $recommendations) {
            $guestVisitor->hasilRekomendasi()->delete();

            foreach ($recommendations as $recommendation) {
                HasilRekomendasi::create([
                    'guest_visitor_id' => $guestVisitor->id,
                    'wisata_id' => $recommendation['wisata_id'],
                    'nilai_prediksi' => round($recommendation['nilai_prediksi'], 4),
                    'nilai_similarity' => round($recommendation['nilai_similarity'], 4),
                    'ranking' => $recommendation['ranking'],
                    'metode' => $recommendation['metode'],
                ]);
            }
        });
    }

    public function getFallbackRecommendations(GuestVisitor $guestVisitor, int $limit = 5): array
    {
        $targetRatings = $this->getTargetRatings($guestVisitor);
        $wisata = Wisata::with('kategoriWisata')
            ->withAvg('surveyPreferensi', 'rating_awal')
            ->where('status', 'aktif')
            ->whereNotIn('id', array_keys($targetRatings))
            ->orderByDesc('survey_preferensi_avg_rating_awal')
            ->latest('id')
            ->limit(max(1, $limit))
            ->get();

        $recommendations = $wisata->values()->map(function (Wisata $item, int $index) {
            $average = $item->survey_preferensi_avg_rating_awal;

            return [
                'wisata_id' => $item->id,
                'wisata' => $item,
                'nilai_prediksi' => round($average !== null ? (float) $average : 3.0, 4),
                'nilai_similarity' => 0.0,
                'ranking' => $index + 1,
                'metode' => 'Collaborative Filtering - Fallback',
            ];
        })->all();

        $this->saveRecommendations($guestVisitor, $recommendations);

        return $recommendations;
    }
}
