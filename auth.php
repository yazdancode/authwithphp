<?php

use JetBrains\PhpStorm\NoReturn;

require 'config/init.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ----------------------------------
// توابع کمکی
// ----------------------------------
if (!function_exists('redirect')) {
    #[NoReturn]
    function redirect(string $target = BASE_URL): void
    {
        header('Location: ' . $target);
        exit;
    }
}

if (!function_exists('setErrorAndRedirect')) {
    #[NoReturn]
    function setErrorAndRedirect(string $message, string $target): void
    {
        $_SESSION['error'] = $message;
        redirect($target);
    }
}

// ----------------------------------
// ثبت نام کاربر
// ----------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_GET['action'] ?? '') === 'register') {

    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    // اعتبارسنجی فیلدها
    if (empty($name) || empty($email) || empty($phone)) {
        setErrorAndRedirect('لطفاً همه فیلدها را پر کنید!', 'auth.php?action=register');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setErrorAndRedirect('ایمیل وارد شده معتبر نیست!', 'auth.php?action=register');
    }

    if (userExists($email, $phone)) {
        setErrorAndRedirect('ایمیل یا شماره موبایل وارد شده قبلاً ثبت شده است!', 'auth.php?action=register');
    }

    // ایجاد کاربر
    $userData = ['name' => $name, 'email' => $email, 'phone' => $phone];
    createUser($userData);
}

// ----------------------------------
// بررسی صفحه verify
// ----------------------------------
if (isset($_GET['action']) && $_GET['action'] === 'verify' && !empty($_SESSION['email'])) {

    if (!userExistsByEmail($_SESSION['email'])) {
        setErrorAndRedirect('ابتدا باید ثبت‌نام کنید یا وارد شوید!', 'auth.php?action=login');
    }

    if (isset($_SESSION['hash']) && isAliveToken($_SESSION['hash'])) {
        # پیدا کردن توکن قدیمی
        $oldToken = findTokenByHash($_SESSION['hash']);
        $token = $oldToken['token'];

        # اینجا می‌تونی بفرستی برای کاربر (ایمیل / SMS)
        // sendToken($_SESSION['email'], $token);

    } else {
        # ساخت توکن جدید
        $tokenResult = generateToken();
        $_SESSION['hash']  = $tokenResult['hash'];
        $_SESSION['token'] = $tokenResult['token'];
        # ارسال به کاربر
        // sendToken($_SESSION['email'], $tokenResult['token']);
    }
    include 'template/verify.php';
}



// ----------------------------------
// بارگذاری صفحه مناسب
// ----------------------------------
$page = $_GET['action'] ?? 'login';
$file = match ($page) {
    'register' => 'template/register.php',
    'verify' => 'template/verify.php',
    default => 'template/login.php',
};

if (file_exists($file)) {
    include $file;
} else {
    echo "صفحه مورد نظر یافت نشد!";
}
