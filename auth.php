<?php
require 'config/init.php';

// نمایش خطاها برای دیباگ
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// متغیر پیام‌ها
$errorMsg = '';
$successMsg = '';

// پردازش فرم ثبت‌نام
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_GET['action'] ?? '';
    $params = $_POST;

    if ($action == 'register') {
        $name = trim($params['name']);
        $email = trim($params['email']);
        $phone = trim($params['phone']);

        // بررسی خالی بودن فیلدها
        if (empty($name) || empty($email) || empty($phone)) {
            setErrorAndRedirect('لطفاً همه فیلدها را پر کنید!', 'auth.php?action=register');
        }

        // اعتبارسنجی ایمیل
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setErrorAndRedirect('ایمیل وارد شده معتبر نیست!', 'auth.php?action=register');
        }

        // بررسی ایمیل تکراری
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            setErrorAndRedirect('این ایمیل قبلاً ثبت شده است!', 'auth.php?action=register');
        }

        // بررسی شماره موبایل تکراری
        $stmt = $pdo->prepare("SELECT id FROM users WHERE phone = ?");
        $stmt->execute([$phone]);
        if ($stmt->rowCount() > 0) {
            setErrorAndRedirect('این شماره موبایل قبلاً ثبت شده است!', 'auth.php?action=register');
        }

        // ذخیره اطلاعات در دیتابیس
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, phone) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $phone]);

            $_SESSION['success'] = 'ثبت‌نام با موفقیت انجام شد!';
            redirect('auth.php?action=login');

        } catch (PDOException $e) {
            setErrorAndRedirect('خطا در ثبت اطلاعات: ' . $e->getMessage(), 'auth.php?action=register');
        }
    }
}





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

// include صفحه انتخاب‌شده
if (file_exists($file)) {
    include $file;
} else {
    echo "صفحه مورد نظر یافت نشد!";
}
