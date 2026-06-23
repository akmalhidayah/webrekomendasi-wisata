@extends('layouts.app')

@section('title', 'Hasil Rekomendasi Wisata')

@push('styles')
<style>
    .result-page {
        padding-top: 2.2rem;
        padding-bottom: 4rem;
    }

    .result-hero {
        padding: 1.6rem;
        border: 1px solid #dfe7ef;
        border-radius: 24px;
        background: #ffffff;
        box-shadow: 0 18px 45px rgba(15, 23, 42, .06);
    }

    .result-kicker {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        padding: .45rem .75rem;
        border-radius: 999px;
        color: #0369a1;
        background: #e0f2fe;
        font-size: .75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .08em;
    }

    .result-title {
        margin-top: .9rem;
        margin-bottom: .4rem;
        color: #0f172a;
        font-size: clamp(1.8rem, 4vw, 3rem);
        line-height: 1.08;
        letter-spacing: -.045em;
        font-weight: 800;
    }

    .result-subtitle {
        color: #64748b;
        line-height: 1.7;
    }

    .guest-code {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        padding: .55rem .75rem;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #f8fafc;
        color: #334155;
        font-size: .84rem;
        font-weight: 700;
    }

    .guest-code code {
        color: #0369a1;
        font-weight: 800;
    }

    .result-actions {
        display: flex;
        flex-wrap: wrap;
        gap: .6rem;
    }

    .result-actions .btn {
        min-height: 42px;
        border-radius: 12px;
        font-size: .82rem;
        font-weight: 700;
    }

    .info-card {
        display: flex;
        gap: .8rem;
        padding: 1rem;
        border: 1px solid #dbeafe;
        border-radius: 16px;
        background: #eff6ff;
        color: #1e3a8a;
    }

    .warning-card {
        display: flex;
        gap: .8rem;
        padding: 1rem;
        border: 1px solid #fde68a;
        border-radius: 16px;
        background: #fffbeb;
        color: #92400e;
    }

    .info-card i,
    .warning-card i {
        margin-top: .15rem;
        font-size: 1.1rem;
    }

    .recommend-card {
        overflow: hidden;
        height: 100%;
        border: 1px solid #dfe7ef;
        border-radius: 22px;
        background: #ffffff;
        box-shadow: 0 14px 34px rgba(15, 23, 42, .06);
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    }

    .recommend-card:hover {
        transform: translateY(-4px);
        border-color: #bae6fd;
        box-shadow: 0 22px 48px rgba(15, 23, 42, .09);
    }

    .recommend-media {
        position: relative;
        overflow: hidden;
        background: #e2e8f0;
    }

    .recommend-img {
        width: 100%;
        height: 235px;
        object-fit: cover;
        display: block;
        transition: transform .35s ease;
    }

    .recommend-card:hover .recommend-img {
        transform: scale(1.04);
    }

    .rank-badge {
        position: absolute;
        top: 14px;
        left: 14px;
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        padding: .55rem .75rem;
        border-radius: 999px;
        background: #0f172a;
        color: #ffffff;
        font-size: .78rem;
        font-weight: 800;
        z-index: 6;
        box-shadow: 0 10px 24px rgba(15, 23, 42, .2);
    }

    .recommend-body {
        padding: 1.35rem;
        display: flex;
        flex-direction: column;
        height: calc(100% - 235px);
    }

    .category-chip {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        width: fit-content;
        padding: .4rem .7rem;
        border: 1px solid #cffafe;
        border-radius: 999px;
        color: #0e7490;
        background: #ecfeff;
        font-size: .72rem;
        font-weight: 800;
    }

    .recommend-name {
        margin-top: .85rem;
        margin-bottom: .3rem;
        color: #0f172a;
        font-size: 1.15rem;
        font-weight: 800;
        line-height: 1.25;
    }

    .recommend-type {
        color: #64748b;
        font-size: .82rem;
        font-weight: 700;
    }

    .recommend-address {
        margin-top: .7rem;
        color: #64748b;
        font-size: .87rem;
        line-height: 1.55;
    }

    .cost-line {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        margin-top: .4rem;
        color: #0369a1;
        font-weight: 800;
    }

    .score-box {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .6rem;
        margin: 1rem 0;
    }

    .score-item {
        padding: .8rem;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #f8fafc;
    }

    .score-item small {
        display: block;
        color: #64748b;
        font-size: .68rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .06em;
        margin-bottom: .25rem;
    }

    .score-item strong {
        color: #0f172a;
        font-size: .95rem;
    }

    .recommend-actions {
        display: flex;
        flex-wrap: wrap;
        gap: .5rem;
        margin-top: auto;
    }

    .recommend-actions .btn {
        border-radius: 11px;
        font-size: .78rem;
        font-weight: 700;
    }

    .empty-state {
        padding: 3rem 1.5rem;
        border: 1px solid #dfe7ef;
        border-radius: 22px;
        background: #ffffff;
        text-align: center;
        box-shadow: 0 14px 34px rgba(15, 23, 42, .05);
    }

    .empty-icon {
        width: 72px;
        height: 72px;
        display: grid;
        place-items: center;
        margin: 0 auto 1rem;
        border-radius: 24px;
        color: #0369a1;
        background: #e0f2fe;
        font-size: 2rem;
    }

    .fade-up-soft {
        animation: fadeUpSoft .45s ease both;
    }

    @keyframes fadeUpSoft {
        from {
            opacity: 0;
            transform: translateY(12px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 575.98px) {
        .result-page {
            padding-top: 1.2rem;
        }

        .result-hero {
            padding: 1.2rem;
            border-radius: 18px;
        }

        .result-actions .btn,
        .recommend-actions .btn {
            width: 100%;
        }

        .recommend-img {
            height: 210px;
        }

        .score-box {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="container result-page">
    <div class="result-hero mb-4 fade-up-soft">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <div class="result-kicker">
                    <i class="bi bi-stars"></i>
                    Rekomendasi Personal
                </div>

                <h1 class="result-title">
                    Hasil Rekomendasi Wisata
                </h1>

                <p class="result-subtitle mb-3">
                    Rekomendasi ini dihitung dari skor utama yang menggabungkan pola pilihan pengunjung lain
                    dan rating destinasi, lalu disesuaikan dengan jenis wisata yang Anda sukai.
                </p>

                <div class="guest-code">
                    <i class="bi bi-person-badge"></i>
                    Kode guest:
                    <code>{{ $guest->kode_guest }}</code>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="result-actions justify-content-lg-end">
                    <form method="POST" action="{{ route('wisatawan.rekomendasi.reset') }}" class="form-reset-rekomendasi">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-repeat me-1"></i>
                            Isi Ulang Survei
                        </button>
                    </form>

                    <a class="btn btn-outline-secondary" href="{{ route('wisatawan.wisata.index') }}">
                        <i class="bi bi-grid me-1"></i>
                        Daftar Wisata
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-3 fade-up-soft" style="animation-delay: 70ms;">
        <div class="info-card">
            <i class="bi bi-info-circle-fill"></i>

            <div>
                <strong>Informasi Perhitungan</strong>
                <div class="small">
                    Sistem mencari pengunjung dengan pola pilihan yang mirip, menggabungkannya dengan rating
                    destinasi, lalu menyesuaikan hasilnya berdasarkan kategori dan jenis wisata yang paling cocok
                    dengan minat Anda. Jika skor cocok antar destinasi berdekatan, destinasi dengan rating lebih
                    tinggi ditampilkan lebih dulu.
                </div>
            </div>
        </div>
    </div>

    @if ($isFallback)
        <div class="mb-4 fade-up-soft" style="animation-delay: 120ms;">
            <div class="warning-card">
                <i class="bi bi-exclamation-triangle-fill"></i>

                <div>
                    <strong>Rekomendasi Awal</strong>
                    <div class="small">
                        Rekomendasi awal ditampilkan dari gabungan rating destinasi, minat kategori, dan data populer
                        karena pola pengunjung lain masih terbatas.
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($hasil->isEmpty())
        <div class="empty-state fade-up-soft">
            <div class="empty-icon">
                <i class="bi bi-magic"></i>
            </div>

            <h2 class="h4 fw-bold">
                Hasil rekomendasi belum tersedia
            </h2>

            <p class="text-muted mb-4">
                Klik tombol di bawah ini untuk memproses rekomendasi berdasarkan survei preferensi yang telah Anda isi.
            </p>

            <form method="POST" action="{{ route('wisatawan.rekomendasi.proses') }}">
                @csrf

                <button class="btn btn-primary btn-lg rounded-pill px-4">
                    <i class="bi bi-stars me-1"></i>
                    Proses Rekomendasi
                </button>
            </form>
        </div>
    @else
        <div class="row g-4">
            @foreach ($hasil as $index => $item)
                @if ($item->wisata)
                    <div class="col-md-6 col-xl-4 fade-up-soft" style="animation-delay: {{ ($index % 3) * 90 }}ms;">
                        <article class="recommend-card">
                            <div class="recommend-media">
                                @if ($item->wisata->foto_url)
                                    <img
                                        class="recommend-img"
                                        src="{{ $item->wisata->foto_url }}"
                                        alt="{{ $item->wisata->nama_wisata }}"
                                        loading="lazy"
                                    >
                                @else
                                    <div class="recommend-img bg-secondary-subtle d-flex flex-column align-items-center justify-content-center text-muted">
                                        <i class="bi bi-image fs-2 mb-2"></i>
                                        <small>Foto belum tersedia</small>
                                    </div>
                                @endif

                                <span class="rank-badge">
                                    <i class="bi bi-trophy-fill"></i>
                                    Ranking #{{ $item->ranking }}
                                </span>

                                <x-rating-badge :wisata="$item->wisata" />
                            </div>

                            <div class="recommend-body">
                                <span class="category-chip">
                                    <i class="bi bi-tag-fill"></i>
                                    {{ $item->wisata->kategoriWisata->nama_kategori }}
                                </span>

                                <h2 class="recommend-name">
                                    {{ $item->wisata->nama_wisata }}
                                </h2>

                                <div class="recommend-type">
                                    <i class="bi bi-compass me-1"></i>
                                    {{ $item->wisata->jenis_wisata }}
                                </div>

                                <p class="recommend-address">
                                    <i class="bi bi-geo-alt me-1"></i>
                                    {{ Str::limit($item->wisata->alamat, 90) }}
                                </p>

                                <div class="cost-line">
                                    <i class="bi bi-wallet2"></i>
                                    Estimasi Rp {{ number_format($item->wisata->total_estimasi_biaya, 0, ',', '.') }}
                                </div>

                                <div class="score-box">
                                    <div class="score-item">
                                        <small>Skor Cocok</small>
                                        <strong>
                                            {{ is_null($item->nilai_prediksi) ? '-' : number_format($item->nilai_prediksi, 2, ',', '.') }}/5
                                        </strong>
                                    </div>

                                    <div class="score-item">
                                        <small>Kemiripan</small>
                                        <strong>
                                            {{ is_null($item->nilai_similarity) ? '-' : number_format($item->nilai_similarity, 2, ',', '.') }}
                                        </strong>
                                    </div>
                                </div>

                                <div class="recommend-actions">
                                    <a
                                        class="btn btn-primary"
                                        href="{{ route('wisatawan.wisata.show', $item->wisata->slug) }}"
                                    >
                                        <i class="bi bi-eye me-1"></i>
                                        Detail
                                    </a>

                                    @if ($item->wisata->link_maps)
                                        <a
                                            class="btn btn-outline-secondary"
                                            href="{{ $item->wisata->link_maps }}"
                                            target="_blank"
                                            rel="noopener"
                                        >
                                            <i class="bi bi-map me-1"></i>
                                            Maps
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </article>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const resetForms = document.querySelectorAll('.form-reset-rekomendasi');

        resetForms.forEach((form) => {
            form.addEventListener('submit', (event) => {
                event.preventDefault();

                if (typeof Swal === 'undefined') {
                    if (confirm('Hapus hasil rekomendasi dan isi ulang survei?')) {
                        form.submit();
                    }

                    return;
                }

                Swal.fire({
                    title: 'Isi ulang survei?',
                    text: 'Hasil rekomendasi dan data survei sebelumnya akan dihapus.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, isi ulang',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#0369a1',
                    cancelButtonColor: '#64748b'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush
