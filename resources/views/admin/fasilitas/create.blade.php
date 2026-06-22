@extends('layouts.admin')
@section('title', 'Tambah Fasilitas')
@section('content')
<h1 class="h3">Tambah Fasilitas</h1><p class="text-muted">{{ $wisata->nama_wisata }}</p><div class="card shadow-sm"><div class="card-body"><form method="POST" action="{{ route('admin.wisata.fasilitas.store', $wisata) }}">@csrf @include('admin.fasilitas._form')</form></div></div>
@endsection
