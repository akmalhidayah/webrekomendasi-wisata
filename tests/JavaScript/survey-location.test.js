import test from 'node:test';
import assert from 'node:assert/strict';
import { buildGoogleMapsEmbedUrl } from '../../resources/js/survey-location.js';

test('valid coordinates produce a Google Maps embed URL', () => {
    assert.equal(
        buildGoogleMapsEmbedUrl({ lat: -5.1477, lng: 119.4327 }),
        'https://www.google.com/maps?q=-5.1477000%2C119.4327000&z=16&output=embed',
    );
});

test('invalid coordinates do not produce a map URL', () => {
    assert.equal(buildGoogleMapsEmbedUrl({ lat: 91, lng: 119.4 }), null);
    assert.equal(buildGoogleMapsEmbedUrl({ lat: -5.1, lng: 'broken' }), null);
    assert.equal(buildGoogleMapsEmbedUrl(null), null);
});
