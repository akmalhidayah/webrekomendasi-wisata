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
    public function index(Request $request, CollaborativeFilteringService $recommendationService): View|RedirectResponse
    {
        $guest = $this->guestFromSession($request);

        if (! $guest || ! $guest->surveyPreferensi()->exists()) {
            return redirect()->route('wisatawan.survey.index')->with('error', 'Silakan isi survey preferensi terlebih dahulu.');
        }

        $recommendationService->generateRecommendations($guest, 5);
        LogAktivitas::create([
            'guest_visitor_id' => $guest->id,
            'aktivitas' => 'Rekomendasi Diproses',
            'deskripsi' => 'Sistem menghasilkan rekomendasi wisata untuk pengunjung.',
            'ip_address' => $request->ip(),
        ]);

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

        $recommendationService->generateRecommendations($guest, 5);
        LogAktivitas::create([
            'guest_visitor_id' => $guest->id,
            'aktivitas' => 'Rekomendasi Diproses',
            'deskripsi' => 'Sistem menghasilkan rekomendasi wisata untuk pengunjung.',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('wisatawan.rekomendasi.hasil');
    }

    public function hasil(Request $request): View|RedirectResponse
    {
        $guest = $this->guestFromSession($request);

        if (! $guest) {
            return redirect()->route('wisatawan.survey.index');
        }

        $hasil = $guest->hasilRekomendasi()
            ->with('wisata.kategoriWisata')
            ->orderBy('ranking')
            ->get();
        $isFallback = $hasil->contains(fn ($item) => str_contains($item->metode, 'Fallback'));

        return view('wisatawan.rekomendasi.hasil', compact('guest', 'hasil', 'isFallback'));
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

        $request->session()->forget('survey_wisata_ids');

        return redirect()->route('wisatawan.survey.index')->with('success', 'Silakan isi ulang survey preferensi.');
    }

    private function guestFromSession(Request $request): ?GuestVisitor
    {
        $code = $request->session()->get('kode_guest');

        return $code ? GuestVisitor::where('kode_guest', $code)->first() : null;
    }
}
