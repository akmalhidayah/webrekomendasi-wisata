@extends('layouts.app')

@section('title', 'Survei Preferensi Wisata')

@push('styles')
<style>
    .survey-page { padding: 2.5rem 0 4rem; }
    .survey-shell { max-width: 1180px; margin: 0 auto; }
    .survey-hero { display: flex; justify-content: space-between; gap: 1.4rem; align-items: flex-end; margin-bottom: 1.4rem; }
    .survey-title { margin: 0; color: #0f172a; font-size: clamp(2rem, 4vw, 3.2rem); font-weight: 850; letter-spacing: -.045em; }
    .survey-subtitle { max-width: 720px; color: #64748b; line-height: 1.7; }
    .wizard-steps { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: .8rem; margin-bottom: 1.2rem; }
    .wizard-step { display: flex; align-items: center; gap: .75rem; padding: .9rem; border: 1px solid #dfe7ef; border-radius: 18px; background: #fff; color: #64748b; font-weight: 800; }
    .wizard-step span { width: 34px; height: 34px; display: grid; place-items: center; border-radius: 12px; background: #f1f5f9; color: #0369a1; }
    .wizard-step.is-active { border-color: #bae6fd; color: #075985; background: #f0f9ff; }
    .wizard-step.is-active span { color: #fff; background: #0369a1; }
    .wizard-panel { display: none; border: 1px solid #dfe7ef; border-radius: 26px; background: #fff; box-shadow: 0 18px 44px rgba(15, 23, 42, .07); overflow: hidden; }
    .wizard-panel.is-active { display: block; animation: fadeUp .35s ease both; }
    .wizard-panel-head { padding: 1.3rem 1.4rem; border-bottom: 1px solid #edf2f7; background: #f8fafc; }
    .wizard-panel-head h2 { margin: 0; color: #0f172a; font-size: 1.2rem; font-weight: 850; }
    .wizard-panel-head p { margin: .35rem 0 0; color: #64748b; }
    .wizard-panel-body { padding: 1.4rem; }
    .rating-card { height: 100%; overflow: hidden; border: 1px solid #e2e8f0; border-radius: 20px; background: #fff; box-shadow: 0 12px 28px rgba(15, 23, 42, .05); animation: fadeUp .45s ease both; }
    .rating-media { height: 160px; background: #e2e8f0; }
    .rating-media img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .rating-empty { height: 100%; display: grid; place-items: center; color: #64748b; background: #f1f5f9; }
    .rating-body { padding: 1rem; }
    .rating-body h3 { min-height: 2.55rem; margin: .55rem 0 .35rem; color: #0f172a; font-size: 1rem; font-weight: 850; line-height: 1.25; }
    .category-chip { display: inline-flex; width: fit-content; padding: .35rem .62rem; border-radius: 999px; color: #075985; background: #e0f2fe; font-size: .68rem; font-weight: 850; }
    .budget-card { padding: 1.1rem; border: 1px solid #e2e8f0; border-radius: 20px; background: #f8fafc; }
    .hotel-choice { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .8rem; }
    .choice-card { position: relative; padding: 1rem; border: 1px solid #dfe7ef; border-radius: 18px; background: #fff; cursor: pointer; }
    .choice-card input { position: absolute; opacity: 0; }
    .choice-card strong { display: block; color: #0f172a; }
    .choice-card small { color: #64748b; }
    .choice-card:has(input:checked) { border-color: #0369a1; background: #f0f9ff; box-shadow: 0 12px 26px rgba(3, 105, 161, .12); }
    .location-box { padding: 1.2rem; border: 1px solid #dbeafe; border-radius: 22px; background: linear-gradient(135deg, #eff6ff, #fff); }
    .location-icon { width: 70px; height: 70px; display: grid; place-items: center; border-radius: 24px; color: #fff; background: #0369a1; font-size: 2rem; animation: pinPulse 1.8s ease-in-out infinite; }
    .survey-map { margin-top: 1rem; overflow: hidden; border: 1px solid #bfdbfe; border-radius: 20px; background: #e2e8f0; }
    .survey-map[hidden] { display: none; }
    .survey-map iframe { display: block; width: 100%; height: clamp(280px, 34vw, 340px); border: 0; }
    .preference-feedback { display: none; margin-top: 1rem; }
    .preference-feedback.is-visible { display: block; }
    .wizard-actions { display: flex; justify-content: space-between; gap: .8rem; margin-top: 1.2rem; }
    .wizard-actions .btn { min-height: 46px; border-radius: 14px; font-weight: 850; }
    .recommend-loading { position: fixed; inset: 0; z-index: 3000; display: none; place-items: center; padding: max(1rem, env(safe-area-inset-top)) max(1rem, env(safe-area-inset-right)) max(1rem, env(safe-area-inset-bottom)) max(1rem, env(safe-area-inset-left)); background: rgba(3, 18, 31, .92); }
    .recommend-loading.is-visible { display: grid; }
    .loading-content { width: min(560px, 100%); display: grid; justify-items: center; gap: .6rem; }
    .loading-visual { position: relative; width: clamp(170px, 30vw, 250px); aspect-ratio: 1; display: grid; place-items: center; isolation: isolate; }
    .loading-orbit { position: absolute; inset: 8%; border: 5px solid rgba(255, 255, 255, .18); border-top-color: #38bdf8; border-radius: 50%; animation: loadingOrbit 1s linear infinite; }
    .loading-icon { position: relative; z-index: 2; width: 48%; aspect-ratio: 1; display: grid; place-items: center; border: 2px solid rgba(255, 255, 255, .7); border-radius: 28px; color: #fff; background: #0369a1; }
    .loading-icon i { font-size: clamp(3.6rem, 9vw, 5.5rem); line-height: 1; }
    .loading-status { min-height: 1.65rem; margin: .2rem 0 .75rem; color: #fff; font-size: clamp(.95rem, 2.4vw, 1.08rem); font-weight: 800; letter-spacing: -.01em; text-align: center; }
    .loading-stages { width: 100%; display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: .55rem; }
    .loading-stage { position: relative; display: grid; justify-items: center; gap: .42rem; min-width: 0; padding: .7rem .35rem; border: 1px solid rgba(255, 255, 255, .16); border-radius: 16px; color: rgba(255, 255, 255, .48); background: #0f293d; transition: color .2s ease, border-color .2s ease, background .2s ease; }
    .loading-stage i { width: 38px; height: 38px; display: grid; place-items: center; border-radius: 12px; background: #1d3b50; font-size: 1.05rem; transition: .2s ease; }
    .loading-stage span { max-width: 100%; overflow: hidden; font-size: .68rem; font-weight: 750; text-overflow: ellipsis; white-space: nowrap; }
    .loading-stage.is-active { color: #fff; border-color: #38bdf8; background: #075985; }
    .loading-stage.is-active i { color: #082f49; background: #7dd3fc; }
    .loading-stage.is-complete { color: rgba(255, 255, 255, .82); border-color: rgba(52, 211, 153, .28); }
    .loading-stage.is-complete i { color: #064e3b; background: #6ee7b7; }
    @keyframes fadeUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: none; } }
    @keyframes loadingOrbit { to { transform: rotate(360deg); } }
    @keyframes pinPulse { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-5px); } }
    @media (max-width: 767.98px) {
        .survey-hero { display: block; }
        .wizard-steps, .hotel-choice { grid-template-columns: 1fr; }
        .wizard-actions { flex-direction: column-reverse; }
        .wizard-actions .btn { width: 100%; }
        .loading-content { width: min(100%, 360px); }
        .loading-visual { width: min(58vw, 210px); }
        .loading-stages { gap: .35rem; }
        .loading-stage { padding: .58rem .2rem; border-radius: 13px; }
        .loading-stage i { width: 34px; height: 34px; border-radius: 10px; }
        .loading-stage span { font-size: .59rem; }
    }
    @media (prefers-reduced-motion: reduce) {
        .loading-orbit { animation-duration: 2.5s; }
    }
</style>
@endpush

@section('content')
<div class="container survey-page">
    <div class="survey-shell">
        <div class="survey-hero">
            <div>
                <h1 class="survey-title">Survei Preferensi Wisata</h1>
                <p class="survey-subtitle mb-0">Nilai destinasi, masukkan rentang budget, lalu izinkan lokasi jika ingin sistem mempertimbangkan jarak dari posisi Anda.</p>
            </div>
        </div>

        <div class="wizard-steps">
            <div class="wizard-step is-active" data-step-label="1"><span>1</span> Preferensi Wisata</div>
            <div class="wizard-step" data-step-label="2"><span>2</span> Budget dan Hotel</div>
            <div class="wizard-step" data-step-label="3"><span>3</span> Lokasi Anda</div>
        </div>

        <form id="surveyWizardForm" method="POST" action="{{ route('wisatawan.survey.store') }}">
            @csrf

            <section class="wizard-panel is-active" data-step="1">
                <div class="wizard-panel-head">
                    <h2>Preferensi Wisata</h2>
                    <p>Beri rating 1-5 untuk seluruh 10 destinasi.</p>
                </div>
                <div class="wizard-panel-body">
                    <div id="preferenceClientError" class="alert alert-warning preference-feedback" role="alert"></div>
                    <div class="row g-4">
                        @foreach ($wisata as $index => $item)
                            <div class="col-md-6 col-xl-4">
                                <article class="rating-card" style="animation-delay: {{ ($index % 3) * 70 }}ms">
                                    <div class="rating-media">
                                        @if($item->foto_url)
                                            <img src="{{ $item->foto_url }}" alt="{{ $item->nama_wisata }}" loading="lazy">
                                        @else
                                            <div class="rating-empty"><i class="bi bi-image fs-2"></i></div>
                                        @endif
                                    </div>
                                    <div class="rating-body">
                                        <span class="category-chip">{{ $item->kategoriWisata->nama_kategori }}</span>
                                        <h3>{{ $item->nama_wisata }}</h3>
                                        <p class="small text-muted mb-3">{{ $item->jenis_wisata }}</p>
                                        <input type="hidden" name="ratings[{{ $index }}][wisata_id]" value="{{ $item->id }}">
                                        <label class="form-label small fw-bold">Nilai minat</label>
                                        <select class="form-select rating-input" name="ratings[{{ $index }}][rating_awal]" required>
                                            <option value="">Pilih rating</option>
                                            @for ($rating = 1; $rating <= 5; $rating++)
                                                <option value="{{ $rating }}" @selected(old("ratings.$index.rating_awal", $savedRatings->get($item->id)) == $rating)>{{ $rating }} - {{ [1 => 'Sangat Tidak Tertarik', 2 => 'Tidak Tertarik', 3 => 'Cukup Tertarik', 4 => 'Tertarik', 5 => 'Sangat Tertarik'][$rating] }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </article>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="wizard-panel" data-step="2">
                <div class="wizard-panel-head">
                    <h2>Budget dan Hotel</h2>
                    <p>Masukkan rentang budget total yang Anda siapkan. Jika memilih hotel, sistem menghitung biaya wisata ditambah harga hotel.</p>
                </div>
                <div class="wizard-panel-body">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="budget-card h-100">
                                <label class="form-label fw-bold">Budget minimum</label>
                                <input type="number" min="0" step="1000" class="form-control mb-3" name="budget_min" value="{{ $formValues['budget_min'] }}" placeholder="Budget minimum (opsional)">
                                <label class="form-label fw-bold">Budget maksimum</label>
                                <input type="number" min="0" step="1000" class="form-control" name="budget_max" value="{{ $formValues['budget_max'] }}" placeholder="Budget maksimum (opsional)">
                                <div class="form-text mt-2">Budget maksimum menjadi batas total yang tidak akan dilampaui rekomendasi.</div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="budget-card h-100">
                                <label class="form-label fw-bold">Kebutuhan hotel</label>
                                <div class="hotel-choice mb-3">
                                    <label class="choice-card">
                                        <input type="radio" name="butuh_hotel" value="0" @checked($formValues['butuh_hotel'] === '0')>
                                        <strong>Tidak membutuhkan hotel</strong>
                                        <small>Budget hanya dihitung dari estimasi wisata.</small>
                                    </label>
                                    <label class="choice-card">
                                        <input type="radio" name="butuh_hotel" value="1" @checked($formValues['butuh_hotel'] === '1')>
                                        <strong>Membutuhkan hotel</strong>
                                        <small>Budget ditambah harga hotel terkait.</small>
                                    </label>
                                </div>
                                <div id="nightField" style="display:none">
                                    <label class="form-label fw-bold">Jumlah malam</label>
                                    <input type="number" min="1" max="30" class="form-control" name="jumlah_malam" value="{{ $formValues['jumlah_malam'] }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="wizard-panel" data-step="3">
                <div class="wizard-panel-head">
                    <h2>Lokasi Anda</h2>
                    <p>Lokasi digunakan untuk menghitung jarak dari posisi Anda ke destinasi wisata. Sistem tetap dapat berjalan jika lokasi dilewati.</p>
                </div>
                <div class="wizard-panel-body">
                    <div class="location-box">
                        <div class="d-flex flex-wrap align-items-center gap-3">
                            <div class="location-icon"><i class="bi bi-geo-alt-fill"></i></div>
                            <div class="flex-grow-1">
                                <h3 class="h5 fw-bold mb-1">Gunakan lokasi untuk rekomendasi lebih realistis</h3>
                                <p id="locationStatus" class="text-muted mb-0">Belum ada lokasi yang diambil.</p>
                            </div>
                            <button class="btn btn-primary" type="button" id="useLocationBtn"><i class="bi bi-crosshair me-1"></i>Gunakan Lokasi Saya</button>
                            <button class="btn btn-outline-secondary" type="button" id="skipLocationBtn">Lewati Lokasi</button>
                        </div>
                        <div class="survey-map" id="surveyMapWrapper" hidden>
                            <iframe id="surveyLocationMap" title="Preview titik lokasi Anda di Google Maps" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>

                    <input type="hidden" name="user_latitude" id="userLatitude" value="{{ $formValues['user_latitude'] }}">
                    <input type="hidden" name="user_longitude" id="userLongitude" value="{{ $formValues['user_longitude'] }}">
                    <input type="hidden" name="is_location_allowed" id="isLocationAllowed" value="{{ $formValues['is_location_allowed'] }}">
                </div>
            </section>

            <div class="wizard-actions">
                <button class="btn btn-light" type="button" id="backStepBtn" disabled><i class="bi bi-arrow-left me-1"></i>Kembali</button>
                <button class="btn btn-primary" type="button" id="nextStepBtn">Lanjutkan <i class="bi bi-arrow-right ms-1"></i></button>
                <button class="btn btn-warning d-none" type="submit" id="submitSurveyBtn"><i class="bi bi-stars me-1"></i>Proses Rekomendasi</button>
            </div>
        </form>
    </div>
</div>

<div class="recommend-loading" id="recommendLoading" role="status" aria-live="polite" aria-label="Memproses rekomendasi" aria-hidden="true">
    <div class="loading-content">
        <div class="loading-visual" aria-hidden="true">
            <span class="loading-orbit"></span>
            <span class="loading-icon"><i class="bi bi-compass-fill"></i></span>
        </div>
        <p class="loading-status" id="loadingStatus">Membaca preferensi...</p>
        <div class="loading-stages" aria-hidden="true">
            <div class="loading-stage is-active" data-loading-stage="0"><i class="bi bi-sliders"></i><span>Preferensi</span></div>
            <div class="loading-stage" data-loading-stage="1"><i class="bi bi-stars"></i><span>Kecocokan</span></div>
            <div class="loading-stage" data-loading-stage="2"><i class="bi bi-wallet2"></i><span>Biaya</span></div>
            <div class="loading-stage" data-loading-stage="3"><i class="bi bi-trophy-fill"></i><span>Hasil</span></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@vite('resources/js/survey-location.js')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        let currentStep = {{ $initialStep }};
        const panels = document.querySelectorAll('.wizard-panel');
        const labels = document.querySelectorAll('.wizard-step');
        const backBtn = document.getElementById('backStepBtn');
        const nextBtn = document.getElementById('nextStepBtn');
        const submitBtn = document.getElementById('submitSurveyBtn');
        const form = document.getElementById('surveyWizardForm');
        const nightField = document.getElementById('nightField');
        const hotelInputs = document.querySelectorAll('input[name="butuh_hotel"]');
        const loading = document.getElementById('recommendLoading');
        const loadingStatus = document.getElementById('loadingStatus');
        const loadingStageElements = [...document.querySelectorAll('[data-loading-stage]')];
        const preferenceClientError = document.getElementById('preferenceClientError');
        let loadingTimer = null;

        const setStep = (step) => {
            currentStep = step;
            panels.forEach((panel) => panel.classList.toggle('is-active', Number(panel.dataset.step) === step));
            labels.forEach((label) => label.classList.toggle('is-active', Number(label.dataset.stepLabel) === step));
            backBtn.disabled = step === 1;
            nextBtn.classList.toggle('d-none', step === 3);
            submitBtn.classList.toggle('d-none', step !== 3);
        };

        const hotelNeeded = () => document.querySelector('input[name="butuh_hotel"]:checked')?.value === '1';
        const updateNight = () => nightField.style.display = hotelNeeded() ? 'block' : 'none';
        hotelInputs.forEach((input) => input.addEventListener('change', updateNight));
        updateNight();

        const validateStep = () => {
            if (currentStep === 1) {
                const ratingInputs = [...document.querySelectorAll('.rating-input')];
                const missing = ratingInputs.some((input) => !input.value);
                if (missing) {
                    preferenceClientError.textContent = 'Semua 10 destinasi harus diberi rating.';
                    preferenceClientError.classList.add('is-visible');
                    return false;
                }

                const ratings = ratingInputs.map((input) => Number(input.value));
                const allLow = ratings.every((rating) => rating >= 1 && rating <= 2);
                const allMiddle = ratings.every((rating) => rating === 3);
                if (allLow || allMiddle) {
                    preferenceClientError.textContent = allLow
                        ? 'Pilih minimal satu destinasi dengan nilai 3–5 agar minat Anda dapat dikenali.'
                        : 'Rating masih terlalu seragam. Berikan nilai berbeda pada beberapa destinasi.';
                    preferenceClientError.classList.add('is-visible');
                    return false;
                }

                preferenceClientError.classList.remove('is-visible');
            }
            if (currentStep === 2) {
                const minRaw = document.querySelector('[name="budget_min"]').value.trim();
                const maxRaw = document.querySelector('[name="budget_max"]').value.trim();
                const min = minRaw === '' ? null : Number(minRaw);
                const max = maxRaw === '' ? null : Number(maxRaw);
                if ((min !== null && (!Number.isFinite(min) || min < 0))
                    || (max !== null && (!Number.isFinite(max) || max < 0))
                    || (min !== null && max !== null && max < min)) {
                    alert('Periksa kembali rentang budget Anda. Kedua nilai boleh dikosongkan.');
                    return false;
                }
                if (hotelNeeded() && Number(document.querySelector('[name="jumlah_malam"]').value || 0) < 1) {
                    alert('Jumlah malam wajib diisi jika membutuhkan hotel.');
                    return false;
                }
            }
            return true;
        };

        nextBtn.addEventListener('click', () => {
            if (validateStep()) setStep(Math.min(3, currentStep + 1));
        });
        backBtn.addEventListener('click', () => setStep(Math.max(1, currentStep - 1)));

        form.addEventListener('submit', (event) => {
            if (!validateStep()) {
                event.preventDefault();
                return;
            }
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span><span class="visually-hidden">Memproses</span>';
            loading.setAttribute('aria-hidden', 'false');
            loading.classList.add('is-visible');

            const stages = [
                'Membaca preferensi...',
                'Mencari destinasi cocok...',
                hotelNeeded() ? 'Menghitung wisata & hotel...' : 'Menyesuaikan budget...',
                'Menyiapkan hasil...'
            ];
            let activeStage = 0;
            const showLoadingStage = () => {
                loadingStatus.textContent = stages[activeStage];
                loadingStageElements.forEach((stage, index) => {
                    stage.classList.toggle('is-active', index === activeStage);
                    stage.classList.toggle('is-complete', index < activeStage);
                });
            };

            showLoadingStage();
            loadingTimer = window.setInterval(() => {
                if (activeStage < stages.length - 1) {
                    activeStage += 1;
                    showLoadingStage();
                } else {
                    window.clearInterval(loadingTimer);
                    loadingTimer = null;
                }
            }, 850);
        });

        window.addEventListener('pageshow', () => {
            if (loadingTimer !== null) {
                window.clearInterval(loadingTimer);
                loadingTimer = null;
            }
            loading.classList.remove('is-visible');
            loading.setAttribute('aria-hidden', 'true');
            loadingStatus.textContent = 'Membaca preferensi...';
            loadingStageElements.forEach((stage, index) => {
                stage.classList.toggle('is-active', index === 0);
                stage.classList.remove('is-complete');
            });
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-stars me-1"></i>Proses Rekomendasi';
        });

        setStep(currentStep);
    });
</script>
@endpush
