<?php

namespace App\Http\Controllers\Wisatawan;

use App\Http\Controllers\Controller;
use App\Models\GuestVisitor;
use App\Models\LogAktivitas;
use App\Services\CollaborativeFilteringService;
use App\Services\GuestVisitorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RekomendasiController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $guest = $this->guestFromSession($request);

        if (! $guest || ! $guest->surveyPreferensi()->exists()) {
            return redirect()->route('wisatawan.survey.index')->with('error', 'Silakan isi survey preferensi terlebih dahulu.');
        }

        return redirect()->route('wisatawan.rekomendasi.hasil');
    }

    public function proses(
        Request $request,
        GuestVisitorService $guestService,
        CollaborativeFilteringService $recommendationService,
    ): RedirectResponse {
        $guest = $guestService->getOrCreateGuestVisitor($request);

        if (! $guest->surveyPreferensi()->exists()) {
            return redirect()->route('wisatawan.survey.index')->with('error', 'Silakan isi survey preferensi terlebih dahulu.');
        }

        $outcome = $recommendationService->generateRecommendationOutcome($guest, 5);
        $this->storeRecommendationIssue($request, $guest, $outcome);
        LogAktivitas::create([
            'guest_visitor_id' => $guest->id,
            'aktivitas' => 'Rekomendasi Diproses',
            'deskripsi' => 'Sistem menghasilkan rekomendasi wisata untuk pengunjung.',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('wisatawan.rekomendasi.hasil')->with('recommendation_generated', true);
    }

    public function hasil(Request $request): View|RedirectResponse
    {
        $guest = $this->guestFromSession($request);

        if (! $guest) {
            return redirect()->route('wisatawan.survey.index');
        }

        $hasil = $guest->hasilRekomendasi()
            ->with(['wisata.kategoriWisata', 'hotel'])
            ->orderBy('ranking')
            ->get()
            ->values();
        $isFallback = $hasil->contains(fn ($item) => str_contains($item->metode, 'Fallback'));
        $isBroadInterest = $hasil->contains(fn ($item) => $item->metode === 'Broad Interest');
        $isQualityBudget = $hasil->contains(fn ($item) => $item->metode === 'Quality Budget & Popularity');
        $topRecommendation = $hasil->first();
        $showResultPopup = (bool) $request->session()->pull('recommendation_generated', false);
        $recommendationIssue = $request->session()->get('recommendation_issue');

        return view('wisatawan.rekomendasi.hasil', compact(
            'guest',
            'hasil',
            'isFallback',
            'isBroadInterest',
            'isQualityBudget',
            'topRecommendation',
            'showResultPopup',
            'recommendationIssue',
        ));
    }

    public function tanpaHotel(
        Request $request,
        CollaborativeFilteringService $recommendationService,
    ): RedirectResponse {
        $guest = $this->guestFromSession($request);

        if (! $guest || ! $guest->surveyPreferensi()->exists()) {
            return redirect()->route('wisatawan.survey.index')->with('error', 'Silakan isi survey preferensi terlebih dahulu.');
        }

        $guest->update([
            'butuh_hotel' => false,
            'jumlah_malam' => 1,
        ]);
        $guest->refresh();
        $outcome = $recommendationService->generateRecommendationOutcome($guest, 5);
        $this->storeRecommendationIssue($request, $guest, $outcome);

        LogAktivitas::create([
            'guest_visitor_id' => $guest->id,
            'aktivitas' => 'Rekomendasi Diproses Tanpa Hotel',
            'deskripsi' => 'Pengunjung menonaktifkan kebutuhan hotel dan menghitung ulang rekomendasi.',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('wisatawan.rekomendasi.hasil')->with('recommendation_generated', true);
    }

    public function reset(Request $request): RedirectResponse
    {
        $guest = $this->guestFromSession($request);

        if ($guest) {
            DB::transaction(function () use ($guest, $request) {
                $guest->surveyPreferensi()->delete();
                $guest->hasilRekomendasi()->delete();
                LogAktivitas::create([
                    'guest_visitor_id' => $guest->id,
                    'aktivitas' => 'Survey dan Rekomendasi Direset',
                    'deskripsi' => 'Pengunjung menghapus preferensi dan hasil rekomendasinya.',
                    'ip_address' => $request->ip(),
                ]);
            });
        }

        $request->session()->forget(['survey_wisata_ids', 'recommendation_issue']);

        return redirect()->route('wisatawan.survey.index')->with('success', 'Silakan isi ulang survey preferensi.');
    }

    private function guestFromSession(Request $request): ?GuestVisitor
    {
        $code = $request->session()->get('kode_guest');

        return $code ? GuestVisitor::where('kode_guest', $code)->first() : null;
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
