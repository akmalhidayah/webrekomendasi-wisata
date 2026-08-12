import {
    clearLocation,
    getCurrentLocation,
    isLocationValid,
    saveLocation,
} from './location-manager.js';

export function buildGoogleMapsEmbedUrl(location) {
    if (!isLocationValid(location)) return null;

    const lat = Number(location.lat).toFixed(7);
    const lng = Number(location.lng).toFixed(7);
    const query = encodeURIComponent(`${lat},${lng}`);

    return `https://www.google.com/maps?q=${query}&z=16&output=embed`;
}

export function initializeSurveyLocation(documentRef = document) {
    const useButton = documentRef.getElementById('useLocationBtn');
    const skipButton = documentRef.getElementById('skipLocationBtn');
    const status = documentRef.getElementById('locationStatus');
    const latitude = documentRef.getElementById('userLatitude');
    const longitude = documentRef.getElementById('userLongitude');
    const allowed = documentRef.getElementById('isLocationAllowed');
    const mapWrapper = documentRef.getElementById('surveyMapWrapper');
    const map = documentRef.getElementById('surveyLocationMap');

    if (!useButton || !skipButton || !status || !latitude || !longitude || !allowed || !mapWrapper || !map) {
        return;
    }

    const hideMap = () => {
        map.removeAttribute('src');
        mapWrapper.hidden = true;
    };

    const clearFields = () => {
        latitude.value = '';
        longitude.value = '';
        allowed.value = '0';
        hideMap();
    };

    const showLocation = (location) => {
        const embedUrl = buildGoogleMapsEmbedUrl(location);
        if (!embedUrl) {
            clearFields();
            return false;
        }

        latitude.value = Number(location.lat).toFixed(7);
        longitude.value = Number(location.lng).toFixed(7);
        allowed.value = '1';
        map.src = embedUrl;
        mapWrapper.hidden = false;
        status.textContent = 'Lokasi Anda berhasil terdeteksi.';
        useButton.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i>Perbarui Lokasi';

        return true;
    };

    const restored = {
        lat: latitude.value,
        lng: longitude.value,
    };
    if (allowed.value === '1' && isLocationValid(restored)) {
        showLocation(restored);
    } else {
        clearFields();
        status.textContent = 'Belum ada lokasi yang diambil.';
    }

    useButton.addEventListener('click', async () => {
        useButton.disabled = true;
        status.textContent = 'Mengambil lokasi...';

        try {
            const position = await getCurrentLocation();
            const location = {
                lat: position.coords.latitude,
                lng: position.coords.longitude,
            };

            if (!showLocation(location)) {
                throw new Error('Koordinat lokasi tidak valid.');
            }

            saveLocation(location);
        } catch (error) {
            clearFields();
            clearLocation();
            status.textContent = 'Lokasi tidak dapat digunakan. Rekomendasi tetap dapat diproses tanpa mempertimbangkan jarak.';
            useButton.innerHTML = '<i class="bi bi-crosshair me-1"></i>Coba Lagi';
            console.warn('Lokasi survei tidak dapat digunakan.', error);
        } finally {
            useButton.disabled = false;
        }
    });

    skipButton.addEventListener('click', () => {
        clearFields();
        clearLocation();
        status.textContent = 'Lokasi dilewati. Rekomendasi tetap dapat diproses tanpa mempertimbangkan jarak.';
        useButton.innerHTML = '<i class="bi bi-crosshair me-1"></i>Gunakan Lokasi Saya';
    });
}

if (typeof document !== 'undefined' && document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => initializeSurveyLocation(), { once: true });
} else if (typeof document !== 'undefined') {
    initializeSurveyLocation();
}
