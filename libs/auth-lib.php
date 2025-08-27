<?php
if (!function_exists('userExists')) {
    function userExists(string $email, string $phone): bool
    {
        global $pdo;

        $sql = "SELECT id FROM users WHERE email = :email OR phone = :phone LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':email' => $email,
            ':phone' => $phone
        ]);

        return $stmt->fetchColumn() !== false;
    }
}

if (!function_exists('createUser')) {
    function createUser(array $userData)
    {
        global $pdo;
        try {
            // آماده‌سازی و اجرای کوئری برای درج کاربر
            $stmt = $pdo->prepare("INSERT INTO users (name, email, phone) VALUES (?, ?, ?)");
            $stmt->execute([
                $userData['name'],
                $userData['email'],
                $userData['phone']
            ]);
            // ذخیره پیام موفقیت در سشن
            $_SESSION['success'] = 'ثبت‌نام با موفقیت انجام شد!';
        } catch (PDOException $e) {
            // در صورت خطا، پیام خطا نمایش داده شود و کاربر به فرم بازگردد
            setErrorAndRedirect('خطا در ثبت اطلاعات: ' . $e->getMessage(), 'auth.php?action=register');
        }
    }
}

