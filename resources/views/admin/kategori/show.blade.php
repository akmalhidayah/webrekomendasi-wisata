@extends('layouts.admin')
@section('title', 'Detail Kategori')
@section('content')
<div class="d-flex justify-content-between"><h1 class="h3">{{ $kategoriWisata->nama_kategori }}</h1><a class="btn btn-primary" href="{{ route('admin.kategori-wisata.edit', $kategoriWisata) }}">Edit</a></div>
<div class="card shadow-sm mt-3"><div class="card-body"><p><strong>Slug:</strong> {{ $kategoriWisata->slug }}</p><p><strong>Deskripsi:</strong><br>{{ $kategoriWisata->deskripsi ?: '-' }}</p><p class="mb-0"><strong>Jumlah destinasi:</strong> {{ $kategoriWisata->wisata_count }}</p></div></div>
<h2 class="h5 mt-4">Destinasi dalam kategori</h2><ul class="list-group">@forelse ($kategoriWisata->wisata as $item)<li class="list-group-item"><a href="{{ route('admin.wisata.show', $item) }}">{{ $item->nama_wisata }}</a></li>@empty<li class="list-group-item text-muted">Belum ada destinasi.</li>@endforelse</ul>
@endsection
