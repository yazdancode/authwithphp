<?php
// ----------------------------------
// DB helpers
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
// Check if user exists
// ----------------------------------
if (!function_exists('userExists')) {
    function userExists(string $field, string $value): bool {
        $allowedFields = ['email', 'phone'];
        if (!in_array($field, $allowedFields)) return false;

        $sql = "SELECT id FROM users WHERE $field = :value LIMIT 1";
        $result = dbFetch($sql, [':value' => $value]);
        return $result !== null;
    }
}

// ----------------------------------
// Create new user
// ----------------------------------
if (!function_exists('createUser')) {
    function createUser(array $userData): void {
        try {
            $sql = "INSERT INTO users (name, email, phone) VALUES (:name, :email, :phone)";
            dbExecute($sql, [
                ':name'  => $userData['name'],
                ':email' => $userData['email'],
                ':phone' => $userData['phone']
            ]);
            $_SESSION['success'] = 'ثبت‌نام با موفقیت انجام شد!';
            $_SESSION['email'] = $userData['email'];
            redirect('auth.php?action=verify');
        } catch (PDOException $e) {
            setErrorAndRedirect('خطا در ثبت اطلاعات: ' . $e->getMessage(), 'auth.php?action=register');
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
// Token helpers
// ----------------------------------
if (!function_exists('generateToken')) {
    function generateToken(int $length = 32): array {
        try {
            $hash = bin2hex(random_bytes($length));
            $token = random_int(100000, 999999);
            $expiresAt = date("Y-m-d H:i:s", time() + 600); // 10 minutes expiry

            $sql = "INSERT INTO tokens (token, hash, expires_at) VALUES (:token, :hash, :expires_at)";
            dbExecute($sql, [
                ':token'=> $token,
                ':hash'=> $hash,
                ':expires_at' => $expiresAt
            ]);

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
    if (!function_exists('sendTokenByEmail')) {
        function sendTokenByEmail(string $email, string|int $token): bool {
            global $phpmailer;

            try {
                // Clear previous recipients
                $phpmailer->clearAddresses();

                // Add recipient
                $phpmailer->addAddress($email);

                // Email content
                $phpmailer->Subject = '7auth verify token';
                $phpmailer->Body = "<h1>Your token is: $token</h1>";

                // Attempt to send email
                if ($phpmailer->send()) {
                    return true;
                }

                // Handle Mailtrap "too many emails" error
                if (str_contains($phpmailer->ErrorInfo, 'Too many emails')) {
                    error_log('Mailtrap limit reached, email skipped.');
                    return false;
                }

                // Other errors
                error_log('Email sending error: ' . $phpmailer->ErrorInfo);
                return false;

            } catch (Exception $e) {
                error_log('Email exception: ' . $e->getMessage());
                return false;
            }
        }
    }

}

