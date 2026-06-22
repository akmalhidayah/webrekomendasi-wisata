<div class="mb-3"><label class="form-label">Nama Kategori</label><input class="form-control" name="nama_kategori" value="{{ old('nama_kategori', $kategoriWisata->nama_kategori ?? '') }}" maxlength="150" required></div>
<div class="mb-3"><label class="form-label">Deskripsi</label><textarea class="form-control" name="deskripsi" rows="4">{{ old('deskripsi', $kategoriWisata->deskripsi ?? '') }}</textarea></div>
<button class="btn btn-primary">Simpan</button>
<a href="{{ route('admin.kategori-wisata.index') }}" class="btn btn-light">Batal</a>
