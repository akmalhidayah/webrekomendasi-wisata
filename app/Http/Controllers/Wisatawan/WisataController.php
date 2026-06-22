<?php

namespace App\Http\Controllers\Wisatawan;

use App\Http\Controllers\Controller;
use App\Models\KategoriWisata;
use App\Models\Wisata;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WisataController extends Controller
{
    public function index(Request $request): View
    {
        $wisata = Wisata::query()
            ->with('kategoriWisata')
            ->where('status', 'aktif')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%'.$request->string('search').'%';
                $query->where(fn ($subquery) => $subquery
                    ->where('nama_wisata', 'like', $search)
                    ->orWhere('jenis_wisata', 'like', $search)
                    ->orWhere('alamat', 'like', $search));
            })
            ->when($request->filled('kategori'), fn ($query) => $query->where('kategori_wisata_id', $request->integer('kategori')))
            ->orderBy('nama_wisata')
            ->paginate(12)
            ->withQueryString();
        $kategori = KategoriWisata::orderBy('nama_kategori')->get();

        return view('wisatawan.wisata.index', compact('wisata', 'kategori'));
    }

    public function show(string $slug): View
    {
        $wisata = Wisata::with([
            'kategoriWisata',
            'fasilitasWisata',
            'fotoWisata',
            'ratingKunjungan' => fn ($query) => $query
                ->where('status', 'disetujui')
                ->whereNotNull('ulasan')
                ->latest()
                ->limit(5),
        ])
            ->withAvg(['ratingKunjungan as rata_rata_rating' => fn ($query) => $query->where('status', 'disetujui')], 'rating')
            ->withCount(['ratingKunjungan as jumlah_rating' => fn ($query) => $query->where('status', 'disetujui')])
            ->where('status', 'aktif')
            ->where('slug', $slug)
            ->firstOrFail();

        return view('wisatawan.wisata.show', compact('wisata'));
    }
}
