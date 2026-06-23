@extends('layouts.admin')
@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')
@push('styles')
<style>
    .dashboard-date { color:#64748b; font-size:.75rem; }
    .metric-card { min-height:130px; padding:1.2rem; border:1px solid #e2e8f0; border-radius:14px; background:#fff; }
    .metric-icon { width:40px; height:40px; display:grid; place-items:center; border-radius:10px; color:#075985; background:#e0f2fe; }
    .metric-value { margin-top:1rem; font-size:1.8rem; line-height:1; font-weight:800; }.metric-label { margin-top:.4rem; color:#64748b; font-size:.76rem; }
    .mini-stat { display:flex; align-items:center; gap:.75rem; padding:.85rem; border:1px solid #e5eaf0; border-radius:11px; background:#fff; }.mini-stat i { color:#075985; }.mini-stat strong { display:block; }.mini-stat small { color:#64748b; font-size:.68rem; }
    .summary-card { height:100%; padding:1.25rem; border:1px solid #e2e8f0; border-radius:14px; background:#fff; }.summary-card h2 { font-size:.78rem; color:#64748b; }.summary-value { font-size:1.1rem; font-weight:800; }
    .quick-link { display:flex; align-items:center; gap:.7rem; padding:.8rem; border:1px solid #e2e8f0; border-radius:10px; color:#334155; text-decoration:none; background:#fff; }.quick-link:hover { color:#075985; border-color:#7dd3fc; }
</style>
@endpush
@section('content')
@php
    $primaryMetrics = ['Kategori Wisata' => 'bi-tags', 'Destinasi Wisata' => 'bi-map', 'Guest Visitor' => 'bi-people', 'Survei Preferensi' => 'bi-ui-checks-grid'];
    $secondaryMetrics = ['Hasil Rekomendasi' => 'bi-stars', 'Rating Kunjungan' => 'bi-chat-square-heart', 'Rating Pending' => 'bi-hourglass', 'Rating Disetujui' => 'bi-check-circle', 'Rating Ditolak' => 'bi-x-circle', 'Fasilitas' => 'bi-building', 'Foto Wisata' => 'bi-images'];
@endphp
<div class="d-flex justify-content-between align-items-center mb-3"><h2 class="h4 fw-bold mb-0">Ringkasan</h2><span class="dashboard-date">{{ now()->translatedFormat('d F Y') }}</span></div>
<div class="row g-3 mb-4">@foreach($primaryMetrics as $label => $icon)<div class="col-sm-6 col-xl-3"><div class="metric-card"><div class="metric-icon"><i class="bi {{ $icon }}"></i></div><div class="metric-value">{{ number_format($statistik[$label] ?? 0) }}</div><div class="metric-label">{{ $label }}</div></div></div>@endforeach</div>
<div class="card mb-4"><div class="card-header"><strong>Data Sistem</strong></div><div class="card-body"><div class="row g-3">@foreach($secondaryMetrics as $label => $icon)<div class="col-sm-6 col-lg-4 col-xxl"><div class="mini-stat"><i class="bi {{ $icon }} fs-5"></i><div><strong>{{ number_format($statistik[$label] ?? 0) }}</strong><small>{{ $label }}</small></div></div></div>@endforeach</div></div></div>
<div class="row g-3 mb-4"><div class="col-lg-6"><div class="summary-card"><h2>Rating Tertinggi</h2>@if($wisataRatingTertinggi)<div class="summary-value">{{ $wisataRatingTertinggi->nama_wisata }}</div><div class="small text-muted mt-1"><i class="bi bi-star-fill text-warning"></i> {{ number_format($wisataRatingTertinggi->rating_disetujui_avg, 2) }}/5</div>@else<div class="text-muted small">Belum ada data</div>@endif</div></div><div class="col-lg-6"><div class="summary-card"><h2>Paling Direkomendasikan</h2>@if($wisataPalingDirekomendasikan)<div class="summary-value">{{ $wisataPalingDirekomendasikan->nama_wisata }}</div><div class="small text-muted mt-1">{{ $wisataPalingDirekomendasikan->hasil_rekomendasi_count }} kali</div>@else<div class="text-muted small">Belum ada data</div>@endif</div></div></div>
<div class="card"><div class="card-header"><strong>Akses Cepat</strong></div><div class="card-body"><div class="row g-3"><div class="col-md-4"><a class="quick-link" href="{{ route('admin.wisata.create') }}"><i class="bi bi-plus-lg"></i>Tambah Wisata</a></div><div class="col-md-4"><a class="quick-link" href="{{ route('admin.rating-kunjungan.index', ['status' => 'disetujui']) }}"><i class="bi bi-chat-square-dots"></i>Rating Terbaru</a></div><div class="col-md-4"><a class="quick-link" href="{{ route('admin.hasil-rekomendasi.index') }}"><i class="bi bi-bar-chart"></i>Hasil Rekomendasi</a></div></div></div></div>
@endsection
