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
     * Menghasilkan rekomendasi hybrid:
     * - skor utama dari User-Based Collaborative Filtering dan rating destinasi,
     * - kecocokan kategori/jenis wisata dari survei target,
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

        $preferenceProfile = $this->buildPreferenceProfile($targetRatings);
        $candidates = Wisata::with('kategoriWisata')
            ->whereIn('id', $this->getCandidateWisataIds($targetRatings))
            ->get()
            ->keyBy('id');

        $predictions = [];
        foreach ($candidates as $wisataId => $wisata) {
            $prediction = $this->predictRatingForWisata($wisataId, $similarities, $matrix);
            $hasCollaborativeSignal = $prediction['nilai_prediksi'] > 0;
            $collaborativeScore = $hasCollaborativeSignal ? $prediction['nilai_prediksi'] : 3.0;
            $preferenceScore = $this->calculatePreferenceScore($wisata, $preferenceProfile);
            $qualityScore = $this->calculateQualityScore($wisata);

            $predictions[] = [
                'wisata_id' => $wisataId,
                'nilai_prediksi' => $this->calculateHybridScore(
                    $collaborativeScore,
                    $preferenceScore,
                    $qualityScore,
                    $hasCollaborativeSignal,
                ),
                'nilai_similarity' => $prediction['nilai_similarity'],
                'prediksi_cf' => round($collaborativeScore, 4),
                'skor_preferensi' => round($preferenceScore * 5, 4),
                'skor_rating_destinasi' => round($qualityScore * 5, 4),
                'has_collaborative_signal' => $hasCollaborativeSignal,
            ];
        }

        if ($predictions === []) {
            return $this->getFallbackRecommendations($guestVisitor, $limit);
        }

        usort($predictions, fn (array $first, array $second) => [
            $second['skor_rating_destinasi'],
            $second['nilai_prediksi'],
            $second['nilai_similarity'],
            $second['prediksi_cf'],
        ] <=> [
            $first['skor_rating_destinasi'],
            $first['nilai_prediksi'],
            $first['nilai_similarity'],
            $first['prediksi_cf'],
        ]);
        $recommendations = array_slice($predictions, 0, max(1, $limit));

        foreach ($recommendations as $index => &$recommendation) {
            $recommendation['ranking'] = $index + 1;
            $recommendation['metode'] = 'Hybrid Collaborative Filtering';
            $recommendation['wisata'] = $candidates->get($recommendation['wisata_id']);
        }
        unset($recommendation);

        $this->saveRecommendations($guestVisitor, $recommendations);

        return $recommendations;
    }

    /**
     * Profil preferensi dibuat dari destinasi yang diberi rating pada survei awal.
     * Nilai tinggi pada kategori/jenis tertentu akan memberi bonus ke kandidat serupa.
     *
     * @param  array<int, int>  $targetRatings
     * @return array{categories: array<int, float>, types: array<string, float>}
     */
    public function buildPreferenceProfile(array $targetRatings): array
    {
        $ratedWisata = Wisata::query()
            ->whereIn('id', array_keys($targetRatings))
            ->get(['id', 'kategori_wisata_id', 'jenis_wisata']);

        $categories = [];
        $types = [];

        foreach ($ratedWisata as $wisata) {
            $score = max(1, min(5, (int) ($targetRatings[$wisata->id] ?? 3))) / 5;

            $categories[$wisata->kategori_wisata_id][] = $score;

            $typeKey = $this->normalizeType($wisata->jenis_wisata);
            if ($typeKey !== '') {
                $types[$typeKey][] = $score;
            }
        }

        return [
            'categories' => collect($categories)
                ->map(fn (array $scores) => array_sum($scores) / count($scores))
                ->all(),
            'types' => collect($types)
                ->map(fn (array $scores) => array_sum($scores) / count($scores))
                ->all(),
        ];
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

    /**
     * Skor 0..1 berdasarkan kesamaan kategori dan jenis wisata dengan survei pengguna.
     *
     * @param  array{categories: array<int, float>, types: array<string, float>}  $preferenceProfile
     */
    public function calculatePreferenceScore(Wisata $wisata, array $preferenceProfile): float
    {
        $categoryScore = $preferenceProfile['categories'][$wisata->kategori_wisata_id] ?? 0.6;
        $typeScore = $preferenceProfile['types'][$this->normalizeType($wisata->jenis_wisata)] ?? $categoryScore;

        return round(($categoryScore * 0.7) + ($typeScore * 0.3), 4);
    }

    /**
     * Skor 0..1 dari rating destinasi. Jika belum ada rating, dipakai nilai netral.
     */
    public function calculateQualityScore(Wisata $wisata): float
    {
        $rating = $wisata->rating_tampil;

        if ($rating === null) {
            return 0.6;
        }

        return round(max(0, min(5, (float) $rating)) / 5, 4);
    }

    public function calculateHybridScore(
        float $collaborativeScore,
        float $preferenceScore,
        float $qualityScore,
        bool $hasCollaborativeSignal = true,
    ): float {
        $mainScore = (($collaborativeScore / 5) * 0.8) + ($qualityScore * 0.2);
        $mainWeight = $hasCollaborativeSignal ? 0.75 : 0.55;
        $preferenceWeight = $hasCollaborativeSignal ? 0.25 : 0.45;

        $score = ($mainScore * $mainWeight) + ($preferenceScore * $preferenceWeight);

        return round(max(0, min(1, $score)) * 5, 4);
    }

    private function normalizeType(?string $type): string
    {
        return str($type ?? '')->lower()->squish()->toString();
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
        $preferenceProfile = $this->buildPreferenceProfile($targetRatings);
        $wisata = Wisata::with('kategoriWisata')
            ->withAvg('surveyPreferensi', 'rating_awal')
            ->where('status', 'aktif')
            ->whereNotIn('id', array_keys($targetRatings))
            ->get();

        $recommendations = $wisata->values()->map(function (Wisata $item) use ($preferenceProfile) {
            $average = $item->survey_preferensi_avg_rating_awal;
            $collaborativeScore = $average !== null ? (float) $average : 3.0;
            $preferenceScore = $this->calculatePreferenceScore($item, $preferenceProfile);
            $qualityScore = $this->calculateQualityScore($item);

            return [
                'wisata_id' => $item->id,
                'wisata' => $item,
                'nilai_prediksi' => $this->calculateHybridScore($collaborativeScore, $preferenceScore, $qualityScore, false),
                'nilai_similarity' => 0.0,
                'prediksi_cf' => round($collaborativeScore, 4),
                'skor_preferensi' => round($preferenceScore * 5, 4),
                'skor_rating_destinasi' => round($qualityScore * 5, 4),
                'metode' => 'Hybrid Collaborative Filtering - Fallback',
            ];
        })
            ->sort(fn (array $first, array $second) => [
                $second['skor_rating_destinasi'],
                $second['nilai_prediksi'],
                $second['prediksi_cf'],
            ] <=> [
                $first['skor_rating_destinasi'],
                $first['nilai_prediksi'],
                $first['prediksi_cf'],
            ])
            ->take(max(1, $limit))
            ->values()
            ->map(function (array $item, int $index) {
                $item['ranking'] = $index + 1;

                return $item;
            })
            ->all();

        $this->saveRecommendations($guestVisitor, $recommendations);

        return $recommendations;
    }
}
