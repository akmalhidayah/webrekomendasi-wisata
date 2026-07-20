export const LOCATION_STORAGE_KEY = 'wisataUserLocation';
export const LOCATION_REDIRECT_GUARD_KEY = 'wisataLocationRedirectAttempted';
export const LOCATION_TTL_MS = 30 * 60 * 1000;

export function isLocationValid(location) {
    if (!location) return false;

    const lat = Number(location.lat);
    const lng = Number(location.lng);

    return Number.isFinite(lat) && lat >= -90 && lat <= 90
        && Number.isFinite(lng) && lng >= -180 && lng <= 180;
}

export function isLocationExpired(location, now = Date.now()) {
    const storedAt = Number(location?.storedAt);

    return !Number.isFinite(storedAt) || storedAt > now || now - storedAt > LOCATION_TTL_MS;
}

export function clearLocation() {
    try {
        localStorage.removeItem(LOCATION_STORAGE_KEY);
        sessionStorage.removeItem(LOCATION_REDIRECT_GUARD_KEY);
    } catch (error) {
        console.warn('Penyimpanan lokasi browser tidak dapat dibersihkan.', error);
    }
}

export function getStoredLocation(now = Date.now()) {
    let rawValue;

    try {
        rawValue = localStorage.getItem(LOCATION_STORAGE_KEY);
        if (!rawValue) return null;

        const parsed = JSON.parse(rawValue);
        if (!isLocationValid(parsed) || isLocationExpired(parsed, now)) {
            localStorage.removeItem(LOCATION_STORAGE_KEY);
            return null;
        }

        return { lat: Number(parsed.lat), lng: Number(parsed.lng), storedAt: Number(parsed.storedAt) };
    } catch (error) {
        try { localStorage.removeItem(LOCATION_STORAGE_KEY); } catch (storageError) {
            console.warn('Penyimpanan lokasi browser tidak tersedia.', storageError);
        }
        console.warn('Data lokasi tersimpan rusak dan telah diabaikan.', error);
        return null;
    }
}

export function saveLocation(location, now = Date.now()) {
    if (!isLocationValid(location)) return null;

    const saved = { lat: Number(location.lat), lng: Number(location.lng), storedAt: now };
    try {
        localStorage.setItem(LOCATION_STORAGE_KEY, JSON.stringify(saved));
    } catch (error) {
        console.warn('Lokasi tidak dapat disimpan di browser.', error);
    }

    return saved;
}

export function getCurrentLocation(geolocation = navigator.geolocation) {
    return new Promise((resolve, reject) => {
        if (!geolocation) {
            reject(new Error('Browser tidak mendukung geolocation.'));
            return;
        }

        geolocation.getCurrentPosition(resolve, reject, {
            enableHighAccuracy: false,
            timeout: 10000,
            maximumAge: 300000,
        });
    });
}

function locationFromUrl(url) {
    if (!url.searchParams.has('lat') || !url.searchParams.has('lng')) return null;

    const location = { lat: url.searchParams.get('lat'), lng: url.searchParams.get('lng') };
    return isLocationValid(location) ? location : null;
}

export function applyLocationToUrlOnce(location, currentUrl = new URL(window.location.href)) {
    if (!isLocationValid(location)) return false;

    try {
        if (sessionStorage.getItem(LOCATION_REDIRECT_GUARD_KEY)) {
            console.warn('Redirect lokasi dihentikan karena query koordinat tidak dipertahankan.');
            sessionStorage.setItem(LOCATION_REDIRECT_GUARD_KEY, 'failed');
            return false;
        }
        sessionStorage.setItem(LOCATION_REDIRECT_GUARD_KEY, '1');
    } catch (error) {
        console.warn('Guard redirect lokasi tidak tersedia; redirect otomatis dibatalkan.', error);
        return false;
    }

    currentUrl.searchParams.set('lat', String(location.lat));
    currentUrl.searchParams.set('lng', String(location.lng));
    window.location.replace(currentUrl.toString());
    return true;
}

function setButtonState(button, state) {
    button.disabled = state === 'loading';
    if (state === 'loading') button.textContent = 'Mengambil lokasi...';
    if (state === 'error') button.textContent = 'Lokasi gagal diambil — coba lagi';
}

function initializeLocationManager() {
    const url = new URL(window.location.href);
    const urlLocation = locationFromUrl(url);

    if (urlLocation) {
        saveLocation(urlLocation);
        try { sessionStorage.removeItem(LOCATION_REDIRECT_GUARD_KEY); } catch (error) {
            console.warn('Guard redirect lokasi tidak dapat dibersihkan.', error);
        }
    } else {
        const storedLocation = getStoredLocation();
        if (storedLocation && applyLocationToUrlOnce(storedLocation, url)) return;
    }

    document.querySelectorAll('.wisata-location-trigger, .js-home-location').forEach((button) => {
        button.addEventListener('click', async () => {
            setButtonState(button, 'loading');
            let navigating = false;
            try {
                const position = await getCurrentLocation();
                const location = saveLocation({
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                });
                try { sessionStorage.removeItem(LOCATION_REDIRECT_GUARD_KEY); } catch (error) {
                    console.warn('Guard redirect lokasi tidak dapat direset.', error);
                }
                navigating = applyLocationToUrlOnce(location, new URL(window.location.href));
                if (!navigating) setButtonState(button, 'error');
            } catch (error) {
                console.warn('Lokasi tidak tersedia; halaman tetap menggunakan urutan destinasi umum.', error);
                setButtonState(button, 'error');
            } finally {
                if (!navigating && !document.hidden) button.disabled = false;
            }
        });
    });

    document.querySelectorAll('[data-location-clear]').forEach((button) => {
        button.addEventListener('click', () => {
            clearLocation();
            const cleanUrl = new URL(window.location.href);
            cleanUrl.searchParams.delete('lat');
            cleanUrl.searchParams.delete('lng');
            window.location.replace(cleanUrl.toString());
        });
    });
}

if (typeof document !== 'undefined' && document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeLocationManager, { once: true });
} else if (typeof document !== 'undefined') {
    initializeLocationManager();
}
