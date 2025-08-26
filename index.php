<?php
// لود تنظیمات و هلسپرها
require __DIR__ . '/config/init.php';

// گرفتن مسیر URL
$url = $_GET['url'] ?? 'login';
$url = trim($url, '/');

// مسیر فایل template
$file = __DIR__ . "/template/{$url}.php";

if (file_exists($file)) {
    require $file;
} else {
    echo "صفحه پیدا نشد!";
}
