<?php
require 'config/init.php';

// نمایش خطاها برای دیباگ
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// گرفتن پارامتر action
$page = $_GET['action'] ?? 'login';

// انتخاب فایل مناسب بر اساس action
switch ($page) {
    case 'register':
        $file = 'template/register.php';
        break;
    case 'verify':
        $file = 'template/verify.php';
        break;
    case 'login':
    default:
        $file = 'template/login.php';
        break;
}

// بررسی وجود فایل و include کردن
if (file_exists($file)) {
    include $file;
} else {
    echo "صفحه مورد نظر یافت نشد!";
}
