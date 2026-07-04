@extends('layouts.admin')
@section('title', 'Tambah Hotel')
@section('content')
<h1 class="h3 mb-3">Tambah Hotel</h1>
<form method="POST" enctype="multipart/form-data" action="{{ route('admin.hotels.store') }}">
    @csrf
    @include('admin.hotels._form', ['hotel' => null])
</form>
@endsection
