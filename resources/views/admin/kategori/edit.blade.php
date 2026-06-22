@extends('layouts.admin')
@section('title', 'Edit Kategori')
@section('content')
<h1 class="h3 mb-3">Edit Kategori</h1><div class="card shadow-sm"><div class="card-body"><form method="POST" action="{{ route('admin.kategori-wisata.update', $kategoriWisata) }}">@csrf @method('PUT') @include('admin.kategori._form')</form></div></div>
@endsection
