@extends('layouts.app')

@section('title', $wisata->nama_wisata)

@push('styles')
<style>
    .detail-page {
        padding-top: 2rem;
        padding-bottom: 3rem;
    }

    .detail-back {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        color: #475569;
        text-decoration: none;
        font-size: .85rem;
        font-weight: 700;
    }

    .detail-back:hover {
        color: #0369a1;
    }

    .detail-hero {
        overflow: hidden;
        border: 1px solid #dfe7ef;
        border-radius: 22px;
        background: #ffffff;
    }

    .detail-cover-wrap {
        position: relative;
        width: 100%;
        height: 100%;
        min-height: 520px;
        background: #e2e8f0;
    }

    .detail-cover {
        width: 100%;
        height: 100%;
        min-height: 520px;
        object-fit: cover;
        display: block;
    }

    .detail-cover-empty {
        width: 100%;
        min-height: 520px;
    }

    .detail-content {
        padding: clamp(1.5rem, 4vw, 2.6rem);
    }

    .category-label {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .45rem .75rem;
        border: 1px solid #bae6fd;
        border-radius: 999px;
        color: #0369a1;
        background: #f0f9ff;
        font-size: .72rem;
        font-weight: 800;
    }

    .detail-title {
        margin: .9rem 0 .4rem;
        font-size: clamp(2rem, 4vw, 3.2rem);
        line-height: 1.08;
        letter-spacing: -.05em;
        font-weight: 800;
        color: #0f172a;
    }

    .rating-summary {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: .6rem;
        margin-bottom: 1rem;
    }

    .rating-note {
        color: #64748b;
        font-size: .82rem;
        font-weight: 600;
    }

    .detail-description {
        color: #526174;
        line-height: 1.75;
    }

    .info-list {
        display: grid;
        gap: .7rem;
        margin: 1.5rem 0;
    }

    .info-item {
        display: grid;
        grid-template-columns: 42px 1fr;
        gap: .8rem;
        align-items: start;
        padding: .8rem 0;
        border-top: 1px solid #edf1f5;
    }

    .info-icon {
        width: 38px;
        height: 38px;
        display: grid;
        place-items: center;
        border-radius: 10px;
        color: #0369a1;
        background: #e0f2fe;
    }

    .info-item small {
        display: block;
        color: #64748b;
        font-size: .68rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .info-item strong {
        display: block;
        margin-top: .15rem;
        font-size: .86rem;
        color: #0f172a;
    }

    .detail-actions {
        display: flex;
        flex-wrap: wrap;
        gap: .65rem;
    }

    .detail-actions .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .45rem;
        min-height: 44px;
        border-radius: 11px;
        font-size: .78rem;
        font-weight: 700;
        box-shadow: none !important;
    }

    .detail-section {
        margin-top: 2rem;
        padding: 1.4rem;
        border: 1px solid #dfe7ef;
        border-radius: 16px;
        background: #ffffff;
    }

    .section-title {
        display: flex;
        align-items: center;
        gap: .65rem;
        margin-bottom: 1rem;
        font-size: 1.05rem;
        font-weight: 800;
        color: #0f172a;
    }

    .section-title i {
        color: #0369a1;
    }

    .rating-source-card {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: .8rem;
    }

    .rating-source-item {
        padding: .95rem;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #f8fafc;
    }

    .rating-source-item small {
        display: block;
        color: #64748b;
        font-size: .7rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .05em;
        margin-bottom: .3rem;
    }

    .rating-source-item strong {
        color: #0f172a;
        font-size: 1.05rem;
    }

    .facility-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .7rem;
    }

    .facility-item {
        display: flex;
        gap: .7rem;
        padding: .85rem;
        border: 1px solid #e5eaf0;
        border-radius: 11px;
        background: #ffffff;
    }

    .facility-item i {
        color: #0f766e;
    }

    .facility-item strong {
        display: block;
        font-size: .8rem;
        color: #0f172a;
    }

    .facility-item small {
        color: #64748b;
    }

    .hotel-section {
        position: relative;
        overflow: hidden;
        border-radius: 24px;
        background:
            linear-gradient(135deg, rgba(240, 249, 255, .95), rgba(255, 255, 255, .98));
    }

    .hotel-section-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .hotel-section-copy {
        color: #64748b;
        max-width: 720px;
        font-size: .9rem;
        line-height: 1.65;
    }

    .budget-corner {
        min-width: 190px;
        padding: .8rem .95rem;
        border: 1px solid #fde68a;
        border-radius: 18px;
        color: #78350f;
        background: #fffbeb;
    }

    .budget-corner small {
        display: block;
        font-size: .68rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .budget-corner strong {
        display: block;
        margin-top: .15rem;
        color: #0f172a;
        font-size: 1rem;
    }

    .hotel-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1rem;
    }

    .hotel-card-public {
        position: relative;
        overflow: hidden;
        min-height: 100%;
        border: 1px solid #dbe7f0;
        border-radius: 22px;
        background: #ffffff;
        box-shadow: 0 18px 42px rgba(15, 23, 42, .08);
    }

    .hotel-media-link {
        position: relative;
        display: block;
        overflow: hidden;
        height: 170px;
        color: inherit;
        text-decoration: none;
        background: #e2e8f0;
    }

    .hotel-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform .28s ease;
    }

    .hotel-card-public:hover .hotel-image {
        transform: scale(1.04);
    }

    .hotel-image-empty {
        height: 100%;
        display: grid;
        place-items: center;
        color: #0369a1;
        background:
            linear-gradient(135deg, #e0f2fe, #f8fafc);
        font-size: 2rem;
    }

    .hotel-rank-badge,
    .hotel-budget-badge {
        position: absolute;
        z-index: 2;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        border-radius: 999px;
        font-size: .7rem;
        font-weight: 800;
        box-shadow: 0 10px 22px rgba(15, 23, 42, .16);
    }

    .hotel-rank-badge {
        top: .75rem;
        left: .75rem;
        padding: .42rem .65rem;
        color: #ffffff;
        background: #0f172a;
    }

    .hotel-budget-badge {
        right: .75rem;
        bottom: .75rem;
        padding: .48rem .7rem;
        color: #78350f;
        background: #fbbf24;
    }

    .hotel-card-body {
        padding: 1rem;
    }

    .hotel-name {
        margin: 0 0 .35rem;
        color: #0f172a;
        font-size: 1rem;
        font-weight: 850;
        line-height: 1.25;
    }

    .hotel-meta {
        color: #64748b;
        font-size: .8rem;
        line-height: 1.55;
    }

    .hotel-price-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: .55rem;
        margin-top: .9rem;
    }

    .hotel-price-item {
        padding: .7rem;
        border: 1px solid #e5eaf0;
        border-radius: 14px;
        background: #f8fafc;
    }

    .hotel-price-item small {
        display: block;
        color: #64748b;
        font-size: .65rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .hotel-price-item strong {
        display: block;
        margin-top: .15rem;
        color: #0f172a;
        font-size: .84rem;
    }

    .hotel-actions {
        display: flex;
        gap: .5rem;
        flex-wrap: wrap;
        margin-top: 1rem;
    }

    .hotel-actions .btn {
        border-radius: 12px;
        font-size: .78rem;
        font-weight: 800;
    }

    .gallery-image {
        width: 100%;
        height: 160px;
        object-fit: cover;
        border: 1px solid #e2e8f0;
        border-radius: 11px;
    }

    .gallery-trigger {
        position: relative;
        display: block;
        width: 100%;
        padding: 0;
        border: 0;
        border-radius: 11px;
        background: transparent;
        overflow: hidden;
    }

    .gallery-trigger::after {
        content: '\F52A';
        position: absolute;
        right: .65rem;
        bottom: .65rem;
        width: 34px;
        height: 34px;
        display: grid;
        place-items: center;
        border-radius: 999px;
        color: #075985;
        background: rgba(255, 255, 255, .88);
        font-family: bootstrap-icons;
        font-size: 1rem;
    }

    #galleryModal .modal-content {
        border: 1px solid #dfe7ef;
        border-radius: 18px;
        box-shadow: none;
        overflow: hidden;
        background: #071f31;
    }

    .gallery-modal-image {
        width: 100%;
        max-height: 78vh;
        object-fit: contain;
        background: #071f31;
    }

    .review-card {
        height: 100%;
        padding: 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #ffffff;
    }

    .review-stars {
        color: #f59e0b;
    }

    .detail-empty {
        display: flex;
        align-items: center;
        gap: .6rem;
        color: #64748b;
        font-size: .84rem;
    }

    #ratingModal .modal-content {
        border: 1px solid #dfe7ef;
        border-radius: 16px;
        box-shadow: none;
    }

    @media (max-width: 991.98px) {
        .detail-cover-wrap,
        .detail-cover,
        .detail-cover-empty {
            min-height: 340px;
            max-height: 480px;
        }

        .detail-hero {
            border-radius: 17px;
        }
    }

    @media (max-width: 575.98px) {
        .detail-page {
            padding-top: 1.2rem;
        }

        .detail-cover-wrap,
        .detail-cover,
        .detail-cover-empty {
            min-height: 270px;
        }

        .detail-content {
            padding: 1.25rem;
        }

        .facility-grid {
            grid-template-columns: 1fr;
        }

        .rating-source-card {
            grid-template-columns: 1fr;
        }

        .hotel-section-head,
        .hotel-price-row {
            grid-template-columns: 1fr;
        }

        .hotel-section-head {
            display: block;
        }

        .budget-corner {
            margin-top: 1rem;
        }

        .hotel-grid {
            grid-template-columns: 1fr;
        }

        .detail-actions .btn {
            width: 100%;
        }
    }

    @media (max-width: 991.98px) {
        .hotel-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
@php
    $ulasanDisetujui = $wisata->ratingKunjungan->where('status', 'disetujui');
    $galleryPhotos = $wisata->fotoWisata->filter(fn ($foto) => $foto->foto_url)->values();
    $relatedHotels = $wisata->hotels->take(3)->values();
    $lowestHotelPrice = $relatedHotels->min(fn ($hotel) => (float) $hotel->harga_min);
    $mapsUrl = $wisata->maps_url ?: $wisata->link_maps;
@endphp

<div class="container detail-page">
    <a href="{{ route('wisatawan.wisata.index') }}" class="detail-back mb-3">
        <i class="bi bi-arrow-left"></i>
        Kembali ke daftar
    </a>

    <article class="detail-hero">
        <div class="row g-0">
            <div class="col-lg-6">
                <div class="detail-cover-wrap">
                    @if ($wisata->foto_url)
                        <img
                            class="detail-cover"
                            src="{{ $wisata->foto_url }}"
                            alt="{{ $wisata->nama_wisata }}"
                        >
                    @else
                        <div class="detail-cover-empty bg-secondary-subtle d-flex flex-column align-items-center justify-content-center text-muted">
                            <i class="bi bi-image fs-1 mb-2"></i>
                            <span>Foto belum tersedia</span>
                        </div>
                    @endif

                    <x-rating-badge :wisata="$wisata" />
                </div>
            </div>

            <div class="col-lg-6">
                <div class="detail-content">
                    <span class="category-label">
                        <i class="bi bi-tag-fill"></i>
                        {{ $wisata->kategoriWisata->nama_kategori }}
                    </span>

                    <h1 class="detail-title">
                        {{ $wisata->nama_wisata }}
                    </h1>

                    <div class="rating-summary">
                        <x-rating-badge :wisata="$wisata" position="detail" />
                    </div>

                    <p class="detail-description mb-0">
                        {{ $wisata->deskripsi }}
                    </p>

                    <div class="info-list">
                        <div class="info-item">
                            <span class="info-icon">
                                <i class="bi bi-geo-alt"></i>
                            </span>

                            <div>
                                <small>Alamat</small>
                                <strong>{{ $wisata->alamat }}</strong>
                            </div>
                        </div>

                        <div class="info-item">
                            <span class="info-icon">
                                <i class="bi bi-clock"></i>
                            </span>

                            <div>
                                <small>Jam Operasional</small>
                                <strong>{{ $wisata->jam_operasional ?: 'Belum tersedia' }}</strong>
                            </div>
                        </div>

                        <div class="info-item">
                            <span class="info-icon">
                                <i class="bi bi-wallet2"></i>
                            </span>

                            <div>
                                <small>Estimasi Biaya</small>
                                <strong>Rp {{ number_format($wisata->total_estimasi_biaya, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="detail-actions">
                        @if ($mapsUrl)
                            <a
                                class="btn btn-outline-secondary"
                                href="{{ $mapsUrl }}"
                                target="_blank"
                                rel="noopener"
                            >
                                <i class="bi bi-map"></i>
                                Buka Maps
                            </a>
                        @endif

                        <a class="btn btn-warning" href="{{ route('wisatawan.rekomendasi.index') }}">
                            <i class="bi bi-stars"></i>
                            Rekomendasi
                        </a>

                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ratingModal">
                            <i class="bi bi-chat-square-heart"></i>
                            Beri Rating
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </article>

    @if ($relatedHotels->isNotEmpty())
        <section class="detail-section hotel-section">
            <div class="hotel-section-head">
                <div>
                    <h2 class="section-title mb-2">
                        <i class="bi bi-buildings"></i>
                        Hotel Terkait
                    </h2>
                    <p class="hotel-section-copy mb-0">
                        Pilihan hotel yang dikaitkan admin dengan destinasi ini. Harga paket dihitung dari estimasi biaya wisata ditambah harga mulai hotel.
                    </p>
                </div>

                @if ($lowestHotelPrice !== null)
                    <div class="budget-corner">
                        <small>Kisaran paket mulai</small>
                        <strong>Rp {{ number_format((float) $wisata->total_estimasi_biaya + $lowestHotelPrice, 0, ',', '.') }}</strong>
                        <small>Wisata + hotel</small>
                    </div>
                @endif
            </div>

            <div class="hotel-grid">
                @foreach ($relatedHotels as $index => $hotel)
                    @php
                        $hotelPriceLabel = (float) $hotel->harga_max > (float) $hotel->harga_min
                            ? 'Rp '.number_format($hotel->harga_min, 0, ',', '.').' - Rp '.number_format($hotel->harga_max, 0, ',', '.')
                            : 'Mulai Rp '.number_format($hotel->harga_min, 0, ',', '.');
                        $packageStart = (float) $wisata->total_estimasi_biaya + (float) $hotel->harga_min;
                        $hotelTargetUrl = $hotel->traveloka_url ?: $hotel->maps_url;
                    @endphp

                    <article class="hotel-card-public">
                        <a
                            class="hotel-media-link"
                            href="{{ $hotelTargetUrl ?: '#' }}"
                            @if ($hotelTargetUrl) target="_blank" rel="noopener" @endif
                            aria-label="Buka {{ $hotel->nama_hotel }}"
                        >
                            @if ($hotel->gambar_url)
                                <img class="hotel-image" src="{{ $hotel->gambar_url }}" alt="{{ $hotel->nama_hotel }}" loading="lazy">
                            @else
                                <div class="hotel-image-empty">
                                    <i class="bi bi-building"></i>
                                </div>
                            @endif

                            <span class="hotel-rank-badge">
                                <i class="bi bi-geo-alt-fill"></i>
                                Hotel {{ $index + 1 }}
                            </span>

                            <span class="hotel-budget-badge">
                                <i class="bi bi-wallet2"></i>
                                Paket Rp {{ number_format($packageStart, 0, ',', '.') }}
                            </span>
                        </a>

                        <div class="hotel-card-body">
                            <h3 class="hotel-name">{{ $hotel->nama_hotel }}</h3>

                            <div class="hotel-meta">
                                <i class="bi bi-star-fill text-warning me-1"></i>
                                {{ $hotel->rating_hotel ? number_format((float) $hotel->rating_hotel, 1, ',', '.') : 'Rating belum tersedia' }}
                                @if ($hotel->alamat)
                                    <br><i class="bi bi-pin-map me-1"></i>{{ Str::limit($hotel->alamat, 72) }}
                                @endif
                            </div>

                            <div class="hotel-price-row">
                                <div class="hotel-price-item">
                                    <small>Harga hotel</small>
                                    <strong>{{ $hotelPriceLabel }}</strong>
                                </div>

                                <div class="hotel-price-item">
                                    <small>Wisata + hotel</small>
                                    <strong>Rp {{ number_format($packageStart, 0, ',', '.') }}</strong>
                                </div>
                            </div>

                            <div class="hotel-actions">
                                @if ($hotel->traveloka_url)
                                    <a class="btn btn-primary" href="{{ $hotel->traveloka_url }}" target="_blank" rel="noopener">
                                        <i class="bi bi-box-arrow-up-right me-1"></i>
                                        Traveloka
                                    </a>
                                @endif

                                @if ($hotel->maps_url)
                                    <a class="btn btn-outline-secondary" href="{{ $hotel->maps_url }}" target="_blank" rel="noopener">
                                        <i class="bi bi-map me-1"></i>
                                        Maps
                                    </a>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <div class="row g-4">
        <div class="col-lg-5">
            <section class="detail-section h-100">
                <h2 class="section-title">
                    <i class="bi bi-building-check"></i>
                    Fasilitas
                </h2>

                <div class="facility-grid">
                    @forelse ($wisata->fasilitasWisata as $item)
                        <div class="facility-item">
                            <i class="bi bi-check-circle-fill"></i>

                            <div>
                                <strong>{{ $item->nama_fasilitas }}</strong>

                                @if ($item->keterangan)
                                    <small>{{ $item->keterangan }}</small>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="detail-empty">
                            <i class="bi bi-info-circle"></i>
                            Belum ada informasi fasilitas.
                        </div>
                    @endforelse
                </div>
            </section>
        </div>

        <div class="col-lg-7">
            <section class="detail-section h-100">
                <h2 class="section-title">
                    <i class="bi bi-images"></i>
                    Galeri
                </h2>

                <div class="row g-2">
                    @forelse ($galleryPhotos as $index => $item)
                        <div class="col-6 col-md-4">
                            <button
                                class="gallery-trigger"
                                type="button"
                                data-bs-toggle="modal"
                                data-bs-target="#galleryModal"
                                data-gallery-index="{{ $index }}"
                                aria-label="Buka foto galeri {{ $index + 1 }}"
                            >
                                <img
                                    class="gallery-image"
                                    src="{{ $item->foto_url }}"
                                    alt="{{ $item->caption ?: $wisata->nama_wisata }}"
                                    loading="lazy"
                                >
                            </button>
                        </div>
                    @empty
                        <div class="col-12 detail-empty">
                            <i class="bi bi-image"></i>
                            Belum ada foto tambahan.
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    <section class="detail-section">
        <h2 class="section-title">
            <i class="bi bi-chat-quote"></i>
            Ulasan Pengunjung
        </h2>

        <div class="row g-3">
            @forelse ($ulasanDisetujui as $item)
                <div class="col-md-6">
                    <article class="review-card">
                        <div class="review-stars">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="bi {{ $i <= $item->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                            @endfor

                            <span class="text-muted small ms-1">
                                {{ $item->rating }}/5
                            </span>
                        </div>

                        @if ($item->ulasan)
                            <p class="mb-2 mt-2">
                                {{ $item->ulasan }}
                            </p>
                        @else
                            <p class="mb-2 mt-2 text-muted">
                                Pengunjung memberikan rating tanpa ulasan.
                            </p>
                        @endif

                        <small class="text-muted">
                            <i class="bi bi-calendar3 me-1"></i>
                            {{ $item->created_at->format('d-m-Y') }}
                        </small>
                    </article>
                </div>
            @empty
                <div class="col-12 detail-empty">
                    <i class="bi bi-chat"></i>
                    Belum ada ulasan.
                </div>
            @endforelse
        </div>
    </section>
</div>

@if ($galleryPhotos->isNotEmpty())
    <div class="modal fade" id="galleryModal" tabindex="-1" aria-labelledby="galleryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 text-white">
                    <h2 class="modal-title fs-6" id="galleryModalLabel">
                        Galeri {{ $wisata->nama_wisata }}
                    </h2>

                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div id="galleryCarousel" class="carousel slide" data-bs-ride="false">
                    <div class="carousel-inner">
                        @foreach ($galleryPhotos as $index => $item)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                <img
                                    class="gallery-modal-image"
                                    src="{{ $item->foto_url }}"
                                    alt="{{ $item->caption ?: $wisata->nama_wisata }}"
                                >
                            </div>
                        @endforeach
                    </div>

                    @if ($galleryPhotos->count() > 1)
                        <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Sebelumnya</span>
                        </button>

                        <button class="carousel-control-next" type="button" data-bs-target="#galleryCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Berikutnya</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif

<div class="modal fade" id="ratingModal" tabindex="-1" aria-labelledby="ratingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content">
            <form method="POST" action="{{ route('wisatawan.rating-kunjungan.store') }}">
                @csrf

                <input type="hidden" name="wisata_id" value="{{ $wisata->id }}">

                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="ratingModalLabel">
                        <i class="bi bi-star me-2"></i>
                        Rating Kunjungan
                    </h2>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="pernah_dikunjungi" value="0">

                    <div class="form-check mb-3">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="pernah_dikunjungi"
                            value="1"
                            id="visitedCheck"
                        >

                        <label class="form-check-label" for="visitedCheck">
                            Saya pernah mengunjungi destinasi ini
                        </label>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Rating</label>

                        <select class="form-select rating-field" name="rating" disabled required>
                            <option value="">Pilih rating</option>

                            @for ($rating = 1; $rating <= 5; $rating++)
                                <option value="{{ $rating }}">
                                    {{ $rating }} - {{ [1 => 'Sangat Buruk', 2 => 'Buruk', 3 => 'Cukup', 4 => 'Baik', 5 => 'Sangat Baik'][$rating] }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ulasan</label>

                        <textarea
                            class="form-control rating-field"
                            name="ulasan"
                            rows="4"
                            maxlength="1000"
                            placeholder="Ceritakan pengalaman Anda saat mengunjungi destinasi ini"
                            disabled
                        ></textarea>
                    </div>

                    <div class="alert alert-info small mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Rating langsung tampil setelah dikirim.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button class="btn btn-primary rating-field" disabled>
                        Kirim Rating
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const checkbox = document.getElementById('visitedCheck');
        const fields = document.querySelectorAll('.rating-field');

        if (checkbox) {
            checkbox.addEventListener('change', () => {
                fields.forEach((field) => {
                    field.disabled = ! checkbox.checked;
                });
            });
        }

        const galleryModal = document.getElementById('galleryModal');
        const galleryCarousel = document.getElementById('galleryCarousel');

        if (galleryModal && galleryCarousel) {
            galleryModal.addEventListener('show.bs.modal', (event) => {
                const trigger = event.relatedTarget;
                const index = Number(trigger?.getAttribute('data-gallery-index') || 0);
                const carousel = bootstrap.Carousel.getOrCreateInstance(galleryCarousel, {
                    interval: false,
                    ride: false,
                });

                carousel.to(index);
            });
        }
    });
</script>
@endpush
