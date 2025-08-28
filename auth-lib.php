<?php
// ----------------------------------
// بررسی وجود کاربر
// ----------------------------------
use Random\RandomException;

if (!function_exists('userExists')) {
    function userExists(string $email=null, string $phone = null): bool
    {
        global $pdo;

        $sql = "SELECT id FROM users WHERE email = :email OR phone = :phone LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email ?? '', ':phone' => $phone ?? '']);
        return $stmt->fetchColumn() !== false;
    }
}

if(!function_exists('userExistsByEmail')){
    function userExistsByEmail(string $email):bool{
        global $pdo;
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        return $stmt->fetchColumn() !== false;
    }
}

// ----------------------------------
// ایجاد کاربر جدید
// ----------------------------------
if (!function_exists('createUser')) {
    function createUser(array $userData): void
    {
        global $pdo;
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, phone) VALUES (?, ?, ?)");
            $stmt->execute([$userData['name'], $userData['email'], $userData['phone']]);
            // ذخیره پیام موفقیت و ایمیل کاربر در سشن
            $_SESSION['success'] = 'ثبت‌نام با موفقیت انجام شد!';
            $_SESSION['email'] = $userData['email'];

            // هدایت به صفحه verify
            redirect('auth.php?action=verify');

        } catch (PDOException $e) {
            setErrorAndRedirect('خطا در ثبت اطلاعات: ' . $e->getMessage(), 'auth.php?action=register');
        }
    }
}

# token generate
if (!function_exists('generateToken')) {
    function generateToken(int $length = 32): array {
        try {
            global $pdo;
            $hash = bin2hex(random_bytes($length));
            $token = rand(100000, 999999);
            $create_at = date("Y-m-d H:i:s", time() + 600);

            $sql = "INSERT INTO `tokens` (token, hash, create_at) VALUES (:token, :hash, :create_at)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'token'     => $token,
                'hash'      => $hash,
                'create_at' => $create_at
            ]);
            return [
                'token' => $token,
                'hash'  => $hash
            ];

        } catch (Exception $e) {
            return [];
        }
    }
}

if (!function_exists('isAliveToken')) {
    function isAliveToken(string $hash): bool
    {
        $token = findTokenByHash($hash);
        if (!$token) {
            return false;
        }
        $now = date("Y-m-d H:i:s");
        return ($token['create_at'] > $now);
    }
}



if (!function_exists('findTokenByHash')) {
    function findTokenByHash(string $hash): ?array
    {
        global $pdo;
        $sql = "SELECT * FROM `tokens` WHERE `hash` = :hash LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['hash' => $hash]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
}



# send token
# verify
# set login session


// ----------------------------------
// حذف کاربر از دیتابیس
// ----------------------------------
if (!function_exists('deleteUser')) {
    function deleteUser(int $userId): bool
    {
        global $pdo;
        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log('خطا در حذف کاربر: ' . $e->getMessage());
            return false;
        }
    }
}

