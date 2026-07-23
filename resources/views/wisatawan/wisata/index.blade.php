@extends('layouts.app')

@section('title', 'Daftar Wisata Makassar')

@push('styles')
<style>
    .wisata-list-page {
        padding-top: 3rem;
        padding-bottom: 4rem;
    }

    .wisata-list-title {
        margin-bottom: 1.35rem;
        color: #0f172a;
        font-size: clamp(2rem, 4vw, 3.2rem);
        font-weight: 850;
        letter-spacing: -.045em;
    }

    .wisata-filter {
        padding: 1rem;
        border: 1px solid #dfe7ef;
        border-radius: 22px;
        background: #ffffff;
        box-shadow: 0 16px 36px rgba(15, 23, 42, .06);
    }

    .wisata-card {
        overflow: hidden;
        height: 100%;
        border: 1px solid #dfe7ef;
        border-radius: 24px;
        background: #ffffff;
        box-shadow: 0 18px 42px rgba(15, 23, 42, .07);
        transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
    }

    .wisata-card:hover {
        transform: translateY(-5px);
        border-color: #bae6fd;
        box-shadow: 0 26px 58px rgba(15, 23, 42, .1);
    }

    .wisata-card-media {
        position: relative;
        overflow: hidden;
        height: 255px;
        background: #e2e8f0;
    }

    .wisata-card-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform .3s ease;
    }

    .wisata-card:hover .wisata-card-img {
        transform: scale(1.04);
    }

    .wisata-card-empty {
        height: 100%;
        display: grid;
        place-items: center;
        color: #64748b;
        background: #f1f5f9;
    }

    .category-float,
    .budget-float {
        position: absolute;
        z-index: 2;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        border-radius: 999px;
        font-size: .72rem;
        font-weight: 850;
        box-shadow: 0 10px 22px rgba(15, 23, 42, .16);
    }

    .category-float {
        left: .9rem;
        top: .9rem;
        padding: .45rem .7rem;
        color: #075985;
        background: rgba(224, 242, 254, .96);
        border: 1px solid rgba(186, 230, 253, .9);
    }

    .budget-float {
        right: .9rem;
        bottom: .9rem;
        padding: .5rem .75rem;
        color: #78350f;
        background: #fbbf24;
    }

    .wisata-card-body {
        padding: 1.2rem;
        display: flex;
        flex-direction: column;
        min-height: 260px;
    }

    .wisata-card-title {
        margin: 0 0 .5rem;
        color: #0f172a;
        font-size: 1.18rem;
        font-weight: 850;
        line-height: 1.25;
    }

    .wisata-card-address {
        color: #64748b;
        font-size: .88rem;
        line-height: 1.6;
    }

    .wisata-distance {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        width: fit-content;
        max-width: 100%;
        margin-top: .75rem;
        padding: .48rem .68rem;
        border: 1px solid #dbeafe;
        border-radius: 999px;
        color: #075985;
        background: #eff6ff;
        font-size: .78rem;
        font-weight: 800;
        line-height: 1.25;
    }

    .wisata-distance.is-muted {
        color: #64748b;
        background: #f8fafc;
        border-color: #e2e8f0;
    }

    .wisata-location-btn {
        border: 0;
        cursor: pointer;
        text-align: left;
    }

    .wisata-location-btn:hover,
    .wisata-location-btn:focus {
        color: #0369a1;
        background: #e0f2fe;
    }

    .wisata-price-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: .55rem;
        margin-top: 1rem;
    }

    .wisata-price-item {
        padding: .72rem;
        border: 1px solid #e5eaf0;
        border-radius: 15px;
        background: #f8fafc;
    }

    .wisata-price-item small {
        display: block;
        color: #64748b;
        font-size: .64rem;
        font-weight: 850;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .wisata-price-item strong {
        display: block;
        margin-top: .15rem;
        color: #0f172a;
        font-size: .86rem;
    }

    .wisata-detail-btn {
        margin-top: auto;
        border-radius: 14px;
        font-weight: 800;
    }

    .wisata-pagination {
        margin: 2rem 0 .5rem;
        padding: 1rem 0;
    }

    .wisata-pagination nav {
        display: flex;
        justify-content: center;
    }

    .wisata-pagination .pagination {
        flex-wrap: wrap;
        justify-content: center;
        gap: .35rem;
        margin-bottom: 0;
    }

    .wisata-pagination .page-link {
        border-radius: 12px;
        border-color: #dbeafe;
        color: #0369a1;
        font-weight: 800;
        box-shadow: 0 8px 18px rgba(15, 23, 42, .06);
    }

    .wisata-pagination .page-item.active .page-link {
        border-color: #0369a1;
        background: #0369a1;
    }

    @media (max-width: 575.98px) {
        .wisata-list-page {
            padding-top: 2.2rem;
        }

        .wisata-card-media {
            height: 220px;
        }

        .wisata-price-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="container wisata-list-page">
    <h1 class="wisata-list-title">Daftar Wisata Makassar</h1>

    <form class="wisata-filter mb-4">
        @if ($hasUserLocation)
            <input type="hidden" name="lat" id="wisataFilterLat" value="{{ $userLocation['lat'] }}">
            <input type="hidden" name="lng" id="wisataFilterLng" value="{{ $userLocation['lng'] }}">
        @endif

        <div class="row g-2">
            <div class="col-md-7">
                <input class="form-control" name="search" value="{{ request('search') }}" placeholder="Cari nama, jenis, atau alamat">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="kategori">
                    <option value="">Semua kategori</option>
                    @foreach ($kategori as $item)
                        <option value="{{ $item->id }}" @selected(request('kategori') == $item->id)>{{ $item->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button class="btn btn-primary">Cari</button>
            </div>
        </div>
    </form>

    @unless ($hasUserLocation)
        <div class="alert alert-info border-0 rounded-4 shadow-sm mb-4 d-flex flex-wrap align-items-center justify-content-between gap-3" id="wisataLocationNotice">
            <span><i class="bi bi-geo-alt-fill me-2"></i>Aktifkan lokasi untuk melihat jarak destinasi dari posisi Anda.</span>
            <button class="btn btn-sm btn-primary rounded-pill fw-bold wisata-location-trigger" type="button">
                Aktifkan lokasi
            </button>
        </div>
    @endunless

    @if ($hasUserLocation)
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-sm btn-outline-secondary rounded-pill" type="button" data-location-clear>Hapus lokasi</button>
        </div>
    @endif

    <div class="row g-4">
        @forelse ($wisata as $item)
            @php
                $lowestHotelPrice = $item->harga_hotel_termurah !== null ? (float) $item->harga_hotel_termurah : null;
                $packageStart = $lowestHotelPrice !== null ? (float) $item->total_estimasi_biaya + $lowestHotelPrice : null;
            @endphp

            <div class="col-md-6 col-xl-4">
                <article class="wisata-card">
                    <div class="wisata-card-media">
                        <img class="wisata-card-img" src="{{ $item->foto_url ?? asset('images/default-wisata.svg') }}" alt="{{ $item->nama_wisata }}" loading="{{ $loop->first ? 'eager' : 'lazy' }}" @if($loop->first) fetchpriority="high" @endif onerror="this.onerror=null;this.src='{{ asset('images/default-wisata.svg') }}';">

                        <span class="category-float">
                            <i class="bi bi-tag-fill"></i>
                            {{ $item->kategoriWisata->nama_kategori }}
                        </span>

                        <span class="budget-float">
                            <i class="bi bi-wallet2"></i>
                            @if ($packageStart !== null)
                                Paket Rp {{ number_format($packageStart, 0, ',', '.') }}
                            @else
                                Rp {{ number_format($item->total_estimasi_biaya, 0, ',', '.') }}
                            @endif
                        </span>
                    </div>

                    <div class="wisata-card-body">
                        <h2 class="wisata-card-title">{{ $item->nama_wisata }}</h2>

                        <p class="wisata-card-address mb-0">
                            <i class="bi bi-geo-alt me-1"></i>
                            {{ Str::limit($item->alamat, 92) }}
                        </p>

                        @if ($hasUserLocation)
                            @if ($item->distance_km !== null)
                                <div class="wisata-distance">
                                    <i class="bi bi-signpost-split-fill"></i>
                                    Jarak dari lokasi Anda: {{ number_format((float) $item->distance_km, 1, '.', '') }} km
                                </div>
                            @else
                                <div class="wisata-distance is-muted">
                                    <i class="bi bi-signpost"></i>
                                    Koordinat destinasi belum tersedia
                                </div>
                            @endif
                        @else
                            <button class="wisata-distance is-muted wisata-location-btn wisata-location-trigger" type="button">
                                <i class="bi bi-crosshair"></i>
                                Aktifkan lokasi untuk melihat jarak destinasi
                            </button>
                        @endif

                        <div class="wisata-price-grid">
                            <div class="wisata-price-item">
                                <small>Estimasi wisata</small>
                                <strong>Rp {{ number_format($item->total_estimasi_biaya, 0, ',', '.') }}</strong>
                            </div>

                            <div class="wisata-price-item">
                                <small>Wisata + hotel</small>
                                <strong>
                                    @if ($packageStart !== null)
                                        Rp {{ number_format($packageStart, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </strong>
                            </div>
                        </div>

                        <a class="btn btn-outline-primary wisata-detail-btn" href="{{ route('wisatawan.wisata.show', $item->slug) }}">
                            Lihat Detail
                        </a>
                    </div>
                </article>
            </div>
        @empty
            <div class="col-12"><div class="alert alert-info">Wisata tidak ditemukan.</div></div>
        @endforelse
    </div>

    @if ($wisata->hasPages())
        <div class="wisata-pagination">{{ $wisata->onEachSide(1)->withQueryString()->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
@endsection

@push('scripts')
@vite('resources/js/location-manager.js')
@endpush
