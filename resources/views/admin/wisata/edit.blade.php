@extends('layouts.admin')
@section('title', 'Edit Wisata')
@section('content')
<h1 class="h3 mb-3">Edit {{ $wisata->nama_wisata }}</h1>
<form method="POST" enctype="multipart/form-data" action="{{ route('admin.wisata.update', $wisata) }}">
    @csrf
    @method('PUT')
    @include('admin.wisata._form')
</form>
@endsection
