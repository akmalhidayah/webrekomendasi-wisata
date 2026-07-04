<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <strong>Informasi Hotel</strong>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Nama Hotel</label>
                        <input class="form-control" name="nama_hotel" value="{{ old('nama_hotel', $hotel->nama_hotel ?? '') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" required>
                            @foreach (['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', $hotel->status ?? 'aktif') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Alamat</label>
                        <textarea class="form-control" name="alamat" rows="2">{{ old('alamat', $hotel->alamat ?? '') }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" rows="4">{{ old('deskripsi', $hotel->deskripsi ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <strong>Harga dan Rating</strong>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Harga Minimum</label>
                        <input type="number" min="0" step="0.01" class="form-control" name="harga_min" value="{{ old('harga_min', $hotel->harga_min ?? 0) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Harga Maksimum</label>
                        <input type="number" min="0" step="0.01" class="form-control" name="harga_max" value="{{ old('harga_max', $hotel->harga_max ?? 0) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Rating Hotel</label>
                        <input type="number" min="0" max="5" step="0.1" class="form-control" name="rating_hotel" value="{{ old('rating_hotel', $hotel->rating_hotel ?? '') }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header">
                <strong>Link Booking dan Maps</strong>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Link Traveloka</label>
                        <input type="url" class="form-control" name="traveloka_url" value="{{ old('traveloka_url', $hotel->traveloka_url ?? '') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Link Google Maps</label>
                        <input type="url" class="form-control" name="maps_url" value="{{ old('maps_url', $hotel->maps_url ?? '') }}">
                    </div>
                    <div class="col-12">
                        <a class="btn btn-sm btn-outline-secondary" href="https://www.google.com/maps" target="_blank" rel="noopener">
                            <i class="bi bi-map me-1"></i>Buka Google Maps
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header">
                <strong>Gambar Hotel</strong>
            </div>
            <div class="card-body">
                @if (! empty($hotel?->gambar_url))
                    <img class="img-fluid rounded mb-3" style="width:100%;max-height:230px;object-fit:cover" src="{{ $hotel->gambar_url }}" alt="{{ $hotel->nama_hotel }}">
                @else
                    <div class="bg-light rounded d-flex flex-column align-items-center justify-content-center text-muted mb-3" style="min-height:180px">
                        <i class="bi bi-image fs-2"></i>
                        <small>Gambar belum tersedia</small>
                    </div>
                @endif
                <input type="file" class="form-control" name="gambar" accept=".jpg,.jpeg,.png,.webp">
                <div class="form-text">JPG, PNG, atau WebP. Maksimal 2 MB.</div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button class="btn btn-primary">Simpan</button>
    <a href="{{ route('admin.hotels.index') }}" class="btn btn-light">Batal</a>
</div>
