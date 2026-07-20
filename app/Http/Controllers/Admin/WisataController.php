<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreWisataRequest;
use App\Http\Requests\Admin\UpdateWisataRequest;
use App\Models\Hotel;
use App\Models\KategoriWisata;
use App\Models\LogAktivitas;
use App\Models\Wisata;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class WisataController extends Controller
{
    public function index(Request $request): View
    {
        $wisata = Wisata::query()
            ->with('kategoriWisata')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%'.$request->string('search').'%';
                $query->where(fn ($subquery) => $subquery
                    ->where('nama_wisata', 'like', $search)
                    ->orWhere('jenis_wisata', 'like', $search)
                    ->orWhere('alamat', 'like', $search));
            })
            ->when($request->filled('kategori'), fn ($query) => $query->where('kategori_wisata_id', $request->integer('kategori')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->latest()
            ->paginate(10)
            ->withQueryString();
        $kategori = KategoriWisata::orderBy('nama_kategori')->get();

        return view('admin.wisata.index', compact('wisata', 'kategori'));
    }

    public function create(): View
    {
        $kategori = KategoriWisata::orderBy('nama_kategori')->get();
        $hotels = Hotel::where('status', 'aktif')->orderBy('nama_hotel')->get();

        return view('admin.wisata.create', compact('kategori', 'hotels'));
    }

    public function store(StoreWisataRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['foto_utama', 'hotel_terkait']);
        $data['slug'] = $this->uniqueSlug($data['nama_wisata']);
        $this->syncMapsUrl($data);
        $this->setCalculatedCosts($data);

        $newPath = $request->hasFile('foto_utama') ? $request->file('foto_utama')->store('wisata', 'public') : null;
        if ($newPath) {
            $data['foto_utama'] = $newPath;
        }
        try {
            $wisata = DB::transaction(function () use ($data, $request) {
                $wisata = Wisata::create($data);
                $this->syncHotels($wisata, $request->validated('hotel_terkait', []));
                $this->log($request, 'Tambah Wisata', "Destinasi {$wisata->nama_wisata} ditambahkan.");

                return $wisata;
            });
        } catch (Throwable $exception) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }
            throw $exception;
        }

        return redirect()->route('admin.wisata.show', $wisata)->with('success', 'Destinasi wisata berhasil ditambahkan.');
    }

    public function show(Wisata $wisata): View
    {
        $wisata->load(['kategoriWisata', 'fasilitasWisata', 'fotoWisata', 'hotels']);

        return view('admin.wisata.show', compact('wisata'));
    }

    public function edit(Wisata $wisata): View
    {
        $wisata->load('hotels');
        $kategori = KategoriWisata::orderBy('nama_kategori')->get();
        $hotels = Hotel::where('status', 'aktif')->orderBy('nama_hotel')->get();

        return view('admin.wisata.edit', compact('wisata', 'kategori', 'hotels'));
    }

    public function update(UpdateWisataRequest $request, Wisata $wisata): RedirectResponse
    {
        $data = $request->safe()->except(['foto_utama', 'hotel_terkait']);
        $data['slug'] = $this->uniqueSlug($data['nama_wisata'], $wisata);
        $this->syncMapsUrl($data);
        $this->setCalculatedCosts($data);

        $oldPath = $wisata->foto_utama;
        $newPath = $request->hasFile('foto_utama') ? $request->file('foto_utama')->store('wisata', 'public') : null;
        if ($newPath) {
            $data['foto_utama'] = $newPath;
        }
        try {
            DB::transaction(function () use ($wisata, $data, $request) {
                $wisata->update($data);
                $this->syncHotels($wisata, $request->validated('hotel_terkait', []));
                $this->log($request, 'Ubah Wisata', "Destinasi {$wisata->nama_wisata} diperbarui.");
            });
        } catch (Throwable $exception) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }
            throw $exception;
        }
        if ($newPath && $oldPath && ! $wisata->fotoWisata()->where('path_foto', $oldPath)->exists()) {
            Storage::disk('public')->delete($oldPath);
        }

        return redirect()->route('admin.wisata.show', $wisata)->with('success', 'Destinasi wisata berhasil diperbarui.');
    }

    public function destroy(Request $request, Wisata $wisata): RedirectResponse
    {
        $nama = $wisata->nama_wisata;
        $wisata->delete();
        $this->log($request, 'Hapus Wisata', "Destinasi {$nama} dihapus sementara.");

        return redirect()->route('admin.wisata.index')->with('success', 'Destinasi wisata berhasil dihapus.');
    }

    private function setCalculatedCosts(array &$data): void
    {
        foreach (['harga_tiket', 'estimasi_transportasi', 'estimasi_biaya_lainnya'] as $field) {
            $data[$field] = (float) ($data[$field] ?? 0);
        }

        $data['total_estimasi_biaya'] = $data['harga_tiket'] + $data['estimasi_transportasi'] + $data['estimasi_biaya_lainnya'];

        $data['rating_maps'] = ($data['rating_maps'] ?? '') === '' ? null : (float) $data['rating_maps'];
        $data['jumlah_rating_maps'] = ($data['jumlah_rating_maps'] ?? '') === '' ? 0 : (int) $data['jumlah_rating_maps'];
    }

    private function syncMapsUrl(array &$data): void
    {
        if (array_key_exists('maps_url', $data)) {
            $data['link_maps'] = $data['maps_url'] ?: null;
        }
    }

    private function syncHotels(Wisata $wisata, array $hotels = []): void
    {
        $syncData = [];

        foreach (array_slice($hotels, 0, 3, true) as $index => $hotel) {
            $hotelId = (int) ($hotel['hotel_id'] ?? 0);

            if ($hotelId <= 0 || isset($syncData[$hotelId])) {
                continue;
            }

            $syncData[$hotelId] = [
                'urutan' => max(1, min(3, (int) $index)),
                'keterangan' => $hotel['keterangan'] ?? null,
            ];
        }

        $wisata->hotels()->sync($syncData);
    }

    private function uniqueSlug(string $name, ?Wisata $except = null): string
    {
        $base = Str::slug($name) ?: 'wisata';
        $slug = $base;
        $counter = 2;

        while (Wisata::withTrashed()->where('slug', $slug)->when($except, fn ($query) => $query->whereKeyNot($except->id))->exists()) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
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
