<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaUrl
{
    public static function fromPublicDisk(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            $appHost = parse_url((string) config('app.url'), PHP_URL_HOST);
            $pathHost = parse_url($path, PHP_URL_HOST);

            if (config('app.force_https') && $appHost && $pathHost === $appHost) {
                return preg_replace('/^http:\/\//i', 'https://', $path);
            }

            return $path;
        }

        return Storage::disk('public')->url(ltrim($path, '/'));
    }
}
