@php
    $selectedHotels = collect(old('hotel_terkait', []));

    if ($selectedHotels->isEmpty() && isset($wisata)) {
        $selectedHotels = $wisata->hotels
            ->mapWithKeys(fn ($hotel) => [
                $hotel->pivot->urutan => [
                    'hotel_id' => $hotel->id,
                    'keterangan' => $hotel->pivot->keterangan,
                ],
            ]);
    }

    $mapsValue = old('maps_url', $wisata->maps_url ?? $wisata->link_maps ?? '');
@endphp

<div class="card shadow-sm mb-4">
    <div class="card-header"><strong>Informasi Utama</strong></div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Kategori</label><select class="form-select" name="kategori_wisata_id" required><option value="">Pilih kategori</option>@foreach ($kategori as $item)<option value="{{ $item->id }}" @selected(old('kategori_wisata_id', $wisata->kategori_wisata_id ?? '') == $item->id)>{{ $item->nama_kategori }}</option>@endforeach</select></div>
            <div class="col-md-6"><label class="form-label">Nama Wisata</label><input class="form-control" name="nama_wisata" value="{{ old('nama_wisata', $wisata->nama_wisata ?? '') }}" required></div>
            <div class="col-md-6"><label class="form-label">Jenis Wisata</label><input class="form-control" name="jenis_wisata" value="{{ old('jenis_wisata', $wisata->jenis_wisata ?? '') }}" required></div>
            <div class="col-md-6"><label class="form-label">Jam Operasional</label><input class="form-control" name="jam_operasional" value="{{ old('jam_operasional', $wisata->jam_operasional ?? '') }}"></div>
            <div class="col-12"><label class="form-label">Deskripsi</label><textarea class="form-control" name="deskripsi" rows="5">{{ old('deskripsi', $wisata->deskripsi ?? '') }}</textarea></div>
            <div class="col-12"><label class="form-label">Alamat</label><textarea class="form-control" name="alamat" rows="2" required>{{ old('alamat', $wisata->alamat ?? '') }}</textarea></div>
            <div class="col-md-4"><label class="form-label">Kecamatan</label><input class="form-control" name="kecamatan" value="{{ old('kecamatan', $wisata->kecamatan ?? '') }}"></div>
            <div class="col-md-4"><label class="form-label">Kota</label><input class="form-control" name="kota" value="{{ old('kota', $wisata->kota ?? 'Makassar') }}"></div>
            <div class="col-md-4"><label class="form-label">Provinsi</label><input class="form-control" name="provinsi" value="{{ old('provinsi', $wisata->provinsi ?? 'Sulawesi Selatan') }}"></div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header"><strong>Biaya dan Rating Maps</strong></div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label">Harga Tiket</label><input type="number" min="0" step="0.01" class="form-control" name="harga_tiket" value="{{ old('harga_tiket', $wisata->harga_tiket ?? 0) }}"></div>
            <div class="col-md-4"><label class="form-label">Estimasi Transportasi</label><input type="number" min="0" step="0.01" class="form-control" name="estimasi_transportasi" value="{{ old('estimasi_transportasi', $wisata->estimasi_transportasi ?? 0) }}"></div>
            <div class="col-md-4"><label class="form-label">Biaya Lainnya</label><input type="number" min="0" step="0.01" class="form-control" name="estimasi_biaya_lainnya" value="{{ old('estimasi_biaya_lainnya', $wisata->estimasi_biaya_lainnya ?? 0) }}"></div>
            <div class="col-md-6"><label class="form-label">Rating Maps</label><input type="number" min="0" max="5" step="0.1" class="form-control" name="rating_maps" value="{{ old('rating_maps', $wisata->rating_maps ?? '') }}"></div>
            <div class="col-md-6"><label class="form-label">Jumlah Rating Maps</label><input type="number" min="0" class="form-control" name="jumlah_rating_maps" value="{{ old('jumlah_rating_maps', $wisata->jumlah_rating_maps ?? 0) }}"></div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header"><strong>Lokasi dan Koordinat</strong></div>
    <div class="card-body">
        <p class="text-muted small mb-3">Latitude dan longitude digunakan untuk menghitung jarak pengguna ke destinasi wisata. Klik kanan titik lokasi pada Google Maps, lalu salin angka koordinat.</p>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Latitude</label>
                <input type="number" step="0.0000001" class="form-control" name="latitude" value="{{ old('latitude', $wisata->latitude ?? '') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Longitude</label>
                <input type="number" step="0.0000001" class="form-control" name="longitude" value="{{ old('longitude', $wisata->longitude ?? '') }}">
            </div>
            <div class="col-12">
                <label class="form-label">Link Google Maps</label>
                <input type="url" class="form-control" name="maps_url" value="{{ $mapsValue }}">
                <div class="form-text">Link Google Maps digunakan untuk tombol buka maps.</div>
            </div>
            <div class="col-12">
                <a class="btn btn-sm btn-outline-secondary" href="https://www.google.com/maps" target="_blank" rel="noopener noreferrer">
                    <i class="bi bi-map me-1"></i>Buka Google Maps
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header"><strong>Hotel Terkait / Hotel Terdekat</strong></div>
    <div class="card-body">
        <p class="text-muted small mb-3">Pilih maksimal 3 hotel yang paling dekat atau paling relevan dengan destinasi ini.</p>
        @if ($hotels->isEmpty())
            <div class="alert alert-info mb-0">Belum ada data hotel. Tambahkan hotel terlebih dahulu pada menu Data Hotel.</div>
        @else
            <div class="row g-3">
                @for ($order = 1; $order <= 3; $order++)
                    @php
                        $hotelValue = data_get($selectedHotels, "{$order}.hotel_id");
                        $noteValue = data_get($selectedHotels, "{$order}.keterangan");
                    @endphp
                    <div class="col-md-5">
                        <label class="form-label">Hotel {{ $order }}</label>
                        <select class="form-select" name="hotel_terkait[{{ $order }}][hotel_id]">
                            <option value="">Pilih hotel</option>
                            @foreach ($hotels as $hotel)
                                <option value="{{ $hotel->id }}" @selected((string) $hotelValue === (string) $hotel->id)>{{ $hotel->nama_hotel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-7">
                        <label class="form-label">Keterangan {{ $order }}</label>
                        <input class="form-control" name="hotel_terkait[{{ $order }}][keterangan]" value="{{ $noteValue }}" placeholder="Contoh: dekat kawasan pantai">
                    </div>
                @endfor
            </div>
        @endif
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header"><strong>Foto dan Status</strong></div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Status</label><select class="form-select" name="status" required>@foreach (['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif'] as $value => $label)<option value="{{ $value }}" @selected(old('status', $wisata->status ?? 'aktif') === $value)>{{ $label }}</option>@endforeach</select></div>
            <div class="col-md-6"><label class="form-label">Foto Utama</label><input type="file" class="form-control" name="foto_utama" accept=".jpg,.jpeg,.png,.webp"><div class="form-text">JPG, PNG, atau WebP. Maksimal 2 MB.</div></div>
        </div>
    </div>
</div>

<div class="mt-4"><button class="btn btn-primary">Simpan</button> <a href="{{ route('admin.wisata.index') }}" class="btn btn-light">Batal</a></div>
