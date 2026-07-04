<?php

namespace App\Http\Controllers\Wisatawan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Wisatawan\StoreSurveyPreferensiRequest;
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
        $service->getOrCreateGuestVisitor($request);
        $storedIds = collect($request->session()->get('survey_wisata_ids', []));
        $stored = Wisata::with('kategoriWisata')->where('status', 'aktif')->whereIn('id', $storedIds)->get()->keyBy('id');

        if ($storedIds->count() === 10 && $stored->count() === 10) {
            $wisata = $storedIds->map(fn ($id) => $stored->get($id))->values();
        } else {
            $all = Wisata::with('kategoriWisata')->where('status', 'aktif')->inRandomOrder()->get();
            $selected = $all->groupBy('kategori_wisata_id')->map->first()->values();

            if ($selected->count() < 10) {
                $selected = $selected->concat(
                    $all->whereNotIn('id', $selected->pluck('id'))->take(10 - $selected->count())
                );
            }

            $wisata = $selected->take(10)->shuffle()->values();
            $request->session()->put('survey_wisata_ids', $wisata->pluck('id')->all());
        }

        abort_if($wisata->count() < 10, 503, 'Survei membutuhkan minimal 10 destinasi wisata aktif.');

        return view('wisatawan.survey.index', compact('wisata'));
    }

    public function store(
        StoreSurveyPreferensiRequest $request,
        GuestVisitorService $service,
        CollaborativeFilteringService $recommendationService,
    ): RedirectResponse
    {
        $guest = $service->getOrCreateGuestVisitor($request);
        $validated = $request->validated();

        DB::transaction(function () use ($request, $guest, $validated) {
            foreach ($validated['ratings'] as $rating) {
                SurveyPreferensi::updateOrCreate(
                    ['guest_visitor_id' => $guest->id, 'wisata_id' => $rating['wisata_id']],
                    ['rating_awal' => $rating['rating_awal']],
                );
            }

            $locationAllowed = (bool) ($validated['is_location_allowed'] ?? false);
            $hasLocation = $locationAllowed
                && ! empty($validated['user_latitude'])
                && ! empty($validated['user_longitude']);

            $guest->update([
                'budget_min' => $validated['budget_min'],
                'budget_max' => $validated['budget_max'],
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
        $recommendationService->generateRecommendations($guest, 5);

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
}
