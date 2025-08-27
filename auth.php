<?php
session_start();
global $pdo;
require 'config/init.php';

// نمایش خطاها برای دیباگ
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// تابع هدایت
if (!function_exists('redirect')) {
    function redirect(string $target = BASE_URL) {
        header('Location: ' . $target);
        exit;
    }
}

// تابع نمایش خطا و هدایت
if (!function_exists('setErrorAndRedirect')) {
    function setErrorAndRedirect(string $message, string $target) {
        $_SESSION['error'] = $message;
        redirect($target);
    }
}

// تابع بررسی تکراری بودن کاربر
if (!function_exists('userExists')) {
    function userExists(string $email, string $phone): bool {
        global $pdo;
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR phone = ?");
        $stmt->execute([$email, $phone]);
        return $stmt->rowCount() > 0;
    }
}

// تابع ایجاد کاربر
if (!function_exists('createUser')) {
    function createUser(array $userData): bool {
        global $pdo;
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, phone) VALUES (?, ?, ?)");
            $stmt->execute([
                $userData['name'],
                $userData['email'],
                $userData['phone']
            ]);
            return true;
        } catch (PDOException $e) {
            setErrorAndRedirect('خطا در ثبت اطلاعات: ' . $e->getMessage(), 'auth.php?action=register');
        }
    }
}

// پردازش فرم ثبت‌نام
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_GET['action'] ?? '';
    $params = $_POST;

    if ($action == 'register') {
        $name  = trim($params['name'] ?? '');
        $email = trim($params['email'] ?? '');
        $phone = trim($params['phone'] ?? '');

        // بررسی خالی بودن فیلدها
        if (empty($name) || empty($email) || empty($phone)) {
            setErrorAndRedirect('لطفاً همه فیلدها را پر کنید!', 'auth.php?action=register');
        }

        // اعتبارسنجی ایمیل
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setErrorAndRedirect('ایمیل وارد شده معتبر نیست!', 'auth.php?action=register');
        }

        // بررسی تکراری بودن ایمیل یا شماره موبایل
        if (userExists($email, $phone)) {
            setErrorAndRedirect('ایمیل یا شماره موبایل وارد شده قبلاً ثبت شده است!', 'auth.php?action=register');
        }

        // ایجاد کاربر و هدایت به صفحه verify
        $userData = [
            'name'  => $name,
            'email' => $email,
            'phone' => $phone
        ];

        if (createUser($userData)) {
            $_SESSION['email'] = $email;
            redirect('auth.php?action=verify');
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
