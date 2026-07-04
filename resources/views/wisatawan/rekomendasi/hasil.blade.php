@extends('layouts.app')

@section('title', 'Hasil Rekomendasi Wisata')

@push('styles')
<style>
    .result-page { padding: 2.4rem 0 4rem; }
    .result-hero { padding: 1.5rem; border: 1px solid #dfe7ef; border-radius: 28px; background: linear-gradient(135deg, #ffffff, #f0f9ff); box-shadow: 0 22px 55px rgba(15, 23, 42, .08); }
    .result-kicker { display: inline-flex; align-items: center; gap: .45rem; padding: .45rem .75rem; border-radius: 999px; color: #0369a1; background: #e0f2fe; font-size: .75rem; font-weight: 850; text-transform: uppercase; letter-spacing: .08em; }
    .result-title { margin: .8rem 0 .4rem; color: #0f172a; font-size: clamp(2rem, 4vw, 3.2rem); line-height: 1.08; letter-spacing: -.045em; font-weight: 900; }
    .result-subtitle { color: #64748b; line-height: 1.7; }
    .guest-code { display: inline-flex; align-items: center; gap: .45rem; padding: .55rem .75rem; border: 1px solid #e2e8f0; border-radius: 14px; background: #fff; color: #334155; font-size: .84rem; font-weight: 750; }
    .result-actions { display: flex; flex-wrap: wrap; gap: .6rem; justify-content: flex-end; }
    .result-actions .btn { min-height: 42px; border-radius: 13px; font-size: .82rem; font-weight: 800; }
    .result-info { display: flex; gap: .8rem; padding: 1rem; border: 1px solid #dbeafe; border-radius: 18px; background: #eff6ff; color: #1e3a8a; }
    .recommend-list { display: grid; gap: 1rem; }
    .recommend-card { position: relative; overflow: hidden; display: grid; grid-template-columns: 315px 1fr; border: 1px solid #dfe7ef; border-radius: 26px; background: #fff; box-shadow: 0 18px 42px rgba(15, 23, 42, .07); animation: fadeUp .45s ease both; transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease; }
    .recommend-card:hover { transform: translateY(-4px); border-color: #bae6fd; box-shadow: 0 26px 58px rgba(15, 23, 42, .1); }
    .recommend-card.rank-one { border-color: #facc15; background: linear-gradient(135deg, #fffdf2, #ffffff); }
    .recommend-media { position: relative; min-height: 100%; background: #e2e8f0; }
    .recommend-img { width: 100%; height: 100%; min-height: 320px; object-fit: cover; display: block; }
    .recommend-img-empty { min-height: 320px; display: grid; place-items: center; color: #64748b; background: #f1f5f9; }
    .rank-badge { position: absolute; top: 14px; left: 14px; display: inline-flex; align-items: center; gap: .42rem; padding: .58rem .78rem; border-radius: 999px; color: #fff; background: #0f172a; font-size: .78rem; font-weight: 900; box-shadow: 0 12px 26px rgba(15, 23, 42, .22); }
    .rank-badge.gold { color: #78350f; background: #fbbf24; }
    .rank-badge.silver { background: #64748b; }
    .rank-badge.bronze { background: #92400e; }
    .score-pill { position: absolute; right: 14px; bottom: 14px; padding: .58rem .78rem; border-radius: 999px; color: #075985; background: rgba(255, 255, 255, .94); font-size: .78rem; font-weight: 900; box-shadow: 0 12px 26px rgba(15, 23, 42, .18); }
    .recommend-body { padding: 1.25rem; }
    .category-chip { display: inline-flex; align-items: center; gap: .35rem; width: fit-content; padding: .38rem .68rem; border: 1px solid #cffafe; border-radius: 999px; color: #0e7490; background: #ecfeff; font-size: .72rem; font-weight: 850; }
    .recommend-name { margin: .75rem 0 .35rem; color: #0f172a; font-size: clamp(1.25rem, 2vw, 1.65rem); font-weight: 900; line-height: 1.18; letter-spacing: -.025em; }
    .recommend-address { color: #64748b; font-size: .9rem; line-height: 1.55; }
    .metric-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: .65rem; margin: 1rem 0; }
    .metric-item { padding: .78rem; border: 1px solid #e5eaf0; border-radius: 16px; background: #f8fafc; }
    .metric-item small { display: block; color: #64748b; font-size: .65rem; font-weight: 850; text-transform: uppercase; letter-spacing: .05em; }
    .metric-item strong { display: block; margin-top: .18rem; color: #0f172a; font-size: .88rem; }
    .hotel-strip { display: flex; justify-content: space-between; gap: .75rem; align-items: center; padding: .9rem; border: 1px solid #fde68a; border-radius: 18px; background: #fffbeb; }
    .hotel-strip strong { color: #78350f; }
    .reason-list { display: flex; flex-wrap: wrap; gap: .45rem; margin: .8rem 0 0; padding: 0; list-style: none; }
    .reason-list li { padding: .38rem .6rem; border-radius: 999px; color: #075985; background: #e0f2fe; font-size: .72rem; font-weight: 800; }
    .score-progress { height: 8px; overflow: hidden; border-radius: 999px; background: #e2e8f0; }
    .score-progress span { display: block; height: 100%; border-radius: inherit; background: linear-gradient(90deg, #0369a1, #f59e0b); animation: scoreFill .9s ease both; }
    .accordion-button { font-weight: 850; }
    .score-detail-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: .65rem; }
    .formula-box { margin-top: .75rem; padding: .85rem; border-radius: 16px; background: #f8fafc; color: #475569; font-size: .86rem; }
    .recommend-actions { display: flex; flex-wrap: wrap; gap: .55rem; margin-top: 1rem; }
    .recommend-actions .btn { border-radius: 13px; font-weight: 850; font-size: .8rem; }
    .empty-state { padding: 3rem 1.5rem; border: 1px solid #dfe7ef; border-radius: 24px; background: #fff; text-align: center; box-shadow: 0 14px 34px rgba(15, 23, 42, .05); }
    @keyframes fadeUp { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }
    @keyframes scoreFill { from { width: 0; } }
    @media (max-width: 991.98px) {
        .recommend-card { grid-template-columns: 1fr; }
        .recommend-img, .recommend-img-empty { min-height: 250px; height: 250px; }
        .metric-grid, .score-detail-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .result-actions { justify-content: flex-start; }
    }
    @media (max-width: 575.98px) {
        .metric-grid, .score-detail-grid { grid-template-columns: 1fr; }
        .hotel-strip { display: block; }
        .recommend-actions .btn, .result-actions .btn { width: 100%; }
    }
</style>
@endpush

@section('content')
@php
    $formatRupiah = fn ($value) => 'Rp '.number_format((float) $value, 0, ',', '.');
    $formatScore = fn ($value) => $value === null ? '-' : number_format((float) $value * 100, 0, ',', '.').'%';
    $formatScoreFive = fn ($value) => $value === null ? '-' : number_format((float) $value, 2, ',', '.').' / 5';
    $formatDistance = fn ($value) => $value === null ? '-' : number_format((float) $value, 1, ',', '.').' km';
@endphp

<div class="container result-page">
    <div class="result-hero mb-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <div class="result-kicker"><i class="bi bi-stars"></i> Rekomendasi Personal</div>
                <h1 class="result-title">Hasil Rekomendasi Wisata</h1>
                <p class="result-subtitle mb-3">Ranking dihitung dari Collaborative Filtering, budget, kebutuhan hotel, jarak lokasi, preferensi kategori, dan rating destinasi.</p>
                <div class="guest-code"><i class="bi bi-person-badge"></i> Kode guest: <code>{{ $guest->kode_guest }}</code></div>
            </div>
            <div class="col-lg-4">
                <div class="result-actions">
                    <form method="POST" action="{{ route('wisatawan.rekomendasi.reset') }}" class="form-reset-rekomendasi">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-primary"><i class="bi bi-arrow-repeat me-1"></i>Isi Ulang Survei</button>
                    </form>
                    <a class="btn btn-outline-secondary" href="{{ route('wisatawan.wisata.index') }}"><i class="bi bi-grid me-1"></i>Daftar Wisata</a>
                </div>
            </div>
        </div>
    </div>

    <div class="result-info mb-4">
        <i class="bi bi-info-circle-fill"></i>
        <div>
            <strong>Informasi Perhitungan</strong>
            <div class="small">Jika lokasi tersedia: 40% CF + 25% Budget + 20% Jarak + 10% Preferensi + 5% Rating Destinasi. Jika lokasi dilewati: bobot jarak dialihkan ke CF, preferensi, dan rating.</div>
        </div>
    </div>

    @if ($hasil->isEmpty())
        <div class="empty-state">
            <div class="display-5 text-primary mb-3"><i class="bi bi-magic"></i></div>
            <h2 class="h4 fw-bold">Hasil rekomendasi belum tersedia</h2>
            <p class="text-muted mb-4">Klik tombol di bawah ini untuk memproses rekomendasi berdasarkan survei preferensi yang telah Anda isi.</p>
            <form method="POST" action="{{ route('wisatawan.rekomendasi.proses') }}">
                @csrf
                <button class="btn btn-primary btn-lg rounded-pill px-4"><i class="bi bi-stars me-1"></i>Proses Rekomendasi</button>
            </form>
        </div>
    @else
        <div class="recommend-list">
            @foreach ($hasil as $index => $item)
                @php
                    $rank = $item->ranking ?: $index + 1;
                    $rankClass = $rank === 1 ? 'gold' : ($rank === 2 ? 'silver' : ($rank === 3 ? 'bronze' : ''));
                    $scoreFive = (float) ($item->nilai_prediksi ?? (($item->skor_akhir ?? 0) * 5));
                    $scorePercent = max(0, min(100, (float) ($item->skor_akhir ?? ($scoreFive / 5)) * 100));
                    $reasons = collect($item->alasan_rekomendasi ?? [])->filter()->values();
                    $mapsUrl = $item->wisata?->maps_url ?: $item->wisata?->link_maps;
                    $hasDistance = ! is_null($item->jarak_km);
                @endphp

                @if ($item->wisata)
                    <article class="recommend-card {{ $rank === 1 ? 'rank-one' : '' }}" style="animation-delay: {{ ($index % 5) * 80 }}ms">
                        <div class="recommend-media">
                            @if ($item->wisata->foto_url)
                                <img class="recommend-img" src="{{ $item->wisata->foto_url }}" alt="{{ $item->wisata->nama_wisata }}" loading="lazy">
                            @else
                                <div class="recommend-img-empty"><i class="bi bi-image fs-1"></i></div>
                            @endif
                            <span class="rank-badge {{ $rankClass }}"><i class="bi {{ $rank === 1 ? 'bi-trophy-fill' : 'bi-award-fill' }}"></i> Ranking #{{ $rank }}</span>
                            <span class="score-pill">{{ number_format($scoreFive, 2, ',', '.') }} / 5</span>
                        </div>

                        <div class="recommend-body">
                            <span class="category-chip"><i class="bi bi-tag-fill"></i>{{ $item->wisata->kategoriWisata->nama_kategori }}</span>
                            <h2 class="recommend-name">{{ $item->wisata->nama_wisata }}</h2>
                            <p class="recommend-address mb-0"><i class="bi bi-geo-alt me-1"></i>{{ Str::limit($item->wisata->alamat, 120) }}</p>

                            <div class="metric-grid">
                                <div class="metric-item"><small>Skor akhir</small><strong>{{ $formatScoreFive($scoreFive) }}</strong></div>
                                <div class="metric-item"><small>Jarak</small><strong>{{ $formatDistance($item->jarak_km) }}</strong></div>
                                <div class="metric-item"><small>Biaya wisata</small><strong>{{ $formatRupiah($item->estimasi_biaya_wisata) }}</strong></div>
                                <div class="metric-item"><small>Total budget</small><strong>{{ $formatRupiah($item->total_estimasi_budget) }}</strong></div>
                            </div>

                            <div class="score-progress mb-3"><span style="width: {{ $scorePercent }}%"></span></div>

                            @if ($item->hotel)
                                <div class="hotel-strip mb-3">
                                    <div>
                                        <strong><i class="bi bi-building me-1"></i>{{ $item->hotel->nama_hotel }}</strong>
                                        <div class="small text-muted">Hotel {{ $formatRupiah($item->estimasi_biaya_hotel) }} &middot; total {{ $formatRupiah($item->total_estimasi_budget) }}</div>
                                    </div>
                                    @if ($item->hotel->traveloka_url)
                                        <a class="btn btn-sm btn-warning fw-bold" href="{{ $item->hotel->traveloka_url }}" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right me-1"></i>Traveloka</a>
                                    @endif
                                </div>
                            @endif

                            @if ($reasons->isNotEmpty())
                                <ul class="reason-list">
                                    @foreach ($reasons->take(4) as $reason)
                                        <li>{{ $reason }}</li>
                                    @endforeach
                                </ul>
                            @endif

                            <div class="accordion mt-3" id="calcAccordion{{ $item->id }}">
                                <div class="accordion-item border rounded-4 overflow-hidden">
                                    <h3 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#calc{{ $item->id }}">
                                            Lihat Detail Perhitungan
                                        </button>
                                    </h3>
                                    <div id="calc{{ $item->id }}" class="accordion-collapse collapse" data-bs-parent="#calcAccordion{{ $item->id }}">
                                        <div class="accordion-body">
                                            <div class="score-detail-grid">
                                                <div class="metric-item"><small>Skor CF</small><strong>{{ $formatScore($item->skor_cf) }}</strong></div>
                                                <div class="metric-item"><small>Skor Budget</small><strong>{{ $formatScore($item->skor_budget) }}</strong></div>
                                                <div class="metric-item"><small>Skor Jarak</small><strong>{{ $item->skor_jarak === null ? '-' : $formatScore($item->skor_jarak) }}</strong></div>
                                                <div class="metric-item"><small>Skor Preferensi</small><strong>{{ $formatScore($item->skor_preferensi) }}</strong></div>
                                                <div class="metric-item"><small>Skor Rating</small><strong>{{ $formatScore($item->skor_rating_destinasi) }}</strong></div>
                                                <div class="metric-item"><small>Skor Akhir</small><strong>{{ $formatScore($item->skor_akhir) }}</strong></div>
                                            </div>
                                            <div class="formula-box">
                                                Formula: {{ $hasDistance ? '40% CF + 25% Budget + 20% Jarak + 10% Preferensi + 5% Rating Destinasi' : '50% CF + 25% Budget + 15% Preferensi + 10% Rating Destinasi' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="recommend-actions">
                                <a class="btn btn-primary" href="{{ route('wisatawan.wisata.show', $item->wisata->slug) }}"><i class="bi bi-eye me-1"></i>Lihat Detail</a>
                                @if ($mapsUrl)
                                    <a class="btn btn-outline-secondary" href="{{ $mapsUrl }}" target="_blank" rel="noopener"><i class="bi bi-map me-1"></i>Buka Maps</a>
                                @endif
                                @if ($item->hotel?->traveloka_url)
                                    <a class="btn btn-outline-warning" href="{{ $item->hotel->traveloka_url }}" target="_blank" rel="noopener"><i class="bi bi-building me-1"></i>Traveloka Hotel</a>
                                @endif
                            </div>
                        </div>
                    </article>
                @endif
            @endforeach
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const resetForms = document.querySelectorAll('.form-reset-rekomendasi');
        resetForms.forEach((form) => {
            form.addEventListener('submit', (event) => {
                event.preventDefault();
                if (typeof Swal === 'undefined') {
                    if (confirm('Hapus hasil rekomendasi dan isi ulang survei?')) form.submit();
                    return;
                }
                Swal.fire({
                    title: 'Isi ulang survei?',
                    text: 'Hasil rekomendasi dan data survei sebelumnya akan dihapus.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, isi ulang',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#0369a1',
                    cancelButtonColor: '#64748b'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });

        @if ($showResultPopup && $topRecommendation?->wisata)
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Rekomendasi Terbaik untuk Anda',
                    html: `
                        <div class="text-start">
                            <strong>{{ $topRecommendation->wisata->nama_wisata }}</strong><br>
                            Skor: {{ number_format((float) $topRecommendation->nilai_prediksi, 2, ',', '.') }} / 5<br>
                            @if ($topRecommendation->jarak_km !== null)
                                Jarak: {{ number_format((float) $topRecommendation->jarak_km, 1, ',', '.') }} km<br>
                            @endif
                            Total budget: {{ $formatRupiah($topRecommendation->total_estimasi_budget) }}<br>
                            @if ($topRecommendation->hotel)
                                Hotel: {{ $topRecommendation->hotel->nama_hotel }}<br>
                            @endif
                        </div>
                    `,
                    icon: 'success',
                    confirmButtonText: 'Lihat Semua Rekomendasi',
                    confirmButtonColor: '#0369a1'
                });
            }
        @endif
    });
</script>
@endpush
