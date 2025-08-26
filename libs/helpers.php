<?php
if (!function_exists('assets')) {
    function assets(string $path): string
    {
        $base = defined('BASE_URL') ? BASE_URL : '';
        return $base . 'assets/' . $path;
    }
}

// $result = assets('js/script.js');

// echo $result;

