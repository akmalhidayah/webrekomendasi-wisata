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

                    <h1 class="h3 fw-bold">
                        Survei berhasil disimpan
                    </h1>

                    <p class="text-muted mb-4">
                        Rekomendasi wisata sedang disiapkan berdasarkan preferensi Anda.
                    </p>

                    <form id="processRecommendationForm" method="POST" action="{{ route('wisatawan.rekomendasi.proses') }}">
                        @csrf

                        <button class="btn btn-warning btn-lg rounded-4 fw-bold" type="submit">
                            <i class="bi bi-stars me-1"></i>
                            Lihat Hasil Rekomendasi
                        </button>
                    </form>

                    <a class="btn btn-link mt-2 text-decoration-none" href="{{ route('wisatawan.wisata.index') }}">
                        Lihat daftar wisata
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('processRecommendationForm');

        if (!form || typeof Swal === 'undefined') {
            return;
        }

        Swal.fire({
            title: 'Survei tersimpan',
            text: 'Sekarang sistem akan menyiapkan hasil rekomendasi wisata untuk Anda.',
            icon: 'success',
            confirmButtonText: 'Lihat Hasil Rekomendasi',
            timer: 2200,
            timerProgressBar: true,
            allowOutsideClick: false,
            allowEscapeKey: false,
            customClass: {
                popup: 'rounded-4',
                confirmButton: 'btn btn-warning rounded-4 fw-bold px-4',
            },
            buttonsStyling: false,
        }).then(() => {
            form.submit();
        });
    });
</script>
@endpush
