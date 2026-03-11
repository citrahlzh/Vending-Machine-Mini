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
