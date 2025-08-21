<?php
use PHPUnit\Framework\TestCase;
require __DIR__ . '/../config/settings.php';

class DatabaseConnectionTest extends TestCase
{
    public function testCanConnectToDatabase()
    {
        global $database_config;


        $dsn = "mysql:host=$database_config->host;dbname=$database_config->dbname;charset=$database_config->charset";

        try {
            $pdo = new PDO($dsn, $database_config->user, $database_config->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            
            $this->assertInstanceOf(PDO::class, $pdo); // ✅ اتصال موفق
        } catch (PDOException $e) {
            $this->fail("اتصال به دیتابیس ناموفق بود: " . $e->getMessage());
        }
    }
}
