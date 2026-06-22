<?php

namespace App\Http\Controllers\Wisatawan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Wisatawan\StoreRatingKunjunganRequest;
use App\Models\LogAktivitas;
use App\Models\RatingKunjungan;
use App\Services\GuestVisitorService;
use Illuminate\Http\RedirectResponse;

class RatingKunjunganController extends Controller
{
    public function store(StoreRatingKunjunganRequest $request, GuestVisitorService $guestService): RedirectResponse
    {
        if (! $request->boolean('pernah_dikunjungi')) {
            return back()->with('error', 'Rating hanya dapat diberikan jika Anda pernah mengunjungi destinasi tersebut.');
        }

        $guest = $guestService->getOrCreateGuestVisitor($request);
        RatingKunjungan::create([
            ...$request->safe()->only(['wisata_id', 'rating', 'ulasan']),
            'guest_visitor_id' => $guest->id,
            'pernah_dikunjungi' => true,
            'status' => 'pending',
        ]);
        LogAktivitas::create([
            'guest_visitor_id' => $guest->id,
            'aktivitas' => 'Rating Kunjungan Dikirim',
            'deskripsi' => 'Pengunjung mengirim rating kunjungan untuk divalidasi admin.',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Terima kasih, rating Anda berhasil dikirim dan menunggu validasi admin.');
    }
}
