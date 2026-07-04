<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreHotelRequest;
use App\Http\Requests\Admin\UpdateHotelRequest;
use App\Models\Hotel;
use App\Models\LogAktivitas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class HotelController extends Controller
{
    public function index(Request $request): View
    {
        $hotels = Hotel::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = '%'.$request->string('search').'%';
                $query->where(fn ($subquery) => $subquery
                    ->where('nama_hotel', 'like', $search)
                    ->orWhere('alamat', 'like', $search));
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.hotels.index', compact('hotels'));
    }

    public function create(): View
    {
        return view('admin.hotels.create');
    }

    public function store(StoreHotelRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('gambar');
        $data['slug'] = $this->uniqueSlug($data['nama_hotel']);

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('hotels', 'public');
        }

        $hotel = Hotel::create($data);
        $this->log($request, 'Tambah Hotel', "Hotel {$hotel->nama_hotel} ditambahkan.");

        return redirect()->route('admin.hotels.show', $hotel)->with('success', 'Data hotel berhasil ditambahkan.');
    }

    public function show(Hotel $hotel): View
    {
        $hotel->load(['wisata.kategoriWisata']);

        return view('admin.hotels.show', compact('hotel'));
    }

    public function edit(Hotel $hotel): View
    {
        return view('admin.hotels.edit', compact('hotel'));
    }

    public function update(UpdateHotelRequest $request, Hotel $hotel): RedirectResponse
    {
        $data = $request->safe()->except('gambar');
        $data['slug'] = $this->uniqueSlug($data['nama_hotel'], $hotel);

        if ($request->hasFile('gambar')) {
            $oldPath = $hotel->gambar;
            $data['gambar'] = $request->file('gambar')->store('hotels', 'public');

            if ($oldPath && ! Str::startsWith($oldPath, ['http://', 'https://'])) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        $hotel->update($data);
        $this->log($request, 'Ubah Hotel', "Hotel {$hotel->nama_hotel} diperbarui.");

        return redirect()->route('admin.hotels.show', $hotel)->with('success', 'Data hotel berhasil diperbarui.');
    }

    public function destroy(Request $request, Hotel $hotel): RedirectResponse
    {
        $nama = $hotel->nama_hotel;
        $hotel->delete();
        $this->log($request, 'Hapus Hotel', "Hotel {$nama} dihapus sementara.");

        return redirect()->route('admin.hotels.index')->with('success', 'Data hotel berhasil dihapus.');
    }

    private function uniqueSlug(string $name, ?Hotel $except = null): string
    {
        $base = Str::slug($name) ?: 'hotel';
        $slug = $base;
        $counter = 2;

        while (Hotel::withTrashed()->where('slug', $slug)->when($except, fn ($query) => $query->whereKeyNot($except->id))->exists()) {
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
