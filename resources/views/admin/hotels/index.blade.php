@extends('layouts.admin')
@section('title', 'Data Hotel')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Data Hotel</h1>
    <a class="btn btn-primary" href="{{ route('admin.hotels.create') }}">Tambah Hotel</a>
</div>

<form class="row g-2 mb-3">
    <div class="col-lg-5"><input class="form-control" name="search" value="{{ request('search') }}" placeholder="Cari nama hotel atau alamat"></div>
    <div class="col-lg-3"><select class="form-select" name="status"><option value="">Semua status</option><option value="aktif" @selected(request('status') === 'aktif')>Aktif</option><option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option></select></div>
    <div class="col-auto"><button class="btn btn-outline-primary">Filter</button> <a class="btn btn-light" href="{{ route('admin.hotels.index') }}">Reset</a></div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th>Foto</th><th>Nama Hotel</th><th>Harga</th><th>Rating</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
                @forelse ($hotels as $hotel)
                    <tr>
                        <td>
                            @if ($hotel->gambar_url)
                                <img class="thumb rounded" src="{{ $hotel->gambar_url }}" alt="{{ $hotel->nama_hotel }}" loading="lazy">
                            @else
                                <span class="d-inline-flex align-items-center justify-content-center bg-light text-muted rounded" style="width:86px;height:62px"><i class="bi bi-building"></i></span>
                            @endif
                        </td>
                        <td class="fw-semibold">
                            {{ $hotel->nama_hotel }}
                            @if ($hotel->traveloka_url)
                                <br><a class="badge text-bg-info text-decoration-none mt-1" href="{{ $hotel->traveloka_url }}" target="_blank" rel="noopener">Traveloka</a>
                            @endif
                        </td>
                        <td>
                            @if ((float) $hotel->harga_max > (float) $hotel->harga_min)
                                Rp {{ number_format($hotel->harga_min, 0, ',', '.') }} - Rp {{ number_format($hotel->harga_max, 0, ',', '.') }}
                            @else
                                Mulai Rp {{ number_format($hotel->harga_min, 0, ',', '.') }}
                            @endif
                        </td>
                        <td>{{ $hotel->rating_hotel ? number_format((float) $hotel->rating_hotel, 1, ',', '.') : '-' }}</td>
                        <td><span class="badge text-bg-{{ $hotel->status === 'aktif' ? 'success' : 'secondary' }}">{{ ucfirst($hotel->status) }}</span></td>
                        <td class="text-end text-nowrap">
                            <a class="btn btn-sm btn-outline-info" href="{{ route('admin.hotels.show', $hotel) }}">Detail</a>
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.hotels.edit', $hotel) }}">Edit</a>
                            <form class="d-inline" method="POST" action="{{ route('admin.hotels.destroy', $hotel) }}" onsubmit="return confirm('Hapus sementara hotel ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus</button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Data hotel tidak ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $hotels->links() }}</div>
@endsection
