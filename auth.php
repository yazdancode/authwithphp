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
// DB helpers
// ---------------------------
if (!function_exists('dbFetch')) {
    function dbFetch(string $sql, array $params = []): ?array {
        global $pdo;
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            error_log('DB Fetch Error: ' . $e->getMessage());
            return null;
        }
    }
}

if (!function_exists('dbExecute')) {
    function dbExecute(string $sql, array $params = []): bool {
        global $pdo;
        try {
            $stmt = $pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log('DB Execute Error: ' . $e->getMessage());
            error_log('SQL: ' . $sql);
            return false;
        }
    }
}

// ---------------------------
// Check if user exists
// ---------------------------
if (!function_exists('userExists')) {
    function userExists(string $field, string $value): bool {
        $allowedFields = ['email', 'phone'];
        if (!in_array($field, $allowedFields)) return false;

        $sql = "SELECT id FROM users WHERE $field = :value LIMIT 1";
        $result = dbFetch($sql, [':value' => $value]);
        return $result !== null;
    }
}

// ---------------------------
// Create new user
// ---------------------------
if (!function_exists('createUser')) {
    function createUser(array $userData): bool {
        try {
            $sql = "INSERT INTO users (name, email, phone) VALUES (:name, :email, :phone)";
            return dbExecute($sql, [
                ':name'  => $userData['name'],
                ':email' => $userData['email'],
                ':phone' => $userData['phone']
            ]);
        } catch (PDOException $e) {
            error_log('Create User Error: ' . $e->getMessage());
            return false;
        }
    }
}

// ---------------------------
// Token helpers
// ---------------------------
if (!function_exists('generateToken')) {
    function generateToken(string $email): array {
        try {
            $hash = bin2hex(random_bytes(32));
            $token = random_int(100000, 999999);
            $expiresAt = date("Y-m-d H:i:s", time() + 600); // 10 minutes expiry

            // حذف توکن‌های قبلی
            dbExecute("DELETE FROM tokens WHERE email = :email", [':email' => $email]);

            // درج توکن جدید
            $sql = "INSERT INTO tokens (email, token, hash, expires_at, created_at) 
                    VALUES (:email, :token, :hash, :expires_at, NOW())";
            $success = dbExecute($sql, [
                ':email'      => $email,
                ':token'      => $token,
                ':hash'       => $hash,
                ':expires_at' => $expiresAt
            ]);

            if (!$success) {
                error_log('خطا در ذخیره توکن در دیتابیس');
                return [];
            }

            return ['token' => $token, 'hash' => $hash];
        } catch (Exception $e) {
            error_log('Token generation error: ' . $e->getMessage());
            return [];
        }
    }
}

if (!function_exists('findTokenByHash')) {
    function findTokenByHash(string $hash): ?array {
        return dbFetch("SELECT * FROM tokens WHERE hash = :hash LIMIT 1", [':hash' => $hash]);
    }
}

if (!function_exists('isAliveToken')) {
    function isAliveToken(string $hash): bool {
        $token = findTokenByHash($hash);
        if (!$token) return false;
        return strtotime($token['expires_at']) > time();
    }
}

if (!function_exists('sendTokenByEmail')) {
    function sendTokenByEmail(string $email, string $token): bool {
        global $phpmailer;
        try {
            $phpmailer->clearAddresses();
            $phpmailer->addAddress($email);

            $phpmailer->Subject = 'کد تأیید 7auth';
            $phpmailer->Body = "<h1>کد تأیید شما: $token</h1><p>این کد تا ۱۰ دقیقه معتبر است.</p>";
            $phpmailer->isHTML(true);

            return $phpmailer->send();
        } catch (Exception $e) {
            error_log('Email exception: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('verifyToken')) {
    function verifyToken(string $hash, string $enteredCode): bool {
        $token = findTokenByHash($hash);

        if (!$token) return false;

        if (strtotime($token['expires_at']) < time()) return false;

        return (string)$token['token'] === (string)$enteredCode;
    }
}

function getOrCreateToken($email): array {
    $existingToken = dbFetch("SELECT * FROM tokens WHERE email = :email AND expires_at > NOW()", [':email' => $email]);

    if ($existingToken) {
        return ['hash' => $existingToken['hash'], 'token' => $existingToken['token']];
    }

    return generateToken($email);
}

// ---------------------------
// Main action
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

    if (createUser(['name'=>$name,'email'=>$email,'phone'=>$phone])) {
        $tokenResult = getOrCreateToken($email);

        if (empty($tokenResult)) {
            setErrorAndRedirect('خطا در تولید توکن، لطفا دوباره تلاش کنید.', 'auth.php?action=register');
        }

        if (!sendTokenByEmail($email, $tokenResult['token'])) {
            setErrorAndRedirect('خطا در ارسال ایمیل، لطفا دوباره تلاش کنید.', 'auth.php?action=register');
        }

        $_SESSION['hash'] = $tokenResult['hash'];
        $_SESSION['email'] = $email;

        setSuccessAndRedirect('ثبت‌نام با موفقیت انجام شد! کد تأیید به ایمیل شما ارسال شد.', 'auth.php?action=verify');
    } else {
        setErrorAndRedirect('خطا در ثبت اطلاعات کاربر!', 'auth.php?action=register');
    }
}

// ---------------------------
// Verify page
// ---------------------------
if ($action === 'verify') {
    if (empty($_SESSION['email'])) {
        setErrorAndRedirect('ابتدا باید ثبت‌نام کنید یا وارد شوید!', 'auth.php?action=login');
    }

    $email = $_SESSION['email'];

    if (!userExists('email', $email)) {
        setErrorAndRedirect('ابتدا باید ثبت‌نام کنید یا وارد شوید!', 'auth.php?action=login');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resend_token'])) {
        $tokenResult = getOrCreateToken($email);

        if (empty($tokenResult)) {
            setErrorAndRedirect('خطا در تولید توکن، لطفا دوباره تلاش کنید.', 'auth.php?action=verify');
        }

        if (!sendTokenByEmail($email, $tokenResult['token'])) {
            setErrorAndRedirect('خطا در ارسال ایمیل، لطفا دوباره تلاش کنید.', 'auth.php?action=verify');
        }

        $_SESSION['hash'] = $tokenResult['hash'];
        $_SESSION['success'] = 'کد تأیید جدید به ایمیل شما ارسال شد.';
        redirect('auth.php?action=verify');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_code'])) {
        $enteredCode = trim($_POST['code'] ?? '');

        if (empty($enteredCode)) {
            setErrorAndRedirect('لطفاً کد تأیید را وارد کنید!', 'auth.php?action=verify');
        }

        if (isset($_SESSION['hash']) && verifyToken($_SESSION['hash'], $enteredCode)) {
            $_SESSION['verified'] = true;
            $_SESSION['success'] = 'احراز هویت با موفقیت انجام شد!';

            dbExecute("DELETE FROM tokens WHERE hash = :hash", [':hash' => $_SESSION['hash']]);
            unset($_SESSION['hash']);

            redirect('dashboard.php');
        } else {
            setErrorAndRedirect('کد تأیید نامعتبر یا منقضی شده است!', 'auth.php?action=verify');
        }
    }

    include 'template/verify.php';
    exit;
}

// ---------------------------
// Load view
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
