@extends('layouts.admin')
@section('title', 'Detail Hotel')
@section('content')
<div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
    <div>
        <h1 class="h3 mb-1">{{ $hotel->nama_hotel }}</h1>
        <span class="badge text-bg-{{ $hotel->status === 'aktif' ? 'success' : 'secondary' }}">{{ ucfirst($hotel->status) }}</span>
    </div>
    <div>
        <a class="btn btn-outline-secondary" href="{{ route('admin.hotels.index') }}">Kembali</a>
        <a class="btn btn-primary" href="{{ route('admin.hotels.edit', $hotel) }}">Edit</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        @if ($hotel->gambar_url)
            <img class="img-fluid rounded w-100" style="max-height:420px;object-fit:cover" src="{{ $hotel->gambar_url }}" alt="{{ $hotel->nama_hotel }}">
        @else
            <div class="bg-light rounded d-flex flex-column align-items-center justify-content-center text-muted" style="min-height:300px"><i class="bi bi-building fs-2"></i><small>Gambar belum tersedia</small></div>
        @endif
    </div>
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Alamat</dt><dd class="col-sm-8">{{ $hotel->alamat ?: '-' }}</dd>
                    <dt class="col-sm-4">Harga</dt><dd class="col-sm-8">@if ((float) $hotel->harga_max > (float) $hotel->harga_min) Rp {{ number_format($hotel->harga_min, 0, ',', '.') }} - Rp {{ number_format($hotel->harga_max, 0, ',', '.') }} @else Mulai Rp {{ number_format($hotel->harga_min, 0, ',', '.') }} @endif</dd>
                    <dt class="col-sm-4">Rating</dt><dd class="col-sm-8">{{ $hotel->rating_hotel ? number_format((float) $hotel->rating_hotel, 1, ',', '.') : '-' }}</dd>
                    <dt class="col-sm-4">Traveloka</dt><dd class="col-sm-8">@if ($hotel->traveloka_url)<a href="{{ $hotel->traveloka_url }}" target="_blank" rel="noopener">Buka Traveloka</a>@else-@endif</dd>
                    <dt class="col-sm-4">Google Maps</dt><dd class="col-sm-8">@if ($hotel->maps_url)<a href="{{ $hotel->maps_url }}" target="_blank" rel="noopener">Buka Maps</a>@else-@endif</dd>
                    <dt class="col-sm-4">Koordinat</dt><dd class="col-sm-8">{{ $hotel->latitude && $hotel->longitude ? $hotel->latitude.', '.$hotel->longitude : '-' }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-body">
        <h2 class="h5">Deskripsi</h2>
        <p class="mb-0">{{ $hotel->deskripsi ?: '-' }}</p>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-header"><strong>Destinasi Wisata Terkait</strong></div>
    <div class="card-body">
        @forelse ($hotel->wisata as $wisata)
            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                <div>
                    <strong>{{ $wisata->nama_wisata }}</strong>
                    <br><small class="text-muted">Prioritas {{ $wisata->pivot->urutan }} &middot; {{ $wisata->kategoriWisata?->nama_kategori }}</small>
                </div>
                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.wisata.show', $wisata) }}">Detail</a>
            </div>
        @empty
            <div class="text-muted">Belum ada destinasi terkait.</div>
        @endforelse
    </div>
</div>
@endsection
