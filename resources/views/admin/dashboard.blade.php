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
        min-height: 455px;
    }

    .chart-wrap.chart-small {
        min-height: 240px;
    }

    .guest-list {
        display: grid;
        gap: .7rem;
    }

    .guest-item {
        display: flex;
        justify-content: space-between;
        gap: .8rem;
        padding: .85rem;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #f8fafc;
    }

    .guest-code {
        display: block;
        color: #0f172a;
        font-size: .82rem;
        font-weight: 800;
    }

    .guest-meta {
        display: block;
        color: #64748b;
        font-size: .68rem;
        margin-top: .15rem;
    }

    .guest-count {
        flex: 0 0 auto;
        align-self: center;
        padding: .35rem .55rem;
        border-radius: 999px;
        color: #075985;
        background: #e0f2fe;
        font-size: .68rem;
        font-weight: 800;
        white-space: nowrap;
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
        'Rating Approved' => 'bi-check-circle',
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
                    {{ number_format($wisataRatingTertinggi->rating_approved_avg, 2) }}/5
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

<div class="row g-3">
    <div class="col-xl-8">
        <div class="chart-card">
            <h2>Perbandingan Rekomendasi dan Rating</h2>
            <p class="text-muted small mb-3">Dua kurva untuk melihat destinasi yang sering direkomendasikan dan nilai ratingnya.</p>

            @if ($chartPerbandingan->isNotEmpty())
                <div class="chart-wrap">
                    <canvas id="comparisonChart"></canvas>
                </div>
            @else
                <div class="chart-empty">Belum ada data rekomendasi atau rating.</div>
            @endif
        </div>
    </div>

    <div class="col-xl-4">
        <div class="chart-card">
            <h2>Guest Kunjungan Terbaru</h2>
            <p class="text-muted small mb-3">ID guest terakhir yang mengakses fitur website.</p>

            @if ($guestTerbaru->isNotEmpty())
                <div class="guest-list">
                    @foreach ($guestTerbaru as $guest)
                        <div class="guest-item">
                            <div>
                                <span class="guest-code">{{ $guest->kode_guest }}</span>
                                <span class="guest-meta">
                                    {{ $guest->created_at->diffForHumans() }}
                                    @if ($guest->tanggal_akses)
                                        · {{ $guest->tanggal_akses->format('d-m-Y') }}
                                    @endif
                                </span>
                            </div>

                            <span class="guest-count">
                                {{ $guest->hasil_rekomendasi_count }} rekom
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="chart-empty">Belum ada guest.</div>
            @endif
        </div>
    </div>
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

        const comparisonData = @json($chartPerbandingan);

        const comparisonCanvas = document.getElementById('comparisonChart');
        if (comparisonCanvas && comparisonData.length) {
            new Chart(comparisonCanvas, {
                type: 'line',
                data: {
                    labels: comparisonData.map((item) => item.nama),
                    datasets: [
                        {
                            label: 'Direkomendasikan',
                            data: comparisonData.map((item) => item.rekomendasi),
                            yAxisID: 'y',
                            borderColor: '#0369a1',
                            backgroundColor: 'rgba(3, 105, 161, .12)',
                            fill: true,
                            tension: .42,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            pointBackgroundColor: '#0369a1',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                        },
                        {
                            label: 'Rating',
                            data: comparisonData.map((item) => item.rating),
                            yAxisID: 'ratingScale',
                            borderColor: '#f59e0b',
                            backgroundColor: 'rgba(245, 158, 11, .12)',
                            fill: false,
                            tension: .42,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            pointBackgroundColor: '#f59e0b',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            spanGaps: true,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                title: (items) => comparisonData[items[0].dataIndex].nama,
                                afterBody: (items) => {
                                    const item = comparisonData[items[0].dataIndex];
                                    return [
                                        `${item.jumlah_rating} rating pengunjung`,
                                    ];
                                },
                            },
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            position: 'left',
                            ticks: { precision: 0 },
                            title: { display: true, text: 'Jumlah rekomendasi' },
                            grid: { color: '#e2e8f0' },
                        },
                        ratingScale: {
                            beginAtZero: true,
                            max: 5,
                            position: 'right',
                            ticks: { stepSize: 1 },
                            title: { display: true, text: 'Rating' },
                            grid: { drawOnChartArea: false },
                        },
                        x: {
                            ticks: { display: false },
                            grid: { display: false },
                            border: { display: false },
                        },
                    },
                },
            });
        }
    });
</script>
@endpush
