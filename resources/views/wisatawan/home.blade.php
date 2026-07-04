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
        isolation: isolate;
        background: linear-gradient(135deg, #082f49 0%, #0f766e 100%);
        box-shadow: 0 28px 70px rgba(8, 47, 73, .2);
    }

    .hero-modern::before {
        content: '';
        position: absolute;
        inset: 0;
        z-index: 1;
        pointer-events: none;
        background:
            linear-gradient(
                90deg,
                rgba(4, 27, 46, .96) 0%,
                rgba(4, 52, 72, .83) 45%,
                rgba(4, 52, 72, .45) 100%
            ),
            radial-gradient(circle at 78% 18%, rgba(255, 255, 255, .23), transparent 31%),
            radial-gradient(circle at 88% 88%, rgba(245, 158, 11, .22), transparent 28%);
    }

    .hero-bg-stack,
    .hero-bg-item {
        position: absolute;
        inset: 0;
    }

    .hero-bg-stack {
        z-index: 0;
        background: linear-gradient(135deg, #082f49 0%, #0f766e 100%);
    }

    .hero-bg-item {
        background-size: cover;
        background-position: center;
        opacity: 0;
        transform: scale(1.08);
        filter: saturate(1.08) contrast(1.04);
        animation: heroImageFade var(--hero-duration, 30s) ease-in-out infinite;
        animation-delay: var(--hero-delay, 0s);
    }

    .hero-bg-item:first-child {
        opacity: .42;
    }

    .hero-bg-item.is-single {
        opacity: .42;
        animation: none;
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
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, .72);
        border-radius: 30px;
        background:
            linear-gradient(135deg, rgba(255, 255, 255, .96), rgba(240, 249, 255, .92));
        box-shadow: 0 24px 60px rgba(15, 23, 42, .13);
        backdrop-filter: blur(14px);
    }

    .stat-item {
        position: relative;
        overflow: hidden;
        padding: 1.45rem 1.6rem;
        isolation: isolate;
        transition: transform .22s ease, background .22s ease;
        animation: statLift .65s ease both;
        animation-delay: var(--stat-delay, 0ms);
    }

    .stat-item::before {
        content: '';
        position: absolute;
        inset: 10px;
        z-index: -1;
        border-radius: 22px;
        background: rgba(255, 255, 255, .58);
        opacity: 0;
        transform: scale(.96);
        transition: .22s ease;
    }

    .stat-item::after {
        content: '';
        position: absolute;
        top: -50%;
        left: -30%;
        width: 70%;
        height: 200%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, .7), transparent);
        transform: rotate(18deg) translateX(-120%);
        animation: statShine 4.8s ease-in-out infinite;
        animation-delay: var(--stat-delay, 0ms);
        pointer-events: none;
    }

    .stat-item:hover {
        transform: translateY(-4px);
    }

    .stat-item:hover::before {
        opacity: 1;
        transform: scale(1);
    }

    .stat-icon {
        width: 54px;
        height: 54px;
        display: grid;
        place-items: center;
        border-radius: 18px;
        color: #0369a1;
        background: linear-gradient(135deg, #e0f2fe, #bae6fd);
        box-shadow: 0 12px 28px rgba(3, 105, 161, .14);
    }

    .stat-value {
        color: #0f172a;
        font-size: clamp(1.65rem, 3vw, 2.15rem);
        font-weight: 900;
        line-height: 1;
        letter-spacing: -.035em;
    }

    .stat-label {
        display: block;
        margin-top: .25rem;
        color: #64748b;
        font-size: .82rem;
        font-weight: 700;
    }

    .stat-chip {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        margin-top: .45rem;
        padding: .28rem .55rem;
        border-radius: 999px;
        color: #075985;
        background: #e0f2fe;
        font-size: .66rem;
        font-weight: 850;
    }

    @keyframes statLift {
        from {
            opacity: 0;
            transform: translateY(14px);
        }

        to {
            opacity: 1;
            transform: none;
        }
    }

    @keyframes statShine {
        0%,
        45% {
            transform: rotate(18deg) translateX(-130%);
        }

        70%,
        100% {
            transform: rotate(18deg) translateX(230%);
        }
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

    .home-budget-badge {
        position: absolute;
        right: 14px;
        bottom: 14px;
        z-index: 5;
        display: inline-flex;
        align-items: center;
        gap: .38rem;
        padding: .52rem .75rem;
        border-radius: 999px;
        color: #78350f;
        background: #fbbf24;
        font-size: .72rem;
        font-weight: 850;
        box-shadow: 0 12px 28px rgba(15, 23, 42, .18);
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

    .south-profile {
        border: 1px solid #dfe7ef;
        border-radius: 30px;
        overflow: hidden;
        background: #ffffff;
    }

    .south-profile-copy {
        padding: clamp(1.5rem, 4vw, 2.7rem);
    }

    .south-profile-title {
        font-size: clamp(1.7rem, 3.2vw, 3rem);
        line-height: 1.08;
        letter-spacing: -.045em;
        font-weight: 800;
        color: #0f172a;
    }

    .south-profile-list {
        display: grid;
        gap: .75rem;
        margin-top: 1.25rem;
    }

    .south-profile-list span {
        display: flex;
        align-items: flex-start;
        gap: .65rem;
        color: #475569;
        line-height: 1.65;
    }

    .south-profile-list i {
        margin-top: .22rem;
        color: #0284c7;
    }

    .video-frame {
        position: relative;
        width: 100%;
        min-height: 100%;
        aspect-ratio: 16 / 9;
        background: #082f49;
    }

    .video-frame iframe {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        border: 0;
    }

    @keyframes heroImageFade {
        0%,
        11% {
            opacity: .42;
            transform: scale(1.02);
        }

        20%,
        100% {
            opacity: 0;
            transform: scale(1.08);
        }

        7%,
        14% {
            opacity: .42;
        }
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
    <section class="hero-modern" style="--hero-duration: {{ max(20, $heroImages->count() * 5) }}s">
        @if ($heroImages->isNotEmpty())
            <div class="hero-bg-stack" aria-hidden="true">
                @foreach ($heroImages as $index => $image)
                    <span
                        class="hero-bg-item {{ $heroImages->count() === 1 ? 'is-single' : '' }}"
                        style="background-image: url('{{ $image }}'); --hero-delay: {{ $index * 5 }}s;"
                    ></span>
                @endforeach
            </div>
        @endif

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
                <a class="btn btn-warning btn-modern btn-lg" href="{{ route('wisatawan.rekomendasi.index') }}">
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
                <div class="col-md-4 stat-item d-flex align-items-center gap-3 border-end-md" style="--stat-delay: 40ms">
                    <div class="stat-icon">
                        <i class="bi bi-person-check-fill fs-5"></i>
                    </div>

                    <div>
                        <div class="stat-value js-count-up" data-count="{{ $totalPengunjungHariIni }}">0</div>
                        <span class="stat-label">Pengunjung hari ini</span>
                        <span class="stat-chip"><i class="bi bi-activity"></i> Live session</span>
                    </div>
                </div>

                <div class="col-md-4 stat-item d-flex align-items-center gap-3" style="--stat-delay: 140ms">
                    <div class="stat-icon">
                        <i class="bi bi-people-fill fs-5"></i>
                    </div>

                    <div>
                        <div class="stat-value js-count-up" data-count="{{ $totalPengunjung }}">0</div>
                        <span class="stat-label">Total pengunjung</span>
                        <span class="stat-chip"><i class="bi bi-bar-chart-fill"></i> Semua waktu</span>
                    </div>
                </div>

                <div class="col-md-4 stat-item d-flex align-items-center gap-3" style="--stat-delay: 240ms">
                    <div class="stat-icon">
                        <i class="bi bi-map-fill fs-5"></i>
                    </div>

                    <div>
                        <div class="stat-value js-count-up" data-count="{{ $totalWisata }}">0</div>
                        <span class="stat-label">Destinasi wisata</span>
                        <span class="stat-chip"><i class="bi bi-stars"></i> Data aktif</span>
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
            @php
                $lowestHotelPrice = $item->hotels->min(fn ($hotel) => (float) $hotel->harga_min);
                $packageStart = $lowestHotelPrice !== null ? (float) $item->total_estimasi_biaya + $lowestHotelPrice : null;
            @endphp
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

                        <span class="home-budget-badge">
                            <i class="bi bi-wallet2"></i>
                            @if ($packageStart !== null)
                                Paket Rp {{ number_format($packageStart, 0, ',', '.') }}
                            @else
                                Rp {{ number_format($item->total_estimasi_biaya, 0, ',', '.') }}
                            @endif
                        </span>
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
                            <div>
                                <strong class="text-primary d-block">
                                    Rp {{ number_format($item->total_estimasi_biaya, 0, ',', '.') }}
                                </strong>

                                @if ($packageStart !== null)
                                    <small class="text-muted">Paket mulai Rp {{ number_format($packageStart, 0, ',', '.') }}</small>
                                @endif
                            </div>

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
    <div class="south-profile">
        <div class="row g-0 align-items-stretch">
            <div class="col-lg-6">
                <div class="south-profile-copy h-100">
                    <div class="section-eyebrow mb-2">City of Makassar</div>

                    <h2 class="south-profile-title mb-3">
                        Sejarah singkat Kota Makassar.
                    </h2>

                    <p class="text-secondary">
                        Makassar adalah ibu kota Provinsi Sulawesi Selatan. Sebelum tahun 1999, kota ini lebih dikenal
                        dengan nama Ujung Pandang. Nama Makassar telah disebut dalam Kitab Negarakertagama karya
                        Mpu Prapanca pada abad ke-14 sebagai salah satu daerah taklukan Kerajaan Majapahit.
                    </p>

                    <p class="text-secondary mb-0">
                        Awal kota dan bandar Makassar berkembang di muara Sungai Tallo pada penghujung abad XV.
                        Pada pertengahan abad XVI, Kerajaan Tallo bersatu dengan Gowa dan mulai menjadi kekuatan
                        penting di kawasan pesisir. Perkembangan perdagangan kemudian mendorong perpindahan pusat
                        bandar ke muara Sungai Jeneberang, disertai pembangunan kawasan istana dan pertahanan
                        Benteng Somba Opu. Pada masa Raja Gowa XVI, Benteng Rotterdam berdiri dan aktivitas
                        perdagangan lokal, regional, hingga internasional semakin meningkat.
                    </p>

                    <a
                        class="btn btn-outline-primary btn-modern mt-4"
                        href="https://makassarkota.go.id/sejarah-kota-makassar/"
                        target="_blank"
                        rel="noopener"
                    >
                        Baca sejarah lengkap <i class="bi bi-arrow-up-right ms-1"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="video-frame h-100">
                    <iframe
                        src="https://www.youtube.com/embed/IWaUQiI1Q00?autoplay=1&mute=1&loop=1&playlist=IWaUQiI1Q00&controls=1&rel=0&modestbranding=1"
                        title="Video Pariwisata Makassar"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen
                    ></iframe>
                </div>
            </div>
        </div>
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
                            Temukan Wisata Makassar yang Paling Cocok untuk Anda
                        </h2>

                        <p class="text-muted">
                            Sistem akan memberikan rekomendasi berdasarkan minat, budget, hotel, dan lokasi Anda.
                        </p>

                        <div class="d-grid gap-2 my-4">
                            <div>
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Berdasarkan rating dan minat wisata
                            </div>

                            <div>
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Menyesuaikan rentang budget
                            </div>

                            <div>
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Bisa menghitung kebutuhan hotel
                            </div>

                            <div>
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Mempertimbangkan jarak dari lokasi Anda
                            </div>
                        </div>

                        <a class="btn btn-warning btn-modern w-100" href="{{ route('wisatawan.rekomendasi.index') }}">
                            Mulai Rekomendasi <i class="bi bi-arrow-right ms-1"></i>
                        </a>

                        <button class="btn btn-link text-muted w-100 mt-2" data-bs-dismiss="modal" id="dismissRecommendationIntro">
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
        document.querySelectorAll('.js-count-up').forEach((counter) => {
            const target = Number(counter.dataset.count || 0);
            const duration = 900;
            const startTime = performance.now();
            const formatter = new Intl.NumberFormat('id-ID');

            const tick = (currentTime) => {
                const progress = Math.min((currentTime - startTime) / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                counter.textContent = formatter.format(Math.round(target * eased));

                if (progress < 1) {
                    requestAnimationFrame(tick);
                }
            };

            requestAnimationFrame(tick);
        });

        const introKey = 'recommendationIntroDismissed';
        const dismissIntro = document.getElementById('dismissRecommendationIntro');

        if (dismissIntro) {
            dismissIntro.addEventListener('click', () => sessionStorage.setItem(introKey, '1'));
        }

        if (! sessionStorage.getItem(introKey)) {
            setTimeout(() => {
                const recommendationModal = document.getElementById('recommendationModal');

                if (recommendationModal) {
                    const modal = new bootstrap.Modal(recommendationModal);
                    modal.show();
                    sessionStorage.setItem(introKey, '1');
                }
            }, 1200);
        }
    });
</script>
@endpush
