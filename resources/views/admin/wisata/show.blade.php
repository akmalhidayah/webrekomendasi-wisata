@extends('layouts.admin')
@section('title', 'Detail Wisata')
@section('content')
@php
    $mapsUrl = $wisata->maps_url ?: $wisata->link_maps;
@endphp
<div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
    <div>
        <h1 class="h3 mb-1">{{ $wisata->nama_wisata }}</h1>
        <span class="badge text-bg-success">{{ $wisata->kategoriWisata->nama_kategori }}</span>
    </div>
    <div>
        <a class="btn btn-outline-secondary" href="{{ route('admin.wisata.fasilitas.index', $wisata) }}">Kelola Fasilitas</a>
        <a class="btn btn-outline-secondary" href="{{ route('admin.wisata.foto.index', $wisata) }}">Kelola Foto</a>
        <a class="btn btn-primary" href="{{ route('admin.wisata.edit', $wisata) }}">Edit</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        @if($wisata->foto_url)
            <img class="img-fluid rounded w-100" style="max-height:420px;object-fit:cover" src="{{ $wisata->foto_url }}" alt="{{ $wisata->nama_wisata }}">
        @else
            <div class="bg-light rounded d-flex flex-column align-items-center justify-content-center text-muted" style="min-height:300px"><i class="bi bi-image fs-2"></i><small>Foto belum tersedia</small></div>
        @endif
    </div>
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Jenis</dt><dd class="col-sm-8">{{ $wisata->jenis_wisata }}</dd>
                    <dt class="col-sm-4">Alamat</dt><dd class="col-sm-8">{{ $wisata->alamat }}</dd>
                    <dt class="col-sm-4">Jam operasional</dt><dd class="col-sm-8">{{ $wisata->jam_operasional ?: '-' }}</dd>
                    <dt class="col-sm-4">Status</dt><dd class="col-sm-8">{{ ucfirst($wisata->status) }}</dd>
                    <dt class="col-sm-4">Google Maps</dt><dd class="col-sm-8">@if ($mapsUrl)<a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer">Buka lokasi</a>@else-@endif</dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-body">
        <h2 class="h5">Deskripsi</h2>
        <p>{{ $wisata->deskripsi ?: '-' }}</p>
        <h2 class="h5 mt-4">Estimasi Biaya</h2>
        <div class="row">
            <div class="col-md-3">Tiket<br><strong>Rp {{ number_format($wisata->harga_tiket, 0, ',', '.') }}</strong></div>
            <div class="col-md-3">Transportasi<br><strong>Rp {{ number_format($wisata->estimasi_transportasi, 0, ',', '.') }}</strong></div>
            <div class="col-md-3">Lainnya<br><strong>Rp {{ number_format($wisata->estimasi_biaya_lainnya, 0, ',', '.') }}</strong></div>
            <div class="col-md-3">Total<br><strong>Rp {{ number_format($wisata->total_estimasi_biaya, 0, ',', '.') }}</strong></div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header"><strong>Informasi Lokasi</strong></div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Latitude</dt><dd class="col-sm-8">{{ $wisata->latitude ?? '-' }}</dd>
                    <dt class="col-sm-4">Longitude</dt><dd class="col-sm-8">{{ $wisata->longitude ?? '-' }}</dd>
                    <dt class="col-sm-4">Koordinat</dt><dd class="col-sm-8">{{ $wisata->coordinate_label }}</dd>
                    <dt class="col-sm-4">Maps URL</dt><dd class="col-sm-8">@if ($mapsUrl)<a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer">Buka Maps</a>@else-@endif</dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header"><strong>Hotel Terkait</strong></div>
            <div class="card-body">
                @forelse ($wisata->hotels->take(3) as $hotel)
                    <div class="d-flex justify-content-between align-items-start border-bottom py-2">
                        <div>
                            <strong>{{ $hotel->pivot->urutan }}. {{ $hotel->nama_hotel }}</strong>
                            <br><small class="text-muted">@if ((float) $hotel->harga_max > (float) $hotel->harga_min) Rp {{ number_format($hotel->harga_min, 0, ',', '.') }} - Rp {{ number_format($hotel->harga_max, 0, ',', '.') }} @else Mulai Rp {{ number_format($hotel->harga_min, 0, ',', '.') }} @endif</small>
                            <br><small class="text-muted">Rating {{ $hotel->rating_hotel ? number_format((float) $hotel->rating_hotel, 1, ',', '.') : '-' }}</small>
                            @if ($hotel->pivot->keterangan)
                                <br><small>{{ $hotel->pivot->keterangan }}</small>
                            @endif
                        </div>
                        <div class="text-end">
                            @if ($hotel->traveloka_url)
                                <a class="btn btn-sm btn-outline-info mb-1" href="{{ $hotel->traveloka_url }}" target="_blank" rel="noopener noreferrer">Traveloka</a>
                            @endif
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.hotels.show', $hotel) }}">Detail</a>
                        </div>
                    </div>
                @empty
                    <div class="text-muted">Belum ada hotel terkait.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-lg-6">
        <h2 class="h5">Fasilitas</h2>
        <ul class="list-group">@forelse ($wisata->fasilitasWisata as $item)<li class="list-group-item"><strong>{{ $item->nama_fasilitas }}</strong><br><small>{{ $item->keterangan }}</small></li>@empty<li class="list-group-item text-muted">Belum ada fasilitas.</li>@endforelse</ul>
    </div>
    <div class="col-lg-6">
        <h2 class="h5">Foto Tambahan</h2>
        <div class="row g-2">@forelse ($wisata->fotoWisata->filter(fn($foto) => $foto->foto_url) as $item)<div class="col-4"><img class="img-fluid rounded" style="height:100px;width:100%;object-fit:cover" src="{{ $item->foto_url }}" alt="{{ $item->caption }}"></div>@empty<div class="text-muted">Belum ada foto tambahan.</div>@endforelse</div>
    </div>
</div>
@endsection
