<?php

global $database_config;
require 'settings.php';
require 'constants.php';
require BASE_PASS . 'libs/helpers.php';

try {
    $dsn = "mysql:host=$database_config->host;dbname=$database_config->dbname;charset=$database_config->charset";
    $pdo = new PDO($dsn, $database_config->user, $database_config->password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    echo "اتصال به دیتابیس ناموفق بود: " . $e->getMessage();
    exit;
}
