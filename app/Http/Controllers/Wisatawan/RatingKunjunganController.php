<?php

namespace App\Http\Controllers\Wisatawan;

use App\Events\RatingKunjunganBaru;
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
        $rating = RatingKunjungan::updateOrCreate(
            ['guest_visitor_id' => $guest->id, 'wisata_id' => $request->integer('wisata_id')],
            [...$request->safe()->only(['rating', 'ulasan']), 'pernah_dikunjungi' => true, 'status' => 'approved'],
        );

        event(new RatingKunjunganBaru($rating));

        LogAktivitas::create([
            'guest_visitor_id' => $guest->id,
            'aktivitas' => 'Rating Kunjungan Baru',
            'deskripsi' => 'Pengunjung mengirim rating kunjungan dan otomatis approved.',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Terima kasih, rating Anda berhasil dikirim.');
    }
}
