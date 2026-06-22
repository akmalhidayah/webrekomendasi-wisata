<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFasilitasWisataRequest;
use App\Http\Requests\Admin\UpdateFasilitasWisataRequest;
use App\Models\FasilitasWisata;
use App\Models\Wisata;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FasilitasWisataController extends Controller
{
    public function index(Wisata $wisata): View
    {
        $fasilitas = $wisata->fasilitasWisata()->latest()->paginate(15);

        return view('admin.fasilitas.index', compact('wisata', 'fasilitas'));
    }

    public function create(Wisata $wisata): View
    {
        return view('admin.fasilitas.create', compact('wisata'));
    }

    public function store(StoreFasilitasWisataRequest $request, Wisata $wisata): RedirectResponse
    {
        $wisata->fasilitasWisata()->create($request->validated());

        return redirect()->route('admin.wisata.fasilitas.index', $wisata)->with('success', 'Fasilitas berhasil ditambahkan.');
    }

    public function edit(Wisata $wisata, FasilitasWisata $fasilitasWisata): View
    {
        $this->ensureOwnedBy($wisata, $fasilitasWisata);

        return view('admin.fasilitas.edit', compact('wisata', 'fasilitasWisata'));
    }

    public function update(UpdateFasilitasWisataRequest $request, Wisata $wisata, FasilitasWisata $fasilitasWisata): RedirectResponse
    {
        $this->ensureOwnedBy($wisata, $fasilitasWisata);
        $fasilitasWisata->update($request->validated());

        return redirect()->route('admin.wisata.fasilitas.index', $wisata)->with('success', 'Fasilitas berhasil diperbarui.');
    }

    public function destroy(Wisata $wisata, FasilitasWisata $fasilitasWisata): RedirectResponse
    {
        $this->ensureOwnedBy($wisata, $fasilitasWisata);
        $fasilitasWisata->delete();

        return back()->with('success', 'Fasilitas berhasil dihapus.');
    }

    private function ensureOwnedBy(Wisata $wisata, FasilitasWisata $fasilitasWisata): void
    {
        abort_unless($fasilitasWisata->wisata_id === $wisata->id, 404);
    }
}
