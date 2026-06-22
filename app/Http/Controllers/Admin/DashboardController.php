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
            'Rating Disetujui' => RatingKunjungan::where('status', 'disetujui')->count(),
            'Rating Ditolak' => RatingKunjungan::where('status', 'ditolak')->count(),
        ];

        $wisataRatingTertinggi = Wisata::query()
            ->whereHas('ratingKunjungan', fn ($query) => $query->where('status', 'disetujui'))
            ->withAvg(['ratingKunjungan as rating_disetujui_avg' => fn ($query) => $query->where('status', 'disetujui')], 'rating')
            ->orderByDesc('rating_disetujui_avg')
            ->first();
        $wisataPalingDirekomendasikan = Wisata::query()
            ->whereHas('hasilRekomendasi')
            ->withCount('hasilRekomendasi')
            ->orderByDesc('hasil_rekomendasi_count')
            ->first();

        return view('admin.dashboard', compact('statistik', 'wisataRatingTertinggi', 'wisataPalingDirekomendasikan'));
    }
}
