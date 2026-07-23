<?php

namespace App\Http\Controllers\Wisatawan;

use App\Http\Controllers\Controller;
use App\Models\GuestVisitor;
use App\Models\KategoriWisata;
use App\Models\Wisata;
use App\Services\GuestVisitorService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request, GuestVisitorService $guestVisitorService): View
    {
        $guestVisitorService->getOrCreateGuestVisitor($request);
        $userLocation = $this->validatedUserLocation($request);

        $wisata = Wisata::with('kategoriWisata')
            ->withMin([
                'hotels as harga_hotel_termurah' => fn ($query) => $query->where('status', 'aktif'),
            ], 'harga_min')
            ->withAvg(['ratingKunjungan as rata_rata_rating' => fn ($query) => $query->where('status', 'approved')], 'rating')
            ->withCount(['ratingKunjungan as jumlah_rating' => fn ($query) => $query->where('status', 'approved')])
            ->withAvg(['ratingKunjungan as rating_aplikasi' => fn ($query) => $query->where('status', 'approved')], 'rating')
            ->withCount(['ratingKunjungan as jumlah_rating_aplikasi' => fn ($query) => $query->where('status', 'approved')])
            ->where('status', 'aktif')
            ->get()
            ->each(function (Wisata $item) use ($userLocation) {
                $item->home_rating_score = $this->calculateRatingScore($item);
                $item->distance_km = $userLocation !== null
                    ? $this->calculateDistanceKm(
                        $userLocation['lat'],
                        $userLocation['lng'],
                        $item->latitude !== null ? (float) $item->latitude : null,
                        $item->longitude !== null ? (float) $item->longitude : null,
                    )
                    : null;
            })
            ->sort(function (Wisata $first, Wisata $second) use ($userLocation) {
                if ($userLocation !== null) {
                    if ($first->distance_km === null) {
                        return 1;
                    }
                    if ($second->distance_km === null) {
                        return -1;
                    }
                    $distanceComparison = $first->distance_km <=> $second->distance_km;
                    if ($distanceComparison !== 0) {
                        return $distanceComparison;
                    }
                }
                $ratingComparison = $second->home_rating_score <=> $first->home_rating_score;

                if ($ratingComparison !== 0) {
                    return $ratingComparison;
                }

                return $first->id <=> $second->id;
            })
            ->take(6)
            ->values();
        $totalWisata = Wisata::where('status', 'aktif')->count();
        $totalKategori = KategoriWisata::count();
        $totalPengunjungHariIni = GuestVisitor::whereDate('tanggal_akses', today())->count();
        $totalPengunjung = GuestVisitor::count();
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

        return view('wisatawan.home', compact(
            'wisata',
            'totalWisata',
            'totalKategori',
            'totalPengunjungHariIni',
            'totalPengunjung',
            'heroImages',
            'userLocation',
        ));
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

    private function calculateRatingScore(Wisata $wisata): float
    {
        $ratingMaps = $wisata->rating_maps !== null ? (float) $wisata->rating_maps : null;
        $ratingAplikasi = $wisata->rata_rata_rating !== null ? (float) $wisata->rata_rata_rating : null;
        $jumlahRatingAplikasi = (int) $wisata->jumlah_rating;

        $jumlahMaps = max(0, (int) ($wisata->jumlah_rating_maps ?? 0));
        $total = $jumlahMaps + $jumlahRatingAplikasi;
        if ($total > 0) {
            return (($ratingMaps ?? 0) * $jumlahMaps + ($ratingAplikasi ?? 0) * $jumlahRatingAplikasi) / $total;
        }

        return 0.0;
    }
}
