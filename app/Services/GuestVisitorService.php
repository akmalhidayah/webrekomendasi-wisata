<?php

namespace App\Services;

use App\Models\GuestVisitor;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GuestVisitorService
{
    public function getOrCreateGuestVisitor(Request $request): GuestVisitor
    {
        $storedCode = $request->session()->get('kode_guest');

        if ($storedCode && $guest = GuestVisitor::where('kode_guest', $storedCode)->first()) {
            return $guest;
        }

        do {
            $code = 'GST-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
        } while (GuestVisitor::where('kode_guest', $code)->exists());

        $guest = GuestVisitor::create([
            'kode_guest' => $code,
            'session_id' => $request->session()->getId(),
            'tanggal_akses' => today(),
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 1000, ''),
        ]);

        $request->session()->put('kode_guest', $guest->kode_guest);

        LogAktivitas::create([
            'guest_visitor_id' => $guest->id,
            'aktivitas' => 'Guest Visitor Dibuat',
            'deskripsi' => 'Pengunjung anonim mengakses fitur rekomendasi.',
            'ip_address' => $request->ip(),
        ]);

        return $guest;
    }
}
