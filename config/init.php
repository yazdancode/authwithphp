<?php
session_start();

// بارگذاری فایل‌های کمکی و تنظیمات
require __DIR__ . '/../libs/helpers.php';
require __DIR__ . '/settings.php';
require __DIR__ . '/constants.php';

// اتصال به دیتابیس با PDO
try {
    $dsn = "mysql:host={$database_config->host};dbname={$database_config->dbname};charset={$database_config->charset}";
    $pdo = new PDO($dsn, $database_config->user, $database_config->password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    // نمایش خطای عمومی به کاربر
    echo "اتصال به دیتابیس ناموفق بود.";
    // برای دیباگ می‌توان خطا را در فایل لاگ ذخیره کرد:
    // error_log($e->getMessage());
    exit;
<<<<<<< HEAD
}
=======
}
>>>>>>> 9afd6b6 (changes repository github and version php)
