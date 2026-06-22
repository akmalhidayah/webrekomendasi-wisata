@extends('layouts.app')
@section('title', 'Proses Rekomendasi')
@section('content')
<div class="container py-5"><div class="row justify-content-center"><div class="col-lg-7"><div class="card shadow-sm text-center"><div class="card-body p-5"><h1 class="h3">Preferensi Anda Sudah Tersimpan</h1><p class="text-muted">Klik tombol di bawah untuk menghitung rekomendasi wisata berdasarkan kemiripan preferensi dengan pengunjung lain.</p><form method="POST" action="{{ route('wisatawan.rekomendasi.proses') }}" class="d-inline">@csrf<button class="btn btn-primary btn-lg">Proses Rekomendasi</button></form><form method="POST" action="{{ route('wisatawan.rekomendasi.reset') }}" class="d-inline" onsubmit="return confirm('Hapus preferensi dan isi ulang survei?')">@csrf @method('DELETE')<button class="btn btn-outline-secondary btn-lg">Isi Ulang Survei</button></form></div></div></div></div></div>
@endsection
