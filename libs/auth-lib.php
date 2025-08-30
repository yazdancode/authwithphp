<?php
// ----------------------------------
// DB helpers
// ----------------------------------
use JetBrains\PhpStorm\NoReturn;

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
            return $pdo->prepare($sql)->execute($params);
        } catch (PDOException $e) {
            error_log('DB Execute Error: ' . $e->getMessage());
            error_log('SQL: ' . $sql);
            return false;
        }
    }
}

// ----------------------------------
// Check if user exists
// ----------------------------------
if (!function_exists('userExists')) {
    function userExists(string $field, string $value): bool {
        $allowedFields = ['email', 'phone'];
        if (!in_array($field, $allowedFields)) {
            return false;
        }

        $sql = "SELECT id FROM users WHERE $field = :value LIMIT 1";
        $result = dbFetch($sql, [':value' => $value]);
        return $result !== null;
    }
}

// ----------------------------------
// Create new user
// ----------------------------------
if (!function_exists('createUser')) {
    function createUser(array $userData): bool {
        try {
            $sql = "INSERT INTO users (name, email, phone) VALUES (:name, :email, :phone)";
            $success = dbExecute($sql, [
                ':name'  => $userData['name'],
                ':email' => $userData['email'],
                ':phone' => $userData['phone']
            ]);

            if ($success) {
                $_SESSION['success'] = 'ثبت‌نام با موفقیت انجام شد!';
                $_SESSION['email'] = $userData['email'];
                return true;
            }

            return false;
        } catch (PDOException $e) {
            error_log('Create User Error: ' . $e->getMessage());
            return false;
        }
    }
}

// ----------------------------------
// Delete user
// ----------------------------------
if (!function_exists('deleteUser')) {
    function deleteUser(int $userId): bool {
        try {
            return dbExecute("DELETE FROM users WHERE id = :id", [':id' => $userId]);
        } catch (PDOException $e) {
            error_log('خطا در حذف کاربر: ' . $e->getMessage());
            return false;
        }
    }
}

// ----------------------------------
// Token helpers - اصلاح شده
// ----------------------------------
if (!function_exists('generateToken')) {
    function generateToken(string $email, int $length = 32): array {
        try {
            // ابتدا بررسی کنید که آیا کاربر وجود دارد
            if (!userExists('email', $email)) {
                error_log('کاربر با ایمیل ' . $email . ' وجود ندارد');
                return [];
            }

            $hash = bin2hex(random_bytes($length));
            $token = random_int(100000, 999999);
            $expiresAt = date("Y-m-d H:i:s", time() + 600); // 10 دقیقه اعتبار

            // ابتدا هر توکن قدیمی برای این ایمیل را حذف کنید
            dbExecute("DELETE FROM tokens WHERE email = :email", [':email' => $email]);

            // سپس توکن جدید را اضافه کنید
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
        if (!$token) {
            return false;
        }
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

// ----------------------------------
// Redirect helpers
// ----------------------------------
if (!function_exists('redirect')) {
    #[NoReturn]
    function redirect(string $url): void {
        header("Location: $url");
        exit();
    }
}

if (!function_exists('setErrorAndRedirect')) {
    #[NoReturn]
    function setErrorAndRedirect(string $error, string $url): void {
        $_SESSION['error'] = $error;
        redirect($url);
    }
}

// ----------------------------------
// Main process registration
// ----------------------------------
#[NoReturn]
function processRegistration(array $userData): void {
    // ابتدا کاربر را ایجاد کنید
    if (!createUser($userData)) {
        setErrorAndRedirect('خطا در ثبت اطلاعات کاربر', 'auth.php?action=register');
    }

    // سپس توکن را تولید و ارسال کنید
    $tokenData = generateToken($userData['email']);

    if (empty($tokenData)) {
        setErrorAndRedirect('خطا در تولید توکن، لطفا دوباره تلاش کنید.', 'auth.php?action=register');
    }

    if (!sendTokenByEmail($userData['email'], $tokenData['token'])) {
        setErrorAndRedirect('خطا در ارسال ایمیل، لطفا دوباره تلاش کنید.', 'auth.php?action=register');
    }

    // ذخیره hash در سشن برای تأیید بعدی
    $_SESSION['token_hash'] = $tokenData['hash'];
    redirect('auth.php?action=verify');
}
