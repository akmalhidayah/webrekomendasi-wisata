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

    @media (max-width: 575.98px) {
        .wisata-list-page {
            padding-top: 1.8rem;
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

    <div class="row g-4">
        @forelse ($wisata as $item)
            @php
                $lowestHotelPrice = $item->hotels->min(fn ($hotel) => (float) $hotel->harga_min);
                $packageStart = $lowestHotelPrice !== null ? (float) $item->total_estimasi_biaya + $lowestHotelPrice : null;
            @endphp

            <div class="col-md-6 col-xl-4">
                <article class="wisata-card">
                    <div class="wisata-card-media">
                        @if($item->foto_url)
                            <img class="wisata-card-img" src="{{ $item->foto_url }}" alt="{{ $item->nama_wisata }}" loading="lazy">
                        @else
                            <div class="wisata-card-empty">
                                <div class="text-center">
                                    <i class="bi bi-image fs-2"></i>
                                    <div class="small mt-1">Foto belum tersedia</div>
                                </div>
                            </div>
                        @endif

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

    <div class="mt-4">{{ $wisata->links() }}</div>
</div>
@endsection
