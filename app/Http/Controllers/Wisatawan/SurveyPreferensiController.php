<?php

namespace App\Http\Controllers\Wisatawan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Wisatawan\StoreSurveyPreferensiRequest;
use App\Models\LogAktivitas;
use App\Models\SurveyPreferensi;
use App\Models\Wisata;
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

    public function store(StoreSurveyPreferensiRequest $request, GuestVisitorService $service): RedirectResponse
    {
        $guest = $service->getOrCreateGuestVisitor($request);

        DB::transaction(function () use ($request, $guest) {
            foreach ($request->validated('ratings') as $rating) {
                SurveyPreferensi::updateOrCreate(
                    ['guest_visitor_id' => $guest->id, 'wisata_id' => $rating['wisata_id']],
                    ['rating_awal' => $rating['rating_awal']],
                );
            }

            LogAktivitas::create([
                'guest_visitor_id' => $guest->id,
                'aktivitas' => 'Survey Preferensi Disimpan',
                'deskripsi' => 'Pengunjung menyimpan 10 rating awal destinasi wisata.',
                'ip_address' => $request->ip(),
            ]);
        });

        $request->session()->forget('survey_wisata_ids');

        return redirect()->route('wisatawan.survey.success');
    }

    public function success(): View
    {
        return view('wisatawan.survey.success');
    }
}
