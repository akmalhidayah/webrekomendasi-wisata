@extends('layouts.admin')
@section('title', 'Edit Hotel')
@section('content')
<h1 class="h3 mb-3">Edit {{ $hotel->nama_hotel }}</h1>
<form method="POST" enctype="multipart/form-data" action="{{ route('admin.hotels.update', $hotel) }}">
    @csrf
    @method('PUT')
    @include('admin.hotels._form')
</form>
@endsection
