<?php
global $database_config;
session_start();
date_default_timezone_set('Asia/Tehran');
require  __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../libs/helpers.php';
require __DIR__ . '/../libs/auth-lib.php';
require __DIR__ . '/settings.php';
require __DIR__ . '/constants.php';
require __DIR__ . '/mail.php';

try {
    $dsn = "mysql:host=$database_config->host;dbname=$database_config->dbname;charset=$database_config->charset";
    $pdo = new PDO($dsn, $database_config->user, $database_config->password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    echo "اتصال به دیتابیس ناموفق بود.";
//    error_log($e->getMessage());
    exit;
}