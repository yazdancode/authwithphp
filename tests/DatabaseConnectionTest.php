<?php
use PHPUnit\Framework\TestCase;

class DatabaseConnectionTest extends TestCase
{
    public function testCanConnectToDatabase()
    {
        require __DIR__ . '/../config/settings.php';
        require __DIR__ . '/../config/constants.php';

        $dsn = "mysql:host={$database_config->host};dbname={$database_config->dbname};charset={$database_config->charset}";

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
