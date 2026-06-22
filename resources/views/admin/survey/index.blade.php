@extends('layouts.admin')
@section('title', 'Data Survei Preferensi')
@section('content')
<h1 class="h3 mb-3">Data Survei Preferensi</h1>
<form class="row g-2 mb-3"><div class="col-md-5"><input class="form-control" name="search" value="{{ request('search') }}" placeholder="Cari kode guest atau nama wisata"></div><div class="col-auto"><button class="btn btn-outline-primary">Cari</button> <a class="btn btn-light" href="{{ route('admin.survey-preferensi.index') }}">Reset</a></div></form>
<div class="card shadow-sm"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>Guest</th><th>Destinasi</th><th>Kategori</th><th>Rating Awal</th><th>Waktu</th></tr></thead><tbody>@forelse ($survey as $item)<tr><td><code>{{ $item->guestVisitor->kode_guest }}</code></td><td>{{ $item->wisata->nama_wisata }}</td><td>{{ $item->wisata->kategoriWisata->nama_kategori }}</td><td><span class="badge text-bg-primary">{{ $item->rating_awal }}/5</span></td><td>{{ $item->created_at->format('d-m-Y H:i') }}</td></tr>@empty<tr><td colspan="5" class="text-center text-muted py-4">Belum ada data survei.</td></tr>@endforelse</tbody></table></div></div><div class="mt-3">{{ $survey->links() }}</div>
@endsection
