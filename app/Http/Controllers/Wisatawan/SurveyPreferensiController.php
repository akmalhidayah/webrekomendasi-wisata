<?php

namespace App\Http\Controllers\Wisatawan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Wisatawan\StoreSurveyPreferensiRequest;
use App\Models\GuestVisitor;
use App\Models\LogAktivitas;
use App\Models\SurveyPreferensi;
use App\Models\Wisata;
use App\Services\CollaborativeFilteringService;
use App\Services\GuestVisitorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SurveyPreferensiController extends Controller
{
    public function index(Request $request, GuestVisitorService $service): View
    {
        $guest = $service->getOrCreateGuestVisitor($request);
        $storedIds = collect($request->session()->get('survey_wisata_ids', []));
        $stored = Wisata::with('kategoriWisata')->where('status', 'aktif')->whereIn('id', $storedIds)->get()->keyBy('id');
        $savedSurvey = $guest->surveyPreferensi()
            ->whereHas('wisata', fn ($query) => $query->where('status', 'aktif'))
            ->orderBy('id')
            ->get();

        if ($storedIds->count() === 10 && $stored->count() === 10) {
            $wisata = $storedIds->map(fn ($id) => $stored->get($id))->values();
        } elseif ($savedSurvey->count() === 10) {
            $savedIds = $savedSurvey->pluck('wisata_id');
            $savedWisata = Wisata::with('kategoriWisata')
                ->where('status', 'aktif')
                ->whereIn('id', $savedIds)
                ->get()
                ->keyBy('id');
            $wisata = $savedIds->map(fn ($id) => $savedWisata->get($id))->filter()->values();
        } else {
            $all = Wisata::with('kategoriWisata')->where('status', 'aktif')->inRandomOrder()->get();
            $selected = $all->groupBy('kategori_wisata_id')->map->first()->values();

            if ($selected->count() < 10) {
                $selected = $selected->concat(
                    $all->whereNotIn('id', $selected->pluck('id'))->take(10 - $selected->count())
                );
            }

            $wisata = $selected->take(10)->shuffle()->values();
        }

        abort_if($wisata->count() < 10, 503, 'Survei membutuhkan minimal 10 destinasi wisata aktif.');
        $request->session()->put('survey_wisata_ids', $wisata->pluck('id')->all());

        $savedRatings = $savedSurvey->pluck('rating_awal', 'wisata_id');
        $initialStep = max(1, min(3, $request->integer('step', 1)));
        $formValues = [
            'budget_min' => old('budget_min', $guest->budget_min ?? ''),
            'budget_max' => old('budget_max', $guest->budget_max ?? ''),
            'butuh_hotel' => (string) old('butuh_hotel', $guest->butuh_hotel ? '1' : '0'),
            'jumlah_malam' => old('jumlah_malam', $guest->jumlah_malam ?? 1),
            'user_latitude' => old('user_latitude', $guest->hasLocation() ? $guest->user_latitude : ''),
            'user_longitude' => old('user_longitude', $guest->hasLocation() ? $guest->user_longitude : ''),
            'is_location_allowed' => (string) old('is_location_allowed', $guest->hasLocation() ? '1' : '0'),
        ];

        return view('wisatawan.survey.index', compact('wisata', 'savedRatings', 'initialStep', 'formValues'));
    }

    public function store(
        StoreSurveyPreferensiRequest $request,
        GuestVisitorService $service,
        CollaborativeFilteringService $recommendationService,
    ): RedirectResponse {
        $guest = $service->getOrCreateGuestVisitor($request);
        $validated = $request->validated();

        DB::transaction(function () use ($request, $guest, $validated) {
            $guest->surveyPreferensi()->delete();
            $timestamp = now();
            SurveyPreferensi::insert(collect($validated['ratings'])->map(fn ($rating) => [
                'guest_visitor_id' => $guest->id,
                'wisata_id' => $rating['wisata_id'],
                'rating_awal' => $rating['rating_awal'],
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ])->all());

            $locationAllowed = (bool) ($validated['is_location_allowed'] ?? false);
            $hasLocation = $locationAllowed
                && array_key_exists('user_latitude', $validated)
                && array_key_exists('user_longitude', $validated)
                && $validated['user_latitude'] !== null
                && $validated['user_longitude'] !== null;

            $guest->update([
                'budget_min' => $validated['budget_min'] ?? null,
                'budget_max' => $validated['budget_max'] ?? null,
                'butuh_hotel' => (bool) $validated['butuh_hotel'],
                'jumlah_malam' => (bool) $validated['butuh_hotel'] ? (int) ($validated['jumlah_malam'] ?? 1) : 1,
                'user_latitude' => $hasLocation ? $validated['user_latitude'] : null,
                'user_longitude' => $hasLocation ? $validated['user_longitude'] : null,
                'is_location_allowed' => $hasLocation,
                'location_captured_at' => $hasLocation ? now() : null,
            ]);

            LogAktivitas::create([
                'guest_visitor_id' => $guest->id,
                'aktivitas' => 'Survey Preferensi Disimpan',
                'deskripsi' => 'Pengunjung menyimpan 10 rating awal destinasi wisata.',
                'ip_address' => $request->ip(),
            ]);
        });

        $request->session()->forget('survey_wisata_ids');
        $guest->refresh();
        $outcome = $recommendationService->generateRecommendationOutcome($guest, 5);
        $this->storeRecommendationIssue($request, $guest, $outcome);

        LogAktivitas::create([
            'guest_visitor_id' => $guest->id,
            'aktivitas' => 'Rekomendasi Diproses',
            'deskripsi' => 'Sistem menghasilkan rekomendasi wisata untuk pengunjung setelah survei.',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('wisatawan.rekomendasi.hasil')->with('recommendation_generated', true);
    }

    public function success(): View
    {
        return view('wisatawan.survey.success');
    }

    private function storeRecommendationIssue(Request $request, GuestVisitor $guest, array $outcome): void
    {
        if ($outcome['status'] === 'success') {
            $request->session()->forget('recommendation_issue');

            return;
        }

        $request->session()->put('recommendation_issue', [
            'type' => $outcome['status'],
            'budget_max' => $guest->budget_max !== null ? (float) $guest->budget_max : null,
            'minimum_required_budget' => $outcome['minimum_required_budget'],
            'hotel_required' => (bool) $guest->butuh_hotel,
        ]);
    }
}
