<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuestVisitor;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HasilRekomendasiController extends Controller
{
    public function index(Request $request): View
    {
        $guests = GuestVisitor::query()
            ->whereHas('hasilRekomendasi')
            ->withCount('hasilRekomendasi')
            ->withMax('hasilRekomendasi', 'created_at')
            ->when($request->filled('search'), fn ($query) => $query
                ->where('kode_guest', 'like', '%'.$request->string('search').'%'))
            ->orderByDesc('hasil_rekomendasi_max_created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.hasil-rekomendasi.index', compact('guests'));
    }

    public function show(GuestVisitor $guestVisitor): View
    {
        $guestVisitor->load([
            'surveyPreferensi' => fn ($query) => $query->with('wisata')->latest(),
            'hasilRekomendasi' => fn ($query) => $query->with('wisata.kategoriWisata')->orderBy('ranking'),
        ]);

        return view('admin.hasil-rekomendasi.show', compact('guestVisitor'));
    }
}
