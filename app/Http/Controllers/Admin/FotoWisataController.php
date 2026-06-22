<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFotoWisataRequest;
use App\Http\Requests\Admin\UpdateFotoWisataRequest;
use App\Models\FotoWisata;
use App\Models\Wisata;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class FotoWisataController extends Controller
{
    public function index(Wisata $wisata): View
    {
        $foto = $wisata->fotoWisata()->latest()->get();

        return view('admin.foto.index', compact('wisata', 'foto'));
    }

    public function store(StoreFotoWisataRequest $request, Wisata $wisata): RedirectResponse
    {
        $makePrimary = $request->boolean('is_utama');

        foreach ($request->file('path_foto') as $index => $upload) {
            $path = $upload->store('wisata', 'public');
            $photo = $wisata->fotoWisata()->create([
                'path_foto' => $path,
                'caption' => $request->string('caption')->toString() ?: null,
                'is_utama' => false,
            ]);

            if ($makePrimary && $index === 0) {
                $this->makePrimary($wisata, $photo);
            }
        }

        return back()->with('success', 'Foto wisata berhasil diunggah.');
    }

    public function update(UpdateFotoWisataRequest $request, Wisata $wisata, FotoWisata $fotoWisata): RedirectResponse
    {
        $this->ensureOwnedBy($wisata, $fotoWisata);
        $data = $request->safe()->except('path_foto');

        if ($request->hasFile('path_foto')) {
            $oldPath = $fotoWisata->path_foto;
            $data['path_foto'] = $request->file('path_foto')->store('wisata', 'public');
            Storage::disk('public')->delete($oldPath);

            if ($fotoWisata->is_utama || $wisata->foto_utama === $oldPath) {
                $wisata->update(['foto_utama' => $data['path_foto']]);
            }
        }

        $fotoWisata->update($data);

        return back()->with('success', 'Data foto berhasil diperbarui.');
    }

    public function destroy(Wisata $wisata, FotoWisata $fotoWisata): RedirectResponse
    {
        $this->ensureOwnedBy($wisata, $fotoWisata);
        $path = $fotoWisata->path_foto;
        $wasPrimary = $fotoWisata->is_utama || $wisata->foto_utama === $path;

        DB::transaction(function () use ($wisata, $fotoWisata, $wasPrimary) {
            $fotoWisata->delete();

            if ($wasPrimary) {
                $replacement = $wisata->fotoWisata()->first();
                $wisata->fotoWisata()->update(['is_utama' => false]);
                $replacement?->update(['is_utama' => true]);
                $wisata->update(['foto_utama' => $replacement?->path_foto]);
            }
        });

        Storage::disk('public')->delete($path);

        return back()->with('success', 'Foto berhasil dihapus.');
    }

    public function setUtama(Wisata $wisata, FotoWisata $fotoWisata): RedirectResponse
    {
        $this->ensureOwnedBy($wisata, $fotoWisata);
        $this->makePrimary($wisata, $fotoWisata);

        return back()->with('success', 'Foto utama berhasil diperbarui.');
    }

    private function makePrimary(Wisata $wisata, FotoWisata $fotoWisata): void
    {
        DB::transaction(function () use ($wisata, $fotoWisata) {
            $wisata->fotoWisata()->update(['is_utama' => false]);
            $fotoWisata->update(['is_utama' => true]);
            $wisata->update(['foto_utama' => $fotoWisata->path_foto]);
        });
    }

    private function ensureOwnedBy(Wisata $wisata, FotoWisata $fotoWisata): void
    {
        abort_unless($fotoWisata->wisata_id === $wisata->id, 404);
    }
}
