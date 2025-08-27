<?php
// تابع برای مسیر فایل‌های assets
if (!function_exists('assets')) {
    function assets(string $path): string
    {
        $base = defined('BASE_URL') ? rtrim(BASE_URL, '/') . '/' : '';
        return $base . 'assets/' . ltrim($path, '/');
    }
}

// تابع برای مسیر صفحات سایت
if (!function_exists('site_url')) {
    function site_url(string $uri = ''): string
    {
        $base = defined('BASE_URL') ? rtrim(BASE_URL, '/') . '/' : '';
        return $base . ltrim($uri, '/');
    }
}

// تابع برای هدایت کاربر به صفحه دیگر
if (!function_exists('redirect')) {
    function redirect(string $target = BASE_URL)
    {
        header('Location: ' . $target);
        exit;
    }
}

// تابع برای ذخیره پیام خطا در سشن و هدایت کاربر
if (!function_exists('setErrorAndRedirect')) {
    function setErrorAndRedirect(string $message, string $target)
    {
        $_SESSION['error'] = $message;
        redirect(site_url($target));
    }
}

// تابع برای نمایش پیام خطا یا موفقیت از سشن و پاک کردن آن
if (!function_exists('displayFlashMessage')) {
    function displayFlashMessage()
    {
        if (!empty($_SESSION['error'])) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">'
                . $_SESSION['error'] .
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="بستن"></button>
                </div>';
            unset($_SESSION['error']);
        }

        if (!empty($_SESSION['success'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">'
                . $_SESSION['success'] .
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="بستن"></button>
                </div>';
            unset($_SESSION['success']);
        }
    }
}