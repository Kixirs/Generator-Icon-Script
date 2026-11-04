<?php
use PHPUnit\Framework\TestCase;

class RegistrationIntegrationTest extends TestCase {
    // Тест 3-4: Запис користувача в БД
    public function testUserInsertionAndRemoval() {
        $pdo = new PDO("mysql:host=localhost;dbname=GIScript;charset=utf8mb4", "root", "");
        $email = "integration_test@gis.com";
        
        // Очистимо старі тести
        $pdo->prepare("DELETE FROM users WHERE email = ?")->execute([$email]);
        
        // Вставка
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute(['IntUser', $email, password_hash('12345678', PASSWORD_BCRYPT)]);
        
        // Перевірка
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        $user = $check->fetch();
        $this->assertNotEmpty($user);
        
        // Очищення після тесту
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$user['id']]);
    }
}
