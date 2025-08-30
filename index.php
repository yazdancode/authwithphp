<?php
require __DIR__ . '/config/init.php';

// لیست صفحات مجاز
$allowedPages = ['login', 'register', 'verify'];

// دریافت مسیر
$url = $_GET['url'] ?? 'login';
$url = trim($url, '/');

// بررسی امنیتی
if (!in_array($url, $allowedPages)) {
    $url = 'not-found';
}

// مسیر فایل
$file = __DIR__ . "/template/$url.php";

// اجرا
require $file;
