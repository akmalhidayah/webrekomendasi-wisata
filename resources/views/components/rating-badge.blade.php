@props([
    'wisata',
    'position' => 'image',
])

@once
    @push('styles')
        <style>
            .rating-badge {
                display: inline-flex;
                align-items: center;
                gap: 7px;
                width: fit-content;
                max-width: max-content;
                padding: 7px 11px 7px 7px;
                border-radius: 999px;
                background: rgba(255, 255, 255, 0.96);
                color: #0f172a;
                border: 1px solid rgba(226, 232, 240, 0.95);
                box-shadow: 0 12px 28px rgba(15, 23, 42, 0.18);
                font-size: 13px;
                font-weight: 800;
                line-height: 1;
                z-index: 20;
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
            }

            .rating-badge-image {
                position: absolute;
                top: 14px;
                right: 14px;
            }

            .rating-badge-detail {
                position: static;
                padding: 8px 13px 8px 8px;
                font-size: 14px;
                box-shadow: 0 10px 24px rgba(15, 23, 42, 0.12);
            }

            .rating-badge-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 25px;
                height: 25px;
                border-radius: 999px;
                background: linear-gradient(135deg, #f59e0b, #f97316);
                color: #ffffff;
                flex: 0 0 auto;
                box-shadow: 0 6px 14px rgba(245, 158, 11, 0.35);
            }

            .rating-badge-value {
                color: #0f172a;
                font-weight: 900;
                letter-spacing: -0.02em;
            }

            .rating-badge-source {
                color: #64748b;
                font-size: 11px;
                font-weight: 800;
            }

            @media (max-width: 575.98px) {
                .rating-badge-image {
                    top: 10px;
                    right: 10px;
                }

                .rating-badge {
                    padding: 6px 10px 6px 6px;
                    font-size: 12px;
                }

                .rating-badge-icon {
                    width: 23px;
                    height: 23px;
                }

                .rating-badge-source {
                    font-size: 10px;
                }
            }
        </style>
    @endpush
@endonce

@if (! is_null($wisata->rating_tampil))
    <div class="rating-badge rating-badge-{{ $position }}" title="Rating destinasi">
        <span class="rating-badge-icon">
            <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path d="M10 1.6l2.47 5.01 5.53.8-4 3.9.94 5.5L10 14.2l-4.94 2.6.94-5.5-4-3.9 5.53-.8L10 1.6z"/>
            </svg>
        </span>

        <span class="rating-badge-value">
            {{ number_format((float) $wisata->rating_tampil, 1, ',', '.') }}
        </span>

        <span class="rating-badge-source">
            @if ($wisata->jumlah_rating_aplikasi > 0)
                {{ $wisata->jumlah_rating_aplikasi }} ulasan
            @else
                rating
            @endif
        </span>
    </div>
@endif
