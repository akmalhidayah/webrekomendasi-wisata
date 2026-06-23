<?php

namespace App\Http\Controllers\Wisatawan;

use App\Http\Controllers\Controller;
use App\Models\KategoriWisata;
use App\Models\Wisata;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $wisata = Wisata::with('kategoriWisata')
            ->withAvg(['ratingKunjungan as rata_rata_rating' => fn ($query) => $query->where('status', 'disetujui')], 'rating')
            ->withCount(['ratingKunjungan as jumlah_rating' => fn ($query) => $query->where('status', 'disetujui')])
            ->where('status', 'aktif')
            ->orderByDesc('rata_rata_rating')
            ->orderBy('id')
            ->limit(6)
            ->get();
        $totalWisata = Wisata::where('status', 'aktif')->count();
        $totalKategori = KategoriWisata::count();
        $heroImages = Wisata::with('fotoWisata')
            ->where('status', 'aktif')
            ->orderBy('nama_wisata')
            ->get()
            ->flatMap(function (Wisata $item) {
                return collect([$item->foto_url])
                    ->merge($item->fotoWisata->map->foto_url);
            })
            ->filter()
            ->unique()
            ->take(8)
            ->values();

        return view('wisatawan.home', compact('wisata', 'totalWisata', 'totalKategori', 'heroImages'));
    }
}
