@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')

@push('styles')
<style>
    .dashboard-date {
        color: #64748b;
        font-size: .75rem;
    }

    .metric-card {
        min-height: 138px;
        padding: 1.25rem;
        border: 1px solid transparent;
        border-radius: 16px;
        color: #0f172a;
        background: #e0f2fe;
        box-shadow: none;
    }

    .metric-card.card-blue {
        background: #dbeafe;
        border-color: #bfdbfe;
    }

    .metric-card.card-cyan {
        background: #cffafe;
        border-color: #a5f3fc;
    }

    .metric-card.card-green {
        background: #dcfce7;
        border-color: #bbf7d0;
    }

    .metric-card.card-amber {
        background: #fef3c7;
        border-color: #fde68a;
    }

    .metric-icon {
        width: 42px;
        height: 42px;
        display: grid;
        place-items: center;
        border-radius: 12px;
        color: #082f49;
        background: rgba(255, 255, 255, .62);
    }

    .metric-value {
        margin-top: 1rem;
        font-size: 2rem;
        line-height: 1;
        font-weight: 800;
    }

    .metric-label {
        margin-top: .45rem;
        color: #334155;
        font-size: .76rem;
        font-weight: 700;
    }

    .mini-stat {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .85rem;
        border: 1px solid #e5eaf0;
        border-radius: 11px;
        background: #fff;
    }

    .mini-stat i {
        color: #075985;
    }

    .mini-stat strong {
        display: block;
    }

    .mini-stat small {
        color: #64748b;
        font-size: .68rem;
    }

    .summary-card,
    .chart-card {
        height: 100%;
        padding: 1.25rem;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        background: #fff;
        box-shadow: none;
    }

    .summary-card h2,
    .chart-card h2 {
        margin-bottom: .35rem;
        color: #64748b;
        font-size: .78rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .summary-value {
        color: #0f172a;
        font-size: 1.1rem;
        font-weight: 800;
    }

    .chart-wrap {
        position: relative;
        min-height: 290px;
    }

    .chart-wrap.chart-small {
        min-height: 240px;
    }

    .chart-empty {
        min-height: 240px;
        display: grid;
        place-items: center;
        color: #64748b;
        border: 1px dashed #cbd5e1;
        border-radius: 13px;
        background: #f8fafc;
        font-size: .85rem;
    }
</style>
@endpush

@section('content')
@php
    $primaryMetrics = [
        ['label' => 'Kategori Wisata', 'icon' => 'bi-tags', 'class' => 'card-blue'],
        ['label' => 'Destinasi Wisata', 'icon' => 'bi-map', 'class' => 'card-cyan'],
        ['label' => 'Guest Visitor', 'icon' => 'bi-people', 'class' => 'card-green'],
        ['label' => 'Survei Preferensi', 'icon' => 'bi-ui-checks-grid', 'class' => 'card-amber'],
    ];

    $secondaryMetrics = [
        'Hasil Rekomendasi' => 'bi-stars',
        'Rating Kunjungan' => 'bi-chat-square-heart',
        'Rating Pending' => 'bi-hourglass',
        'Rating Disetujui' => 'bi-check-circle',
        'Rating Ditolak' => 'bi-x-circle',
        'Fasilitas' => 'bi-building',
        'Foto Wisata' => 'bi-images',
    ];
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h4 fw-bold mb-0">Ringkasan</h2>
    <span class="dashboard-date">{{ now()->translatedFormat('d F Y') }}</span>
</div>

<div class="row g-3 mb-4">
    @foreach ($primaryMetrics as $metric)
        <div class="col-sm-6 col-xl-3">
            <div class="metric-card {{ $metric['class'] }}">
                <div class="metric-icon">
                    <i class="bi {{ $metric['icon'] }}"></i>
                </div>

                <div class="metric-value">{{ number_format($statistik[$metric['label']] ?? 0) }}</div>
                <div class="metric-label">{{ $metric['label'] }}</div>
            </div>
        </div>
    @endforeach
</div>

<div class="card mb-4">
    <div class="card-header">
        <strong>Data Sistem</strong>
    </div>

    <div class="card-body">
        <div class="row g-3">
            @foreach ($secondaryMetrics as $label => $icon)
                <div class="col-sm-6 col-lg-4 col-xxl">
                    <div class="mini-stat">
                        <i class="bi {{ $icon }} fs-5"></i>

                        <div>
                            <strong>{{ number_format($statistik[$label] ?? 0) }}</strong>
                            <small>{{ $label }}</small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="summary-card">
            <h2>Rating Tertinggi</h2>

            @if ($wisataRatingTertinggi)
                <div class="summary-value">{{ $wisataRatingTertinggi->nama_wisata }}</div>
                <div class="small text-muted mt-1">
                    <i class="bi bi-star-fill text-warning"></i>
                    {{ number_format($wisataRatingTertinggi->rating_disetujui_avg, 2) }}/5
                </div>
            @else
                <div class="text-muted small">Belum ada data</div>
            @endif
        </div>
    </div>

    <div class="col-lg-6">
        <div class="summary-card">
            <h2>Paling Direkomendasikan</h2>

            @if ($wisataPalingDirekomendasikan)
                <div class="summary-value">{{ $wisataPalingDirekomendasikan->nama_wisata }}</div>
                <div class="small text-muted mt-1">{{ $wisataPalingDirekomendasikan->hasil_rekomendasi_count }} kali</div>
            @else
                <div class="text-muted small">Belum ada data</div>
            @endif
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-7">
        <div class="chart-card">
            <h2>Destinasi Paling Sering Direkomendasikan</h2>
            <p class="text-muted small mb-3">Kurva jumlah kemunculan destinasi pada hasil rekomendasi.</p>

            @if ($chartRekomendasi->isNotEmpty())
                <div class="chart-wrap">
                    <canvas id="recommendationChart"></canvas>
                </div>
            @else
                <div class="chart-empty">Belum ada data rekomendasi.</div>
            @endif
        </div>
    </div>

    <div class="col-xl-5">
        <div class="chart-card">
            <h2>Status Rating</h2>
            <p class="text-muted small mb-3">Komposisi rating masuk berdasarkan status.</p>

            @if ($chartStatusRating->sum('total') > 0)
                <div class="chart-wrap chart-small">
                    <canvas id="ratingStatusChart"></canvas>
                </div>
            @else
                <div class="chart-empty">Belum ada rating.</div>
            @endif
        </div>
    </div>
</div>

<div class="chart-card">
    <h2>Rating Destinasi Tertinggi</h2>
    <p class="text-muted small mb-3">Rata-rata rating dari ulasan pengunjung yang aktif.</p>

    @if ($chartRating->isNotEmpty())
        <div class="chart-wrap">
            <canvas id="ratingChart"></canvas>
        </div>
    @else
        <div class="chart-empty">Belum ada data rating destinasi.</div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (!window.Chart) {
            return;
        }

        Chart.defaults.font.family = "'Manrope', sans-serif";
        Chart.defaults.color = '#475569';

        const recommendationData = @json($chartRekomendasi);
        const ratingData = @json($chartRating);
        const ratingStatusData = @json($chartStatusRating);

        const compactLabel = (label) => label.length > 18 ? `${label.slice(0, 18)}...` : label;

        const recommendationCanvas = document.getElementById('recommendationChart');
        if (recommendationCanvas && recommendationData.length) {
            new Chart(recommendationCanvas, {
                type: 'line',
                data: {
                    labels: recommendationData.map((item) => compactLabel(item.nama)),
                    datasets: [{
                        label: 'Direkomendasikan',
                        data: recommendationData.map((item) => item.total),
                        borderColor: '#0369a1',
                        backgroundColor: 'rgba(3, 105, 161, .12)',
                        fill: true,
                        tension: .42,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointBackgroundColor: '#f59e0b',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { title: (items) => recommendationData[items[0].dataIndex].nama } },
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#e2e8f0' } },
                        x: { grid: { display: false } },
                    },
                },
            });
        }

        const ratingStatusCanvas = document.getElementById('ratingStatusChart');
        if (ratingStatusCanvas && ratingStatusData.length) {
            new Chart(ratingStatusCanvas, {
                type: 'doughnut',
                data: {
                    labels: ratingStatusData.map((item) => item.status),
                    datasets: [{
                        data: ratingStatusData.map((item) => item.total),
                        backgroundColor: ['#22c55e', '#ef4444', '#f59e0b'],
                        borderColor: '#ffffff',
                        borderWidth: 4,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '66%',
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } },
                    },
                },
            });
        }

        const ratingCanvas = document.getElementById('ratingChart');
        if (ratingCanvas && ratingData.length) {
            new Chart(ratingCanvas, {
                type: 'bar',
                data: {
                    labels: ratingData.map((item) => compactLabel(item.nama)),
                    datasets: [{
                        label: 'Rata-rata rating',
                        data: ratingData.map((item) => item.rating),
                        backgroundColor: '#fbbf24',
                        borderColor: '#d97706',
                        borderWidth: 1,
                        borderRadius: 10,
                        maxBarThickness: 52,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                title: (items) => ratingData[items[0].dataIndex].nama,
                                afterLabel: (item) => `${ratingData[item.dataIndex].total} rating`,
                            },
                        },
                    },
                    scales: {
                        y: { beginAtZero: true, max: 5, ticks: { stepSize: 1 }, grid: { color: '#e2e8f0' } },
                        x: { grid: { display: false } },
                    },
                },
            });
        }
    });
</script>
@endpush
