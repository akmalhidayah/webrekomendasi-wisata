@extends('layouts.admin')
@section('title', 'Edit Fasilitas')
@section('content')
<h1 class="h3">Edit Fasilitas</h1><p class="text-muted">{{ $wisata->nama_wisata }}</p><div class="card shadow-sm"><div class="card-body"><form method="POST" action="{{ route('admin.wisata.fasilitas.update', [$wisata, $fasilitasWisata]) }}">@csrf @method('PUT') @include('admin.fasilitas._form')</form></div></div>
@endsection
