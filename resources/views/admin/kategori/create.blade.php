@extends('layouts.admin')
@section('title', 'Tambah Kategori')
@section('content')
<h1 class="h3 mb-3">Tambah Kategori</h1><div class="card shadow-sm"><div class="card-body"><form method="POST" action="{{ route('admin.kategori-wisata.store') }}">@csrf @include('admin.kategori._form')</form></div></div>
@endsection
