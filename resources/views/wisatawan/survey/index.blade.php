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
    .wizard-actions { display: flex; justify-content: space-between; gap: .8rem; margin-top: 1.2rem; }
    .wizard-actions .btn { min-height: 46px; border-radius: 14px; font-weight: 850; }
    .recommend-loading { position: fixed; inset: 0; z-index: 3000; display: none; place-items: center; padding: 1rem; background: rgba(8, 24, 38, .58); backdrop-filter: blur(14px); }
    .recommend-loading.is-visible { display: grid; }
    .loading-card { width: min(760px, 100%); padding: 1.3rem; border-radius: 28px; background: #fff; box-shadow: 0 28px 70px rgba(2, 8, 23, .24); }
    .loading-spinner { width: 64px; height: 64px; border: 5px solid #e0f2fe; border-top-color: #0369a1; border-radius: 999px; animation: spin .9s linear infinite; }
    .loading-steps { display: grid; gap: .45rem; margin: 1rem 0; }
    .loading-step { display: flex; align-items: center; gap: .55rem; color: #475569; font-size: .86rem; }
    .loading-step i { color: #0369a1; }
    .loading-progress { height: 8px; overflow: hidden; border-radius: 999px; background: #e2e8f0; }
    .loading-progress span { display: block; width: 35%; height: 100%; border-radius: inherit; background: linear-gradient(90deg, #0369a1, #f59e0b); animation: progressMove 1.4s ease-in-out infinite; }
    .skeleton-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: .7rem; margin-top: 1rem; }
    .skeleton-card { height: 92px; border-radius: 18px; background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 38%, #f1f5f9 63%); background-size: 400% 100%; animation: shimmer 1.3s ease-in-out infinite; }
    @keyframes fadeUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: none; } }
    @keyframes spin { to { transform: rotate(360deg); } }
    @keyframes shimmer { 0% { background-position: 100% 0; } 100% { background-position: 0 0; } }
    @keyframes progressMove { 0% { transform: translateX(-40%); } 100% { transform: translateX(220%); } }
    @keyframes pinPulse { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-5px); } }
    @media (max-width: 767.98px) {
        .survey-hero { display: block; }
        .wizard-steps, .hotel-choice, .skeleton-grid { grid-template-columns: 1fr; }
        .wizard-actions { flex-direction: column-reverse; }
        .wizard-actions .btn { width: 100%; }
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
                                                <option value="{{ $rating }}" @selected(old("ratings.$index.rating_awal") == $rating)>{{ $rating }} - {{ [1 => 'Sangat Tidak Tertarik', 2 => 'Tidak Tertarik', 3 => 'Cukup Tertarik', 4 => 'Tertarik', 5 => 'Sangat Tertarik'][$rating] }}</option>
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
                                <input type="number" min="0" step="1000" class="form-control mb-3" name="budget_min" value="{{ old('budget_min', 100000) }}" required>
                                <label class="form-label fw-bold">Budget maksimum</label>
                                <input type="number" min="0" step="1000" class="form-control" name="budget_max" value="{{ old('budget_max', 1000000) }}" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="budget-card h-100">
                                <label class="form-label fw-bold">Kebutuhan hotel</label>
                                <div class="hotel-choice mb-3">
                                    <label class="choice-card">
                                        <input type="radio" name="butuh_hotel" value="0" @checked(old('butuh_hotel', '0') === '0')>
                                        <strong>Tidak membutuhkan hotel</strong>
                                        <small>Budget hanya dihitung dari estimasi wisata.</small>
                                    </label>
                                    <label class="choice-card">
                                        <input type="radio" name="butuh_hotel" value="1" @checked(old('butuh_hotel') === '1')>
                                        <strong>Membutuhkan hotel</strong>
                                        <small>Budget ditambah harga hotel terkait.</small>
                                    </label>
                                </div>
                                <div id="nightField" style="display:none">
                                    <label class="form-label fw-bold">Jumlah malam</label>
                                    <input type="number" min="1" max="30" class="form-control" name="jumlah_malam" value="{{ old('jumlah_malam', 1) }}">
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
                    </div>

                    <input type="hidden" name="user_latitude" id="userLatitude" value="{{ old('user_latitude') }}">
                    <input type="hidden" name="user_longitude" id="userLongitude" value="{{ old('user_longitude') }}">
                    <input type="hidden" name="is_location_allowed" id="isLocationAllowed" value="{{ old('is_location_allowed', 0) }}">
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

<div class="recommend-loading" id="recommendLoading">
    <div class="loading-card">
        <div class="d-flex align-items-center gap-3">
            <div class="loading-spinner"></div>
            <div>
                <h2 class="h4 fw-bold mb-1">Sedang Menghitung Rekomendasi</h2>
                <p class="text-muted mb-0">Sistem menyusun ranking wisata terbaik dari rating, budget, hotel, dan lokasi Anda.</p>
            </div>
        </div>
        <div class="loading-steps" id="loadingSteps"></div>
        <div class="loading-progress"><span></span></div>
        <div class="skeleton-grid"><div class="skeleton-card"></div><div class="skeleton-card"></div><div class="skeleton-card"></div></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        let currentStep = 1;
        const panels = document.querySelectorAll('.wizard-panel');
        const labels = document.querySelectorAll('.wizard-step');
        const backBtn = document.getElementById('backStepBtn');
        const nextBtn = document.getElementById('nextStepBtn');
        const submitBtn = document.getElementById('submitSurveyBtn');
        const form = document.getElementById('surveyWizardForm');
        const nightField = document.getElementById('nightField');
        const hotelInputs = document.querySelectorAll('input[name="butuh_hotel"]');
        const loading = document.getElementById('recommendLoading');
        const loadingSteps = document.getElementById('loadingSteps');

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
                const missing = [...document.querySelectorAll('.rating-input')].some((input) => !input.value);
                if (missing) {
                    alert('Semua 10 destinasi harus diberi rating.');
                    return false;
                }
            }
            if (currentStep === 2) {
                const min = Number(document.querySelector('[name="budget_min"]').value);
                const max = Number(document.querySelector('[name="budget_max"]').value);
                if (!Number.isFinite(min) || !Number.isFinite(max) || min < 0 || max < min) {
                    alert('Periksa kembali rentang budget Anda.');
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

        document.getElementById('useLocationBtn').addEventListener('click', () => {
            const status = document.getElementById('locationStatus');
            if (!navigator.geolocation) {
                status.textContent = 'Browser tidak mendukung geolocation. Rekomendasi tetap dapat diproses tanpa skor jarak.';
                document.getElementById('isLocationAllowed').value = 0;
                return;
            }
            status.textContent = 'Mengambil lokasi...';
            navigator.geolocation.getCurrentPosition((position) => {
                const lat = position.coords.latitude.toFixed(7);
                const lng = position.coords.longitude.toFixed(7);
                document.getElementById('userLatitude').value = lat;
                document.getElementById('userLongitude').value = lng;
                document.getElementById('isLocationAllowed').value = 1;
                status.textContent = `Lokasi berhasil diambil: ${lat}, ${lng}`;
            }, () => {
                document.getElementById('isLocationAllowed').value = 0;
                status.textContent = 'Lokasi ditolak atau gagal diambil. Rekomendasi tetap dapat diproses tanpa skor jarak.';
            }, { enableHighAccuracy: true, timeout: 10000 });
        });

        document.getElementById('skipLocationBtn').addEventListener('click', () => {
            document.getElementById('userLatitude').value = '';
            document.getElementById('userLongitude').value = '';
            document.getElementById('isLocationAllowed').value = 0;
            document.getElementById('locationStatus').textContent = 'Lokasi dilewati. Sistem akan memakai rating, budget, dan preferensi.';
        });

        form.addEventListener('submit', (event) => {
            if (!validateStep()) {
                event.preventDefault();
                return;
            }
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Sedang Menghitung...';
            const steps = [
                'Membaca rating dan preferensi wisata Anda',
                'Membentuk matriks rating pengguna',
                'Menghitung kemiripan dengan Cosine Similarity',
                'Menyesuaikan rekomendasi dengan rentang budget',
                hotelNeeded() ? 'Menghitung estimasi biaya wisata dan hotel' : 'Menghitung estimasi biaya wisata',
                document.getElementById('isLocationAllowed').value === '1' ? 'Menghitung jarak destinasi dari lokasi Anda' : 'Menggunakan rekomendasi tanpa skor jarak lokasi',
                'Menyusun ranking wisata terbaik'
            ];
            loadingSteps.innerHTML = steps.map((step) => `<div class="loading-step"><i class="bi bi-check-circle-fill"></i>${step}</div>`).join('');
            loading.classList.add('is-visible');
        });
    });
</script>
@endpush
