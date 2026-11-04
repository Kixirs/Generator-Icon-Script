<?php
use PHPUnit\Framework\TestCase;

class DatabaseIntegrationTest extends TestCase {
    protected $pdo;
    protected function setUp(): void {
        $host = 'localhost'; $db = 'GIScript'; $user = 'root'; $pass = '';
        $this->pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    }
    // Тест 1: З'єднання з БД активне
    public function testDatabaseConnectionActive() {
        $this->assertInstanceOf(PDO::class, $this->pdo);
    }
    // Тест 2: Таблиця користувачів існує
    public function testUsersTableExists() {
        $stmt = $this->pdo->query("SHOW TABLES LIKE 'users'");
        $this->assertNotEmpty($stmt->fetchAll());
    }
}
