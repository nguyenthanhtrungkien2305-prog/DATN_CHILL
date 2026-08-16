<?php

if (!function_exists('format_image_url')) {
    function format_image_url(?string $url, string $default = '/images/logo1.png'): string {
        if (empty($url)) {
            return asset($default);
        }
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }
        $clean = ltrim($url, '/');
        if (str_starts_with($clean, 'storage/') || str_starts_with($clean, 'images/')) {
            return asset($clean);
        }
        return asset('storage/' . $clean);
    }
}
