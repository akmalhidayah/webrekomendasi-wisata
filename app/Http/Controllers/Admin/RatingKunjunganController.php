<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use App\Models\RatingKunjungan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RatingKunjunganController extends Controller
{
    public function index(Request $request): View
    {
        $ratings = RatingKunjungan::with(['wisata', 'guestVisitor'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%'.$request->string('search').'%';
                $query->where(function ($subquery) use ($search) {
                    $subquery->where('ulasan', 'like', $search)
                        ->orWhereHas('wisata', fn ($wisata) => $wisata->where('nama_wisata', 'like', $search))
                        ->orWhereHas('guestVisitor', fn ($guest) => $guest->where('kode_guest', 'like', $search));
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.rating-kunjungan.index', compact('ratings'));
    }

    public function show(RatingKunjungan $ratingKunjungan): View
    {
        $ratingKunjungan->load(['wisata.kategoriWisata', 'guestVisitor']);

        return view('admin.rating-kunjungan.show', compact('ratingKunjungan'));
    }

    public function setujui(Request $request, RatingKunjungan $ratingKunjungan): RedirectResponse
    {
        $ratingKunjungan->update(['status' => 'disetujui']);
        $this->log($request, 'Rating Disetujui', "Rating untuk {$ratingKunjungan->wisata->nama_wisata} disetujui.");

        return back()->with('success', 'Rating berhasil disetujui.');
    }

    public function tolak(Request $request, RatingKunjungan $ratingKunjungan): RedirectResponse
    {
        $ratingKunjungan->update(['status' => 'ditolak']);
        $this->log($request, 'Rating Ditolak', "Rating untuk {$ratingKunjungan->wisata->nama_wisata} ditolak.");

        return back()->with('success', 'Rating berhasil ditolak.');
    }

    public function destroy(Request $request, RatingKunjungan $ratingKunjungan): RedirectResponse
    {
        $wisata = $ratingKunjungan->wisata->nama_wisata;
        $ratingKunjungan->delete();
        $this->log($request, 'Rating Dihapus', "Rating untuk {$wisata} dihapus.");

        return redirect()->route('admin.rating-kunjungan.index')->with('success', 'Rating berhasil dihapus.');
    }

    private function log(Request $request, string $aktivitas, string $deskripsi): void
    {
        LogAktivitas::create([
            'user_id' => $request->user()->id,
            'aktivitas' => $aktivitas,
            'deskripsi' => $deskripsi,
            'ip_address' => $request->ip(),
        ]);
    }
}
