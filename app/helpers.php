<?php

if (!function_exists('format_image_url')) {
    function format_image_url(?string $url, string $default = '/images/logo1.png'): string {
        if (empty($url)) {
            return '/' . ltrim($default, '/');
        }
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }
        $clean = ltrim($url, '/');
        return '/' . $clean;
    }
}
