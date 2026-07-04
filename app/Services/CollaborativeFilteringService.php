<?php

namespace App\Services;

use App\Models\GuestVisitor;
use App\Models\HasilRekomendasi;
use App\Models\SurveyPreferensi;
use App\Models\Wisata;
use Illuminate\Support\Facades\DB;

class CollaborativeFilteringService
{
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
        $candidates = Wisata::with([
            'kategoriWisata',
            'hotels' => fn ($query) => $query->where('status', 'aktif'),
        ])
            ->whereIn('id', $this->getCandidateWisataIds($targetRatings))
            ->get()
            ->keyBy('id');

        $predictions = [];
        foreach ($candidates as $wisataId => $wisata) {
            $prediction = $this->predictRatingForWisata($wisataId, $similarities, $matrix);
            $hasCollaborativeSignal = $prediction['nilai_prediksi'] > 0;
            $qualityScore = $this->calculateQualityScore($wisata);
            $collaborativeScore = $hasCollaborativeSignal ? $prediction['nilai_prediksi'] : max(3.0, $qualityScore * 5);
            $collaborativeNormalized = $this->clamp($collaborativeScore / 5);
            $preferenceScore = $this->calculatePreferenceScore($wisata, $preferenceProfile);
            $estimation = $this->calculateBudgetEstimation($guestVisitor, $wisata);
            $distance = $this->calculateCandidateDistance($guestVisitor, $wisata);

            $predictions[] = [
                'wisata_id' => $wisataId,
                'wisata' => $wisata,
                'nilai_similarity' => $prediction['nilai_similarity'],
                'prediksi_cf' => round($collaborativeScore, 4),
                'skor_cf' => round($collaborativeNormalized, 4),
                'skor_budget' => round($estimation['skor_budget'], 4),
                'skor_jarak' => null,
                'skor_preferensi' => round($preferenceScore, 4),
                'skor_rating_destinasi' => round($qualityScore, 4),
                'has_collaborative_signal' => $hasCollaborativeSignal,
                'hotel_id' => $estimation['hotel_id'],
                'hotel' => $estimation['hotel'],
                'hotel_requested' => (bool) $guestVisitor->butuh_hotel,
                'estimasi_biaya_wisata' => $estimation['estimasi_biaya_wisata'],
                'estimasi_biaya_hotel' => $estimation['estimasi_biaya_hotel'],
                'total_estimasi_budget' => $estimation['total_estimasi_budget'],
                'jarak_km' => $distance,
                'alasan_rekomendasi' => $this->buildRecommendationReasons(
                    $collaborativeNormalized,
                    $estimation['skor_budget'],
                    null,
                    $preferenceScore,
                    $qualityScore,
                    $estimation['hotel_id'] !== null,
                    (bool) $guestVisitor->butuh_hotel,
                    $distance,
                ),
            ];
        }

        if ($predictions === []) {
            return $this->getFallbackRecommendations($guestVisitor, $limit);
        }

        $this->applyDistanceScores($predictions);
        $this->applyFinalScores($predictions, $guestVisitor->hasLocation());
        $this->sortRecommendations($predictions);

        $recommendations = array_slice($predictions, 0, max(1, $limit));

