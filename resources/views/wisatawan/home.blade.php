@extends('layouts.app')

@section('title', 'Jelajah Wisata Makassar')

@push('styles')
<style>
    .home-shell {
        padding: 18px 16px 0;
    }

    .hero-modern {
        position: relative;
        min-height: 620px;
        border-radius: 34px;
        overflow: hidden;
        background:
            linear-gradient(
                90deg,
                rgba(4, 27, 46, .94) 0%,
                rgba(4, 52, 72, .75) 54%,
                rgba(4, 52, 72, .18) 100%
            ),
            url('https://images.unsplash.com/photo-1518509562904-e7ef99cdcc86?auto=format&fit=crop&w=2000&q=88') center/cover;
        box-shadow: 0 28px 70px rgba(8, 47, 73, .2);
    }

.hero-content {
    position: relative;
    z-index: 2;
    padding: 95px 36px 130px;
}

    .hero-kicker {
        display: inline-flex;
        align-items: center;
        gap: .55rem;
        border: 1px solid rgba(255, 255, 255, .2);
        border-radius: 999px;
        padding: .55rem .9rem;
        color: #cffafe;
        background: rgba(255, 255, 255, .1);
        backdrop-filter: blur(10px);
        font-weight: 700;
        font-size: .82rem;
    }

    .hero-title {
        max-width: 780px;
        color: #fff;
        font-size: clamp(2.7rem, 6vw, 5.4rem);
        line-height: 1.02;
        letter-spacing: -.055em;
        font-weight: 800;
    }

    .hero-title span {
        color: #fbbf24;
    }

    .hero-copy {
        max-width: 630px;
        color: rgba(255, 255, 255, .76);
        font-size: 1.1rem;
        line-height: 1.8;
    }

    .hero-proof {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        color: rgba(255, 255, 255, .8);
        font-size: .86rem;
        font-weight: 600;
    }

    .hero-orbit {
        position: absolute;
        right: 7%;
        bottom: 10%;
        width: 240px;
        padding: 1rem;
        border: 1px solid rgba(255, 255, 255, .2);
        border-radius: 24px;
        color: #fff;
        background: rgba(6, 33, 50, .55);
        backdrop-filter: blur(16px);
        animation: floatCta 5s ease-in-out infinite;
    }

    .stat-wrap {
        position: relative;
        z-index: 3;
        margin-top: -64px;
    }

    .stat-panel {
        border: 1px solid rgba(255, 255, 255, .7);
        border-radius: 26px;
        background: rgba(255, 255, 255, .9);
        box-shadow: 0 22px 55px rgba(15, 23, 42, .12);
        backdrop-filter: blur(14px);
    }

    .stat-item {
        padding: 1.5rem;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        display: grid;
        place-items: center;
        border-radius: 16px;
        color: #0369a1;
        background: #e0f2fe;
    }

    .section-eyebrow {
        color: #0284c7;
        font-size: .78rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .14em;
    }

    .destination-media {
        position: relative;
    }

    .category-pill {
        color: #0e7490;
        background: #ecfeff;
        border: 1px solid #cffafe;
    }

    .recommend-banner {
        border-radius: 30px;
        overflow: hidden;
        color: #fff;
        background: linear-gradient(110deg, #082f49, #0369a1 56%, #0f766e);
        box-shadow: 0 24px 60px rgba(3, 105, 161, .2);
    }

  @media (max-width: 991.98px) {
    .hero-modern {
        min-height: auto;
        border-radius: 28px;
    }

    .hero-content {
        padding: 70px 28px 115px;
    }

    .hero-orbit {
        display: none;
    }

    .home-shell {
        padding: 10px 8px 0;
    }
}

@media (max-width: 575.98px) {
    .home-shell {
        padding: 8px 8px 0;
    }

    .hero-modern {
        border-radius: 24px;
        min-height: 465px;
        background-position: center;
    }

    .hero-content {
        padding: 58px 22px 88px;
    }

    .hero-kicker {
        font-size: .72rem;
        padding: .48rem .72rem;
        margin-bottom: 1rem !important;
    }

    .hero-title {
        font-size: clamp(2.05rem, 10vw, 2.7rem);
        line-height: 1.03;
        letter-spacing: -.045em;
        margin-bottom: 1.1rem !important;
        max-width: 310px;
    }

    .hero-content .d-flex.flex-wrap.gap-3 {
        flex-direction: column;
        align-items: flex-start;
        gap: .75rem !important;
    }

    .hero-content .btn {
        width: fit-content;
        max-width: 100%;
        padding: .8rem 1.05rem;
        font-size: .95rem;
        border-radius: 14px;
    }

    .stat-wrap {
        margin-top: -46px;
        padding-left: 14px;
        padding-right: 14px;
    }

    .stat-panel {
        border-radius: 22px;
    }

    .stat-item {
        padding: 1rem 1.1rem;
    }

    }
</style>
@endpush

@section('content')
<div class="home-shell">
    <section class="hero-modern">
        <div class="container hero-content">
            <div class="hero-kicker mb-4">
                <i class="bi bi-geo-alt-fill"></i>
                Eksplorasi Kota Daeng
            </div>

            <h1 class="hero-title mb-4">
                Makassar punya cerita.<br>
                <span>Temukan versimu.</span>
            </h1>



            <div class="d-flex flex-wrap gap-3 mb-4">
                <a class="btn btn-warning btn-modern btn-lg" href="{{ route('wisatawan.survey.index') }}">
                    <i class="bi bi-stars me-2"></i>Cari Rekomendasiku
                </a>

                <a class="btn btn-outline-light btn-modern btn-lg" href="{{ route('wisatawan.wisata.index') }}">
                    <i class="bi bi-compass me-2"></i>Jelajahi Wisata
                </a>
            </div>

          
        </div>

    
    </section>

    <div class="container stat-wrap reveal">
        <div class="stat-panel">
            <div class="row g-0">
                <div class="col-md-4 stat-item d-flex align-items-center gap-3 border-end-md">
                    <div class="stat-icon">
                        <i class="bi bi-map-fill fs-5"></i>
                    </div>

                    <div>
                        <div class="h3 fw-bold mb-0">{{ $totalWisata }}+</div>
                        <small class="text-muted">Destinasi aktif</small>
                    </div>
                </div>

                <div class="col-md-4 stat-item d-flex align-items-center gap-3">
                    <div class="stat-icon">
                        <i class="bi bi-grid-fill fs-5"></i>
                    </div>

                    <div>
                        <div class="h3 fw-bold mb-0">{{ $totalKategori }}</div>
                        <small class="text-muted">Kategori pilihan</small>
                    </div>
                </div>

                <div class="col-md-4 stat-item d-flex align-items-center gap-3">
                    <div class="stat-icon">
                        <i class="bi bi-lightning-charge-fill fs-5"></i>
                    </div>

                    <div>
                        <div class="h3 fw-bold mb-0">5</div>
                        <small class="text-muted">Rekomendasi personal</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="container py-5 mt-4">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4 reveal">
        <div>
            <div class="section-eyebrow mb-2">Pilihan teratas</div>

            <h2 class="display-6 fw-bold mb-1">
                Destinasi favorit pengunjung
            </h2>


        </div>

        <a class="btn btn-outline-primary btn-modern" href="{{ route('wisatawan.wisata.index') }}">
            Lihat semua <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>

    <div class="row g-4">
        @foreach ($wisata as $index => $item)
            <div class="col-md-6 col-xl-4 reveal" style="transition-delay: {{ ($index % 3) * 90 }}ms">
                <article class="card modern-card h-100">
                    <div class="destination-media overflow-hidden">
                        @if ($item->foto_url)
                            <img
                                class="destination-img image-zoom"
                                src="{{ $item->foto_url }}"
                                alt="{{ $item->nama_wisata }}"
                                loading="lazy"
                            >
                        @else
                            <div class="destination-img bg-secondary-subtle d-flex flex-column align-items-center justify-content-center text-muted">
                                <i class="bi bi-image fs-2 mb-2"></i>
                                <small>Foto belum tersedia</small>
                            </div>
                        @endif

                        <x-rating-badge :wisata="$item" />
                    </div>

                    <div class="card-body p-4 d-flex flex-column">
                        <span class="badge category-pill align-self-start rounded-pill px-3 py-2 mb-3">
                            <i class="bi bi-tag-fill me-1"></i>
                            {{ $item->kategoriWisata->nama_kategori }}
                        </span>

                        <h3 class="h5 fw-bold">
                            {{ $item->nama_wisata }}
                        </h3>

                        <p class="text-muted small mb-3">
                            <i class="bi bi-geo-alt me-1"></i>
                            {{ Str::limit($item->alamat, 78) }}
                        </p>

                        <p class="text-secondary">
                            {{ Str::limit($item->deskripsi, 105) }}
                        </p>

                        <div class="mt-auto d-flex justify-content-between align-items-center gap-3">
                            <strong class="text-primary">
                                Rp {{ number_format($item->total_estimasi_biaya, 0, ',', '.') }}
                            </strong>

                            <a
                                class="btn btn-light rounded-pill px-3 fw-semibold"
                                href="{{ route('wisatawan.wisata.show', $item->slug) }}"
                            >
                                Detail <i class="bi bi-arrow-up-right"></i>
                            </a>
                        </div>
                    </div>
                </article>
            </div>
        @endforeach
    </div>
</section>

<section class="container pb-5 reveal">
    <div class="recommend-banner p-4 p-lg-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <div class="section-eyebrow text-warning mb-2">
                    Bingung memilih?
                </div>

                <h2 class="display-6 fw-bold">
                    Biarkan sistem mencarikan yang cocok.
                </h2>

                <p class="text-white-50 mb-0">
                    Nilai 10 destinasi, lalu dapatkan rekomendasi berdasarkan kemiripan preferensimu
                    dengan pengunjung lain.
                </p>
            </div>

            <div class="col-lg-4 text-lg-end">
                <button
                    class="btn btn-warning btn-modern btn-lg"
                    data-bs-toggle="modal"
                    data-bs-target="#recommendationModal"
                >
                    <i class="bi bi-magic me-2"></i>Mulai Sekarang
                </button>
            </div>
        </div>
    </div>
</section>

<div
    class="modal fade recommendation-modal"
    id="recommendationModal"
    tabindex="-1"
    aria-labelledby="recommendationModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="row g-0">
                <div class="col-md-5 modal-visual d-flex align-items-end p-4">
                    <div class="text-white">
                        <span class="badge rounded-pill text-bg-warning mb-2">
                            Gratis & tanpa login
                        </span>

                        <h3 class="h4 fw-bold">
                            Wisata yang tepat menunggumu.
                        </h3>
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="p-4 p-lg-5">
                        <button type="button" class="btn-close float-end" data-bs-dismiss="modal"></button>

                        <div class="section-eyebrow mb-2">
                            <i class="bi bi-stars"></i>
                            Rekomendasi personal
                        </div>

                        <h2 class="h3 fw-bold" id="recommendationModalLabel">
                            Masih bingung mau ke mana?
                        </h2>

                        <p class="text-muted">
                            Cukup beri nilai minat pada 10 destinasi. Sistem akan menyiapkan
                            5 rekomendasi khusus untukmu.
                        </p>

                        <div class="d-grid gap-2 my-4">
                            <div>
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Proses kurang dari 2 menit
                            </div>

                            <div>
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Tanpa membuat akun
                            </div>

                            <div>
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Berdasarkan preferensi nyata
                            </div>
                        </div>

                        <a class="btn btn-warning btn-modern w-100" href="{{ route('wisatawan.survey.index') }}">
                            Temukan Wisataku <i class="bi bi-arrow-right ms-1"></i>
                        </a>

                        <button class="btn btn-link text-muted w-100 mt-2" data-bs-dismiss="modal">
                            Nanti saja
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (! sessionStorage.getItem('recommendationTeaserSeen')) {
            setTimeout(() => {
                const recommendationModal = document.getElementById('recommendationModal');

                if (recommendationModal) {
                    const modal = new bootstrap.Modal(recommendationModal);
                    modal.show();
                    sessionStorage.setItem('recommendationTeaserSeen', '1');
                }
            }, 1200);
        }
    });
</script>
@endpush