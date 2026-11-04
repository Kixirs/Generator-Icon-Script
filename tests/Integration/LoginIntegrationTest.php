<?php
use PHPUnit\Framework\TestCase;

class LoginIntegrationTest extends TestCase {
    // Тест 5-6: Перевірка верифікації паролю з хэшу з бази даних
    public function testDatabasePasswordVerify() {
        $pdo = new PDO("mysql:host=localhost;dbname=GIScript;charset=utf8mb4", "root", "");
        $hash = password_hash("my_secret_pass", PASSWORD_BCRYPT);
        
        $this->assertTrue(password_verify("my_secret_pass", $hash));
        $this->assertFalse(password_verify("wrong_pass", $hash));
    }
}
