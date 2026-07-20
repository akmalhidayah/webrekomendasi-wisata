@extends('layouts.admin')
@section('title', 'Kategori Wisata')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><h1 class="h3 mb-0">Kategori Wisata</h1><a class="btn btn-primary" href="{{ route('admin.kategori-wisata.create') }}">Tambah Kategori</a></div>
<form class="row g-2 mb-3"><div class="col-md-5"><input class="form-control" name="search" value="{{ request('search') }}" placeholder="Cari nama kategori"></div><div class="col-auto"><button class="btn btn-outline-primary">Cari</button><a class="btn btn-light" href="{{ route('admin.kategori-wisata.index') }}">Reset</a></div></form>
<div class="card shadow-sm"><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Nama</th><th>Slug</th><th>Jumlah Wisata</th><th class="text-end">Aksi</th></tr></thead><tbody>
@forelse ($kategori as $item)<tr><td>{{ $item->nama_kategori }}</td><td><code>{{ $item->slug }}</code></td><td>{{ $item->wisata_count }}</td><td class="text-end text-nowrap"><a class="btn btn-sm btn-outline-info" href="{{ route('admin.kategori-wisata.show', $item) }}">Detail</a> <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.kategori-wisata.edit', $item) }}">Edit</a> <form class="d-inline" method="POST" action="{{ route('admin.kategori-wisata.destroy', $item) }}" onsubmit="return confirm('Hapus kategori ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus</button></form></td></tr>
@empty <tr><td colspan="4" class="text-center text-muted py-4">Belum ada kategori.</td></tr>@endforelse
</tbody></table></div></div><x-admin-pagination :paginator="$kategori" />
@endsection
