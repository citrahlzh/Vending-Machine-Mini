<?php

use App\Models\Machine;
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

if (!function_exists('current_machine')) {
    function current_machine(): ?array
    {
        return cache()->rememberForever('current_machine', function () {
            return Machine::query()
                ->latest('id')
                ->first()?->toArray();
        });
    }
}

if (!function_exists('machine_setting')) {
    function machine_setting($key, $default = null)
    {
        $machine = current_machine();
        if (is_array($machine) && array_key_exists($key, $machine) && $machine[$key] !== null && $machine[$key] !== '') {
            return $machine[$key];
        }

        $legacyMap = [
            'name' => 'machine_name',
            'code' => 'machine_code',
            'serial_number' => 'machine_serial_number',
            'location' => 'machine_location',
            'operator_name' => 'machine_operator_name',
        ];

        if (isset($legacyMap[$key])) {
            return setting($legacyMap[$key], $default);
        }

        return $default;
    }
}

if (!function_exists('machine_asset_url')) {
    function machine_asset_url($key = 'photo_url', $default = null)
    {
        $value = machine_setting($key);

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
