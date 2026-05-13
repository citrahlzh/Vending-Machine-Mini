<?php

use App\Models\SiteSetting;

if (!function_exists('setting')) {
    function setting($key, $default = null)
    {
        $settings = cache()->rememberForever('site_settings', function () {
            return SiteSetting::pluck('value', 'key');
        });

        return $settings[$key] ?? $default;
    }
}

if (!function_exists('setting_asset_url')) {
    function setting_asset_url($key, $default = null)
    {
        $value = setting($key);

        if (blank($value)) {
            return $default;
        }

        $normalizedValue = trim((string) $value);

        if (filter_var($normalizedValue, FILTER_VALIDATE_URL)) {
            $path = parse_url($normalizedValue, PHP_URL_PATH) ?: '';

            if (str_starts_with($path, '/storage/')) {
                return asset('image/' . ltrim(substr($path, strlen('/storage/')), '/'));
            }

            return $normalizedValue;
        }

        if (str_starts_with($normalizedValue, '/storage/')) {
            return asset('image/' . ltrim(substr($normalizedValue, strlen('/storage/')), '/'));
        }

        if (str_starts_with($normalizedValue, 'storage/')) {
            return asset('image/' . ltrim(substr($normalizedValue, strlen('storage/')), '/'));
        }

        if (str_starts_with($normalizedValue, '/image/')) {
            return asset(ltrim($normalizedValue, '/'));
        }

        if (str_starts_with($normalizedValue, 'image/')) {
            return asset($normalizedValue);
        }

        return asset('image/' . ltrim($normalizedValue, '/'));
    }
}
