@extends('layouts.app')

@section('title', 'Survei Tersimpan')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 rounded-4 text-center">
                <div class="card-body p-5">
                    <div class="display-5 text-success mb-3">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>

                    <h1 class="h3 fw-bold">Survei berhasil disimpan</h1>

                    <p class="text-muted mb-4">
                        Rekomendasi diproses langsung setelah survei dikirim. Jika halaman hasil belum terbuka, klik tombol di bawah.
                    </p>

                    <a class="btn btn-warning btn-lg rounded-4 fw-bold" href="{{ route('wisatawan.rekomendasi.hasil') }}">
                        <i class="bi bi-stars me-1"></i>
                        Lihat Hasil Rekomendasi
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
