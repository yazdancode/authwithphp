<?php

use JetBrains\PhpStorm\NoReturn;

require 'config/init.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ---------------------------
// helper redirect / flash
// ---------------------------
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

if (!function_exists('setSuccessAndRedirect')) {
    #[NoReturn]
    function setSuccessAndRedirect(string $message, string $target): void
    {
        $_SESSION['success'] = $message;
        redirect($target);
    }
}

// ---------------------------
// helper token
// ---------------------------
function getOrCreateToken(): array {
    if (!empty($_SESSION['hash']) && isAliveToken($_SESSION['hash'])) {
        $oldToken = findTokenByHash($_SESSION['hash']);
        return ['hash' => $_SESSION['hash'], 'token' => $oldToken['token']];
    }
    $newToken = generateToken();
    $_SESSION['hash']  = $newToken['hash'];
    $_SESSION['token'] = $newToken['token'];
    return $newToken;
}

// ---------------------------
// action اصلی
// ---------------------------
$action = $_GET['action'] ?? 'login';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'register') {
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if (empty($name) || empty($email) || empty($phone)) {
        setErrorAndRedirect('لطفاً همه فیلدها را پر کنید!', 'auth.php?action=register');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setErrorAndRedirect('ایمیل وارد شده معتبر نیست!', 'auth.php?action=register');
    }

    if (userExists('email', $email) || userExists('phone', $phone)) {
        setErrorAndRedirect('ایمیل یا شماره موبایل وارد شده قبلاً ثبت شده است!', 'auth.php?action=register');
    }

    createUser(['name'=>$name,'email'=>$email,'phone'=>$phone]);
}

if ($action === 'verify' && !empty($_SESSION['email'])) {
    if (!userExists('email', $_SESSION['email'])) {
        setErrorAndRedirect('ابتدا باید ثبت‌نام کنید یا وارد شوید!', 'auth.php?action=login');
    }
    $tokenResult = getOrCreateToken();
    // ارسال توکن به کاربر
    // sendToken($_SESSION['email'], $tokenResult['token']);

    include 'template/verify.php';
}

// ---------------------------
// بارگذاری صفحه مناسب
// ---------------------------
$file = match ($action) {
    'register' => 'template/register.php',
    'verify'   => 'template/verify.php',
    default    => 'template/login.php',
};

if (file_exists($file)) {
    include $file;
} else {
    echo "صفحه مورد نظر یافت نشد!";
}
