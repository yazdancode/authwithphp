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
// DB helpers - اضافه شده
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
// Check if user exists - اضافه شده
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
// Create new user - اضافه شده
// ---------------------------
if (!function_exists('createUser')) {
    function createUser(array $userData): bool {
        try {
            $sql = "INSERT INTO users (name, email, phone) VALUES (:name, :email, :phone)";
            $success = dbExecute($sql, [
                ':name'  => $userData['name'],
                ':email' => $userData['email'],
                ':phone' => $userData['phone']
            ]);

            return $success;
        } catch (PDOException $e) {
            error_log('Create User Error: ' . $e->getMessage());
            return false;
        }
    }
}

// ---------------------------
// توکن helpers - کاملاً اصلاح شده
// ---------------------------
if (!function_exists('generateToken')) {
    function generateToken(string $email): array {
        try {
            $hash = bin2hex(random_bytes(32));
            $token = random_int(100000, 999999);
            $expiresAt = date("Y-m-d H:i:s", time() + 600); // 10 minutes expiry

            // حذف توکن‌های قبلی برای این ایمیل
            dbExecute("DELETE FROM tokens WHERE email = :email", [':email' => $email]);

            // درج توکن جدید
            $sql = "INSERT INTO tokens (email, token, hash, create_at) VALUES (:email, :token, :hash, :create_at)";
            $success = dbExecute($sql, [
                ':email' => $email,
                ':token' => $token,
                ':hash' => $hash,
                ':create_at	' => $expiresAt
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
        return strtotime($token['create_at']) > time();
    }
}

if (!function_exists('sendTokenByEmail')) {
    function sendTokenByEmail(string $email, string $token): bool {
        global $phpmailer;

        try {
            // Clear previous recipients
            $phpmailer->clearAddresses();

            // Add recipient
            $phpmailer->addAddress($email);

            // Email content
            $phpmailer->Subject = 'کد تأیید 7auth';
            $phpmailer->Body = "<h1>کد تأیید شما: $token</h1><p>این کد تا ۱۰ دقیقه معتبر است.</p>";
            $phpmailer->isHTML(true);

            // Attempt to send email
            if ($phpmailer->send()) {
                return true;
            }

            error_log('Email sending error: ' . $phpmailer->ErrorInfo);
            return false;

        } catch (Exception $e) {
            error_log('Email exception: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('verifyToken')) {
    function verifyToken(string $hash, string $enteredCode): bool {
        $token = findTokenByHash($hash);

        if (!$token) {
            error_log("توکن با هش داده شده یافت نشد: $hash");
            return false;
        }

        // بررسی انقضای توکن
        if (strtotime($token['created_at']) < time()) {
            error_log("توکن منقضی شده: $hash");
            return false;
        }

        // تبدیل هر دو مقدار به رشته برای مقایسه صحیح
        $storedToken = (string)$token['token'];
        $enteredCode = (string)$enteredCode;

        // بررسی تطابق کد
        if ($storedToken !== $enteredCode) {
            error_log("کد وارد شده با توکن مطابقت ندارد: $enteredCode != $storedToken");
            return false;
        }

        return true;
    }
}

// ---------------------------
// helper token - اصلاح شده
// ---------------------------
function getOrCreateToken($email): array {
    // بررسی وجود توکن فعال برای این ایمیل
    $existingToken = dbFetch("SELECT * FROM tokens WHERE email = :email AND create_at > NOW()", [':email' => $email]);

    if ($existingToken) {
        return ['hash' => $existingToken['hash'], 'token' => $existingToken['token']];
    }

    // ایجاد توکن جدید
    $newToken = generateToken($email);
    if (empty($newToken)) {
        return [];
    }

    return $newToken;
}

// ---------------------------
// action اصلی - اصلاح شده
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

    // ایجاد کاربر و سپس تولید توکن
    if (createUser(['name'=>$name,'email'=>$email,'phone'=>$phone])) {
        // تولید و ارسال توکن
        $tokenResult = getOrCreateToken($email);

        if (empty($tokenResult)) {
            setErrorAndRedirect('خطا در تولید توکن، لطفا دوباره تلاش کنید.', 'auth.php?action=register');
        }

        // ارسال توکن به کاربر
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

// پردازش صفحه تأیید
if ($action === 'verify') {
    if (empty($_SESSION['email'])) {
        setErrorAndRedirect('ابتدا باید ثبت‌نام کنید یا وارد شوید!', 'auth.php?action=login');
    }

    $email = $_SESSION['email'];

    if (!userExists('email', $email)) {
        setErrorAndRedirect('ابتدا باید ثبت‌نام کنید یا وارد شوید!', 'auth.php?action=login');
    }

    // اگر درخواست ارسال مجدد توکن باشد
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resend_token'])) {
        $tokenResult = getOrCreateToken($email);

        if (empty($tokenResult)) {
            setErrorAndRedirect('خطا در تولید توکن، لطفا دوباره تلاش کنید.', 'auth.php?action=verify');
        }

        // ارسال توکن به کاربر
        if (!sendTokenByEmail($email, $tokenResult['token'])) {
            setErrorAndRedirect('خطا در ارسال ایمیل، لطفا دوباره تلاش کنید.', 'auth.php?action=verify');
        }

        $_SESSION['hash'] = $tokenResult['hash'];
        $_SESSION['success'] = 'کد تأیید جدید به ایمیل شما ارسال شد.';
        redirect('auth.php?action=verify');
    }

    // اگر کد تأیید ارسال شده باشد
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_code'])) {
        $enteredCode = trim($_POST['code'] ?? '');

        if (empty($enteredCode)) {
            setErrorAndRedirect('لطفاً کد تأیید را وارد کنید!', 'auth.php?action=verify');
        }

        // بررسی صحت کد
        if (isset($_SESSION['hash']) && verifyToken($_SESSION['hash'], $enteredCode)) {
            // کد صحیح است
            $_SESSION['verified'] = true;
            $_SESSION['success'] = 'احراز هویت با موفقیت انجام شد!';

            // حذف توکن استفاده شده
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