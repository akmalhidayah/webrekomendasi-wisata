<div class="row g-3">
    <div class="col-md-6"><label class="form-label">Kategori</label><select class="form-select" name="kategori_wisata_id" required><option value="">Pilih kategori</option>@foreach ($kategori as $item)<option value="{{ $item->id }}" @selected(old('kategori_wisata_id', $wisata->kategori_wisata_id ?? '') == $item->id)>{{ $item->nama_kategori }}</option>@endforeach</select></div>
    <div class="col-md-6"><label class="form-label">Nama Wisata</label><input class="form-control" name="nama_wisata" value="{{ old('nama_wisata', $wisata->nama_wisata ?? '') }}" required></div>
    <div class="col-md-6"><label class="form-label">Jenis Wisata</label><input class="form-control" name="jenis_wisata" value="{{ old('jenis_wisata', $wisata->jenis_wisata ?? '') }}" required></div>
    <div class="col-md-6"><label class="form-label">Status</label><select class="form-select" name="status" required>@foreach (['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif'] as $value => $label)<option value="{{ $value }}" @selected(old('status', $wisata->status ?? 'aktif') === $value)>{{ $label }}</option>@endforeach</select></div>
    <div class="col-12"><label class="form-label">Deskripsi</label><textarea class="form-control" name="deskripsi" rows="5">{{ old('deskripsi', $wisata->deskripsi ?? '') }}</textarea></div>
    <div class="col-12"><label class="form-label">Alamat</label><textarea class="form-control" name="alamat" rows="2" required>{{ old('alamat', $wisata->alamat ?? '') }}</textarea></div>
    <div class="col-md-4"><label class="form-label">Kecamatan</label><input class="form-control" name="kecamatan" value="{{ old('kecamatan', $wisata->kecamatan ?? '') }}"></div>
    <div class="col-md-4"><label class="form-label">Kota</label><input class="form-control" name="kota" value="{{ old('kota', $wisata->kota ?? 'Makassar') }}"></div>
    <div class="col-md-4"><label class="form-label">Provinsi</label><input class="form-control" name="provinsi" value="{{ old('provinsi', $wisata->provinsi ?? 'Sulawesi Selatan') }}"></div>
    <div class="col-12"><label class="form-label">Link Google Maps</label><input type="url" class="form-control" name="link_maps" value="{{ old('link_maps', $wisata->link_maps ?? '') }}"></div>
    <div class="col-md-4"><label class="form-label">Harga Tiket</label><input type="number" min="0" step="0.01" class="form-control" name="harga_tiket" value="{{ old('harga_tiket', $wisata->harga_tiket ?? 0) }}"></div>
    <div class="col-md-4"><label class="form-label">Estimasi Transportasi</label><input type="number" min="0" step="0.01" class="form-control" name="estimasi_transportasi" value="{{ old('estimasi_transportasi', $wisata->estimasi_transportasi ?? 0) }}"></div>
    <div class="col-md-4"><label class="form-label">Biaya Lainnya</label><input type="number" min="0" step="0.01" class="form-control" name="estimasi_biaya_lainnya" value="{{ old('estimasi_biaya_lainnya', $wisata->estimasi_biaya_lainnya ?? 0) }}"></div>
    <div class="col-md-6"><label class="form-label">Jam Operasional</label><input class="form-control" name="jam_operasional" value="{{ old('jam_operasional', $wisata->jam_operasional ?? '') }}"></div>
    <div class="col-md-6"><label class="form-label">Foto Utama</label><input type="file" class="form-control" name="foto_utama" accept=".jpg,.jpeg,.png,.webp"><div class="form-text">JPG, PNG, atau WebP. Maksimal 2 MB.</div></div>
</div>
<div class="mt-4"><button class="btn btn-primary">Simpan</button> <a href="{{ route('admin.wisata.index') }}" class="btn btn-light">Batal</a></div>
