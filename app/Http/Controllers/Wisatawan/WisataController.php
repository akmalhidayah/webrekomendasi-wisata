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
        $userLocation = $this->validatedUserLocation($request);

        $wisata = Wisata::query()
            ->with('kategoriWisata')
            ->withMin([
                'hotels as harga_hotel_termurah' => fn ($query) => $query->where('status', 'aktif'),
            ], 'harga_min')
            ->withAvg(['ratingKunjungan as rating_aplikasi' => fn ($query) => $query->where('status', 'approved')], 'rating')
            ->withCount(['ratingKunjungan as jumlah_rating_aplikasi' => fn ($query) => $query->where('status', 'approved')])
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

        if ($userLocation !== null) {
            $wisata->getCollection()->each(function (Wisata $item) use ($userLocation) {
                $item->distance_km = $this->calculateDistanceKm(
                    $userLocation['lat'],
                    $userLocation['lng'],
                    $item->latitude !== null ? (float) $item->latitude : null,
                    $item->longitude !== null ? (float) $item->longitude : null,
                );
            });
        }

        $kategori = KategoriWisata::orderBy('nama_kategori')->get();
        $hasUserLocation = $userLocation !== null;

        return view('wisatawan.wisata.index', compact('wisata', 'kategori', 'hasUserLocation', 'userLocation'));
    }

    public function show(string $slug): View
    {
        $wisata = Wisata::with([
            'kategoriWisata',
            'fasilitasWisata',
            'fotoWisata',
            'hotels' => fn ($query) => $query->where('status', 'aktif'),
            'ratingKunjungan' => fn ($query) => $query
                ->where('status', 'approved')
                ->whereNotNull('ulasan')
                ->latest()
                ->limit(5),
        ])
            ->withAvg(['ratingKunjungan as rata_rata_rating' => fn ($query) => $query->where('status', 'approved')], 'rating')
            ->withCount(['ratingKunjungan as jumlah_rating' => fn ($query) => $query->where('status', 'approved')])
            ->withAvg(['ratingKunjungan as rating_aplikasi' => fn ($query) => $query->where('status', 'approved')], 'rating')
            ->withCount(['ratingKunjungan as jumlah_rating_aplikasi' => fn ($query) => $query->where('status', 'approved')])
            ->where('status', 'aktif')
            ->where('slug', $slug)
            ->firstOrFail();

        return view('wisatawan.wisata.show', compact('wisata'));
    }

    private function validatedUserLocation(Request $request): ?array
    {
        if (! $request->has(['lat', 'lng'])) {
            return null;
        }

        $lat = $request->query('lat');
        $lng = $request->query('lng');

        if (! is_numeric($lat) || ! is_numeric($lng)) {
            return null;
        }

        $lat = (float) $lat;
        $lng = (float) $lng;

        if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
            return null;
        }

        return compact('lat', 'lng');
    }

    private function calculateDistanceKm(float $userLat, float $userLng, ?float $destLat, ?float $destLng): ?float
    {
        if ($destLat === null || $destLng === null) {
            return null;
        }

        $earthRadius = 6371;
        $latDelta = deg2rad($destLat - $userLat);
        $lngDelta = deg2rad($destLng - $userLng);
        $a = sin($latDelta / 2) ** 2
            + cos(deg2rad($userLat)) * cos(deg2rad($destLat)) * sin($lngDelta / 2) ** 2;
        $a = max(0.0, min(1.0, $a));
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 1);
    }
}
