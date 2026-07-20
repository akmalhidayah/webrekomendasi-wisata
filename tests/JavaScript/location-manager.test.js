import test from 'node:test';
import assert from 'node:assert/strict';
import {
    LOCATION_TTL_MS,
    getCurrentLocation,
    getStoredLocation,
    isLocationExpired,
    isLocationValid,
} from '../../resources/js/location-manager.js';

test('coordinate validation accepts zero and rejects out-of-range values', () => {
    assert.equal(isLocationValid({ lat: 0, lng: 0 }), true);
    assert.equal(isLocationValid({ lat: -90, lng: 180 }), true);
    assert.equal(isLocationValid({ lat: 90.01, lng: 119 }), false);
    assert.equal(isLocationValid({ lat: -5, lng: 180.01 }), false);
    assert.equal(isLocationValid({ lat: 'broken', lng: 119 }), false);
});

test('corrupt local storage is removed without throwing', () => {
    let removed = false;
    global.localStorage = {
        getItem: () => '{broken-json',
        removeItem: () => { removed = true; },
    };

    assert.equal(getStoredLocation(), null);
    assert.equal(removed, true);
    delete global.localStorage;
});

test('geolocation denial rejects without triggering navigation', async () => {
    const deniedGeolocation = {
        getCurrentPosition: (success, error) => error(new Error('permission denied')),
    };

    await assert.rejects(getCurrentLocation(deniedGeolocation), /permission denied/);
});

test('stored location expires after thirty minutes', () => {
    const now = 2_000_000;
    assert.equal(isLocationExpired({ storedAt: now - LOCATION_TTL_MS }, now), false);
    assert.equal(isLocationExpired({ storedAt: now - LOCATION_TTL_MS - 1 }, now), true);
    assert.equal(isLocationExpired({ storedAt: 'broken' }, now), true);
});
