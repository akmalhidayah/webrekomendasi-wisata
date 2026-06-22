@extends('layouts.admin')
@section('title', 'Tambah Wisata')
@section('content')
<h1 class="h3 mb-3">Tambah Destinasi Wisata</h1><div class="card shadow-sm"><div class="card-body"><form method="POST" enctype="multipart/form-data" action="{{ route('admin.wisata.store') }}">@csrf @include('admin.wisata._form')</form></div></div>
@endsection
