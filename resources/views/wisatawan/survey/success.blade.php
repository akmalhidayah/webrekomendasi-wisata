@extends('layouts.app')
@section('title', 'Survei Tersimpan')
@section('content')
<div class="container py-5"><div class="row justify-content-center"><div class="col-md-8 col-lg-6"><div class="card shadow-sm text-center"><div class="card-body p-5"><div class="display-4 text-success mb-3">&#10003;</div><h1 class="h3">Survei Berhasil Disimpan</h1><p class="text-muted">Preferensi Anda sudah tersimpan dan siap diproses oleh sistem rekomendasi Collaborative Filtering.</p><form class="d-inline" method="POST" action="{{ route('wisatawan.rekomendasi.proses') }}">@csrf<button class="btn btn-primary">Proses Rekomendasi Sekarang</button></form> <a class="btn btn-outline-secondary" href="{{ route('wisatawan.wisata.index') }}">Lihat Wisata</a></div></div></div></div></div>
@endsection
