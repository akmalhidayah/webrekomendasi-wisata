<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreKategoriWisataRequest;
use App\Http\Requests\Admin\UpdateKategoriWisataRequest;
use App\Models\KategoriWisata;
use App\Models\LogAktivitas;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class KategoriWisataController extends Controller
{
    public function index(Request $request): View
    {
        $kategori = KategoriWisata::query()
            ->withCount('wisata')
            ->when($request->filled('search'), fn ($query) => $query
                ->where('nama_kategori', 'like', '%'.$request->string('search').'%'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.kategori.index', compact('kategori'));
    }

    public function create(): View
    {
        return view('admin.kategori.create');
    }

    public function store(StoreKategoriWisataRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = $this->uniqueSlug($data['nama_kategori']);
        $kategoriWisata = KategoriWisata::create($data);
        $this->log($request, 'Tambah Kategori', "Kategori {$kategoriWisata->nama_kategori} ditambahkan.");

        return redirect()->route('admin.kategori-wisata.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function show(KategoriWisata $kategoriWisata): View
    {
        $kategoriWisata->loadCount('wisata')->load(['wisata' => fn ($query) => $query->latest()->limit(10)]);

        return view('admin.kategori.show', compact('kategoriWisata'));
    }

    public function edit(KategoriWisata $kategoriWisata): View
    {
        return view('admin.kategori.edit', compact('kategoriWisata'));
    }

    public function update(UpdateKategoriWisataRequest $request, KategoriWisata $kategoriWisata): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = $this->uniqueSlug($data['nama_kategori'], $kategoriWisata);
        $kategoriWisata->update($data);
        $this->log($request, 'Ubah Kategori', "Kategori {$kategoriWisata->nama_kategori} diperbarui.");

        return redirect()->route('admin.kategori-wisata.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Request $request, KategoriWisata $kategoriWisata): RedirectResponse
    {
        if ($kategoriWisata->wisata()->withTrashed()->exists()) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki destinasi wisata.');
        }

        $nama = $kategoriWisata->nama_kategori;
        try {
            $kategoriWisata->delete();
        } catch (QueryException) {
            return back()->with('error', 'Kategori masih digunakan dan tidak dapat dihapus.');
        }
        $this->log($request, 'Hapus Kategori', "Kategori {$nama} dihapus.");

        return redirect()->route('admin.kategori-wisata.index')->with('success', 'Kategori berhasil dihapus.');
    }

    private function uniqueSlug(string $name, ?KategoriWisata $except = null): string
    {
        $base = Str::slug($name) ?: 'kategori';
        $slug = $base;
        $counter = 2;

        while (KategoriWisata::where('slug', $slug)->when($except, fn ($query) => $query->whereKeyNot($except->id))->exists()) {
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
