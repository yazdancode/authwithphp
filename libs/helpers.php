<?php
if (!function_exists('assets')) {
    function assets(string $path): string
    {
        $base = defined('BASE_URL') ? rtrim(BASE_URL, '/') . '/' : '';
        return $base . 'assets/' . ltrim($path, '/');
    }
}

if (!function_exists('site_url')) {
    function site_url(string $uri = ''): string
    {
        $base = defined('BASE_URL') ? rtrim(BASE_URL, '/') . '/' : '';
        return $base . ltrim($uri, '/');
    }
}

