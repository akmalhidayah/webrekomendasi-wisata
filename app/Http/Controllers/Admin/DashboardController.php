<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FasilitasWisata;
use App\Models\FotoWisata;
use App\Models\GuestVisitor;
use App\Models\HasilRekomendasi;
use App\Models\KategoriWisata;
use App\Models\RatingKunjungan;
use App\Models\SurveyPreferensi;
use App\Models\Wisata;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $statistik = [
            'Kategori Wisata' => KategoriWisata::count(),
            'Destinasi Wisata' => Wisata::count(),
            'Fasilitas' => FasilitasWisata::count(),
            'Foto Wisata' => FotoWisata::count(),
            'Guest Visitor' => GuestVisitor::count(),
            'Survei Preferensi' => SurveyPreferensi::count(),
            'Rating Kunjungan' => RatingKunjungan::count(),
            'Hasil Rekomendasi' => HasilRekomendasi::count(),
            'Rating Pending' => RatingKunjungan::where('status', 'pending')->count(),
            'Rating Approved' => RatingKunjungan::where('status', 'approved')->count(),
            'Rating Ditolak' => RatingKunjungan::where('status', 'ditolak')->count(),
        ];

        $wisataRatingTertinggi = Wisata::query()
            ->whereHas('ratingKunjungan', fn ($query) => $query->where('status', 'approved'))
            ->withAvg(['ratingKunjungan as rating_approved_avg' => fn ($query) => $query->where('status', 'approved')], 'rating')
            ->orderByDesc('rating_approved_avg')
            ->first();
        $wisataPalingDirekomendasikan = Wisata::query()
            ->whereHas('hasilRekomendasi')
            ->withCount('hasilRekomendasi')
            ->orderByDesc('hasil_rekomendasi_count')
            ->first();

        $topRecommendationIds = Wisata::query()
            ->whereHas('hasilRekomendasi')
            ->withCount('hasilRekomendasi')
            ->orderByDesc('hasil_rekomendasi_count')
            ->limit(8)
            ->pluck('id');

        $topRatingIds = Wisata::query()
            ->whereHas('ratingKunjungan', fn ($query) => $query->where('status', 'approved'))
            ->withAvg(['ratingKunjungan as rating_approved_avg' => fn ($query) => $query->where('status', 'approved')], 'rating')
            ->orderByDesc('rating_approved_avg')
            ->limit(8)
            ->pluck('id');

        $chartWisataIds = $topRecommendationIds
            ->merge($topRatingIds)
            ->unique()
            ->take(10)
            ->values();

        $chartPerbandingan = Wisata::query()
            ->whereIn('id', $chartWisataIds)
            ->withCount('hasilRekomendasi')
            ->withAvg(['ratingKunjungan as rating_approved_avg' => fn ($query) => $query->where('status', 'approved')], 'rating')
            ->withCount(['ratingKunjungan as rating_approved_count' => fn ($query) => $query->where('status', 'approved')])
            ->get(['id', 'nama_wisata'])
            ->sortByDesc('hasil_rekomendasi_count')
            ->values()
            ->map(fn (Wisata $wisata) => [
                'nama' => $wisata->nama_wisata,
                'rekomendasi' => (int) $wisata->hasil_rekomendasi_count,
                'rating' => $wisata->rating_approved_avg !== null ? round((float) $wisata->rating_approved_avg, 2) : null,
                'jumlah_rating' => (int) $wisata->rating_approved_count,
            ]);

        $guestTerbaru = GuestVisitor::query()
            ->withCount(['surveyPreferensi', 'ratingKunjungan', 'hasilRekomendasi'])
            ->latest()
            ->limit(6)
            ->get();

        return view('admin.dashboard', compact(
            'statistik',
            'wisataRatingTertinggi',
            'wisataPalingDirekomendasikan',
            'chartPerbandingan',
            'guestTerbaru',
        ));
    }
}