        foreach ($recommendations as $index => &$recommendation) {
            $recommendation['ranking'] = $index + 1;
            $recommendation['metode'] = 'Hybrid Collaborative Filtering';
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

    /**
     * @return array{hotel_id: ?int, hotel: mixed, estimasi_biaya_wisata: float, estimasi_biaya_hotel: float, total_estimasi_budget: float, skor_budget: float}
     */
    private function calculateBudgetEstimation(GuestVisitor $guestVisitor, Wisata $wisata): array
    {
        $tourCost = $this->calculateTourCost($wisata);
        $hotel = null;
        $hotelCost = 0.0;

        if ($guestVisitor->butuh_hotel) {
            $hotel = $this->selectBestHotelForBudget($guestVisitor, $wisata, $tourCost);
            $hotelCost = $hotel ? ((float) $hotel->harga_min * max(1, (int) $guestVisitor->jumlah_malam)) : 0.0;
        }

        $total = $tourCost + $hotelCost;

        return [
            'hotel_id' => $hotel?->id,
            'hotel' => $hotel,
            'estimasi_biaya_wisata' => $tourCost,
            'estimasi_biaya_hotel' => $hotelCost,
            'total_estimasi_budget' => $total,
            'skor_budget' => $this->calculateBudgetScore($guestVisitor, $total),
        ];
    }

    private function calculateTourCost(Wisata $wisata): float
    {
        $total = (float) ($wisata->total_estimasi_biaya ?? 0);

        if ($total > 0) {
            return $total;
        }

        return (float) ($wisata->harga_tiket ?? 0)
            + (float) ($wisata->estimasi_transportasi ?? 0)
            + (float) ($wisata->estimasi_biaya_lainnya ?? 0);
    }

    private function selectBestHotelForBudget(GuestVisitor $guestVisitor, Wisata $wisata, float $tourCost): mixed
    {
        return $wisata->hotels
            ->filter(fn ($hotel) => $hotel->status === 'aktif')
            ->sortByDesc(function ($hotel) use ($guestVisitor, $tourCost) {
                $hotelCost = (float) $hotel->harga_min * max(1, (int) $guestVisitor->jumlah_malam);

                return $this->calculateBudgetScore($guestVisitor, $tourCost + $hotelCost);
            })
            ->first();
    }

    private function calculateBudgetScore(GuestVisitor $guestVisitor, float $totalBudget): float
    {
        $budgetMin = (float) ($guestVisitor->budget_min ?? 0);
        $budgetMax = (float) ($guestVisitor->budget_max ?? 0);

        if ($budgetMin <= 0 && $budgetMax <= 0) {
            return 0.6;
        }

        if ($totalBudget >= $budgetMin && $totalBudget <= $budgetMax) {
            return 1.0;
        }

        if ($totalBudget < $budgetMin) {
            return 0.85;
        }

        if ($budgetMax <= 0 || $totalBudget <= 0) {
            return 0.6;
        }

        return $this->clamp(max(0.2, $budgetMax / $totalBudget));
    }

    private function calculateCandidateDistance(GuestVisitor $guestVisitor, Wisata $wisata): ?float
    {
        if (! $guestVisitor->hasLocation() || ! $wisata->latitude || ! $wisata->longitude) {
            return null;
        }

        return $this->calculateDistanceKm(
            (float) $guestVisitor->user_latitude,
            (float) $guestVisitor->user_longitude,
            (float) $wisata->latitude,
            (float) $wisata->longitude,
        );
    }

    private function calculateDistanceKm(float $lat1, float $lon1, float $lat2, float $lon2): ?float
    {
        $earthRadius = 6371;
        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);
        $a = sin($latDelta / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($lonDelta / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }

    private function applyDistanceScores(array &$predictions): void
    {
        $distances = collect($predictions)
            ->pluck('jarak_km')
            ->filter(fn ($distance) => $distance !== null)
            ->map(fn ($distance) => (float) $distance)
            ->values();

        if ($distances->isEmpty()) {
            return;
        }

        $nearest = $distances->min();
        $farthest = $distances->max();

        foreach ($predictions as &$prediction) {
            if ($prediction['jarak_km'] === null) {
                $prediction['skor_jarak'] = null;
                continue;
            }

            $prediction['skor_jarak'] = $nearest === $farthest
                ? 1.0
                : round($this->clamp(1 - (((float) $prediction['jarak_km'] - $nearest) / ($farthest - $nearest))), 4);
        }
        unset($prediction);
    }

    private function applyFinalScores(array &$predictions, bool $hasLocation): void
    {
        foreach ($predictions as &$prediction) {
            $distanceScore = $prediction['skor_jarak'];

            if ($hasLocation && $distanceScore !== null) {
                $final = (0.40 * $prediction['skor_cf'])
                    + (0.25 * $prediction['skor_budget'])
                    + (0.20 * $distanceScore)
                    + (0.10 * $prediction['skor_preferensi'])
                    + (0.05 * $prediction['skor_rating_destinasi']);
            } else {
                $final = (0.50 * $prediction['skor_cf'])
                    + (0.25 * $prediction['skor_budget'])
                    + (0.15 * $prediction['skor_preferensi'])
                    + (0.10 * $prediction['skor_rating_destinasi']);
            }

            $prediction['skor_akhir'] = round($this->clamp($final), 4);
            $prediction['nilai_prediksi'] = round($prediction['skor_akhir'] * 5, 4);
            $prediction['alasan_rekomendasi'] = $this->buildRecommendationReasons(
                $prediction['skor_cf'],
                $prediction['skor_budget'],
                $prediction['skor_jarak'],
                $prediction['skor_preferensi'],
                $prediction['skor_rating_destinasi'],
                $prediction['hotel_id'] !== null,
                (bool) ($prediction['hotel_requested'] ?? false),
                $prediction['jarak_km'],
            );
        }
        unset($prediction);
    }

    private function sortRecommendations(array &$predictions): void
    {
        usort($predictions, fn (array $first, array $second) => [
            $second['skor_akhir'],
            $second['skor_budget'],
            $second['skor_cf'],
            $second['skor_rating_destinasi'],
        ] <=> [
            $first['skor_akhir'],
            $first['skor_budget'],
            $first['skor_cf'],
            $first['skor_rating_destinasi'],
        ]);
    }

    private function buildRecommendationReasons(
        float $scoreCf,
        float $scoreBudget,
        ?float $scoreDistance,
        float $scorePreference,
        float $scoreQuality,
        bool $hasHotel,
        bool $hotelRequested,
        ?float $distance,
    ): array {
        $reasons = [];

        if ($scoreCf >= 0.6 || $scorePreference >= 0.6) {
            $reasons[] = 'Cocok dengan pola rating dan preferensi wisata Anda.';
        }

        if ($scoreBudget >= 0.85) {
            $reasons[] = 'Estimasi budget masih sesuai dengan rentang yang Anda masukkan.';
        }

        if ($scoreDistance !== null && $scoreDistance >= 0.65 && $distance !== null) {
            $reasons[] = 'Destinasi ini termasuk yang relatif dekat dari lokasi Anda.';
        }

        if ($hotelRequested && $hasHotel) {
            $reasons[] = 'Tersedia hotel terkait untuk kebutuhan menginap.';
        }

        if ($hotelRequested && ! $hasHotel) {
            $reasons[] = 'Hotel terkait belum tersedia, sehingga budget dihitung dari biaya wisata saja.';
        }

        if ($scoreQuality >= 0.75) {
            $reasons[] = 'Rating destinasi cukup baik dari data yang tersedia.';
        }

        return $reasons ?: ['Rekomendasi ini dipilih dari kombinasi rating, budget, dan preferensi Anda.'];
    }

    private function clamp(float $value, float $min = 0.0, float $max = 1.0): float
    {
        return max($min, min($max, $value));
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
                    'hotel_id' => $recommendation['hotel_id'] ?? null,
                    'estimasi_biaya_wisata' => round($recommendation['estimasi_biaya_wisata'] ?? 0, 2),
                    'estimasi_biaya_hotel' => round($recommendation['estimasi_biaya_hotel'] ?? 0, 2),
                    'total_estimasi_budget' => round($recommendation['total_estimasi_budget'] ?? 0, 2),
                    'jarak_km' => isset($recommendation['jarak_km']) ? round($recommendation['jarak_km'], 2) : null,
                    'skor_cf' => round($recommendation['skor_cf'] ?? 0, 4),
                    'skor_budget' => round($recommendation['skor_budget'] ?? 0, 4),
                    'skor_jarak' => isset($recommendation['skor_jarak']) ? round($recommendation['skor_jarak'], 4) : null,
                    'skor_preferensi' => round($recommendation['skor_preferensi'] ?? 0, 4),
                    'skor_rating_destinasi' => round($recommendation['skor_rating_destinasi'] ?? 0, 4),
                    'skor_akhir' => round($recommendation['skor_akhir'] ?? (($recommendation['nilai_prediksi'] ?? 0) / 5), 4),
                    'alasan_rekomendasi' => $recommendation['alasan_rekomendasi'] ?? [],
                ]);
            }
        });
    }

    public function getFallbackRecommendations(GuestVisitor $guestVisitor, int $limit = 5): array
    {
        $targetRatings = $this->getTargetRatings($guestVisitor);
        $preferenceProfile = $this->buildPreferenceProfile($targetRatings);
        $wisata = Wisata::with([
            'kategoriWisata',
            'hotels' => fn ($query) => $query->where('status', 'aktif'),
        ])
            ->withAvg('surveyPreferensi', 'rating_awal')
            ->where('status', 'aktif')
            ->whereNotIn('id', array_keys($targetRatings))
            ->get();

        $recommendations = $wisata->values()->map(function (Wisata $item) use ($preferenceProfile, $guestVisitor) {
            $average = $item->survey_preferensi_avg_rating_awal;
            $qualityScore = $this->calculateQualityScore($item);
            $collaborativeScore = $average !== null ? (float) $average : max(3.0, $qualityScore * 5);
            $collaborativeNormalized = $this->clamp($collaborativeScore / 5);
            $preferenceScore = $this->calculatePreferenceScore($item, $preferenceProfile);
            $estimation = $this->calculateBudgetEstimation($guestVisitor, $item);
            $distance = $this->calculateCandidateDistance($guestVisitor, $item);

            return [
                'wisata_id' => $item->id,
                'wisata' => $item,
                'nilai_similarity' => 0.0,
                'prediksi_cf' => round($collaborativeScore, 4),
                'skor_cf' => round($collaborativeNormalized, 4),
                'skor_budget' => round($estimation['skor_budget'], 4),
                'skor_jarak' => null,
                'skor_preferensi' => round($preferenceScore, 4),
                'skor_rating_destinasi' => round($qualityScore, 4),
                'hotel_id' => $estimation['hotel_id'],
                'hotel' => $estimation['hotel'],
                'hotel_requested' => (bool) $guestVisitor->butuh_hotel,
                'estimasi_biaya_wisata' => $estimation['estimasi_biaya_wisata'],
                'estimasi_biaya_hotel' => $estimation['estimasi_biaya_hotel'],
                'total_estimasi_budget' => $estimation['total_estimasi_budget'],
                'jarak_km' => $distance,
                'alasan_rekomendasi' => [],
                'metode' => 'Hybrid Collaborative Filtering - Fallback',
            ];
        })->all();

        $this->applyDistanceScores($recommendations);
        $this->applyFinalScores($recommendations, $guestVisitor->hasLocation());
        $this->sortRecommendations($recommendations);

        $recommendations = collect($recommendations)
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
