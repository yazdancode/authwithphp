<?php
// ----------------------------------
// helper برای اجرای کوئری
// ----------------------------------
if (!function_exists('dbFetch')) {
    function dbFetch(string $sql, array $params = []): ?array {
        global $pdo;
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
}

if (!function_exists('dbExecute')) {
    function dbExecute(string $sql, array $params = []): bool {
        global $pdo;
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }
}

// ----------------------------------
// بررسی وجود کاربر
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
// ایجاد کاربر جدید
// ----------------------------------
if (!function_exists('createUser')) {
    function createUser(array $userData): void {
        try {
            $sql = "INSERT INTO users (name, email, phone) VALUES (:name, :email, :phone)";
            dbExecute($sql, [':name'  => $userData['name'], ':email' => $userData['email'], ':phone' => $userData['phone']]);
            $_SESSION['success'] = 'ثبت‌نام با موفقیت انجام شد!';
            $_SESSION['email'] = $userData['email'];
            redirect('auth.php?action=verify');
        } catch (PDOException $e) {
            setErrorAndRedirect('خطا در ثبت اطلاعات: ' . $e->getMessage(), 'auth.php?action=register');
        }
    }
}

// ----------------------------------
// حذف کاربر
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
// token
// ----------------------------------
if (!function_exists('generateToken')) {
    function generateToken(int $length = 32): array {
        try {
            $hash = bin2hex(random_bytes($length));
            $token = rand(100000, 999999);
            $create_at = date("Y-m-d H:i:s", time() + 600);
            $sql = "INSERT INTO tokens (token, hash, create_at) VALUES (:token, :hash, :create_at)";
            dbExecute($sql, [':token'=> $token, ':hash'=> $hash, ':create_at' => $create_at]);
            return ['token' => $token, 'hash' => $hash];
        } catch (Exception) {
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
        return strtotime($token['create_at']) > time();
    }
}

