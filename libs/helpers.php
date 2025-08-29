<?php

use JetBrains\PhpStorm\NoReturn;

if (!function_exists('baseUrl')) {
    function baseUrl(): string
    {
        return defined('BASE_URL') ? rtrim(BASE_URL, '/') . '/' : '';
    }
}

if (!function_exists('assets')) {
    function assets(string $path): string
    {
        return baseUrl() . 'assets/' . ltrim($path, '/');
    }
}

if (!function_exists('site_url')) {
    function site_url(string $uri = ''): string
    {
        return baseUrl() . ltrim($uri, '/');
    }
}

if (!function_exists('redirect')) {
    #[NoReturn]
    function redirect(string $target = BASE_URL): void
    {
        header('Location: ' . $target);
        exit;
    }
}

if (!function_exists('setFlashMessage')) {
    function setFlashMessage(string $type, string $message): void
    {
        $_SESSION[$type] = $message;
    }
}

if (!function_exists('setErrorAndRedirect')) {
    #[NoReturn]
    function setErrorAndRedirect(string $message, string $target): void
    {
        setFlashMessage('error', $message);
        redirect(site_url($target));
    }
}

if (!function_exists('displayAlert')) {
    function displayAlert(string $type, string $message): void
    {
        echo '<div class="alert alert-' . htmlspecialchars($type) . ' alert-dismissible fade show" role="alert">'
            . htmlspecialchars($message) .
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="بستن"></button>
        </div>';
    }
}

if (!function_exists('displayFlashMessage')) {
    function displayFlashMessage(): void
    {
        foreach (['error' => 'danger', 'success' => 'success'] as $key => $type) {
            if (!empty($_SESSION[$key])) {
                displayAlert($type, $_SESSION[$key]);
                unset($_SESSION[$key]);
            }
        }
    }
}
