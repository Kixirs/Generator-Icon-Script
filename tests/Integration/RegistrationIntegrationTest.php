<?php
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversNothing;

#[CoversNothing]
class RegistrationIntegrationTest extends TestCase {
    // Тест 3-4: Запис користувача в БД
    public function testUserInsertionAndRemoval() {
        $pdo = new PDO("mysql:host=localhost;dbname=GIScript;charset=utf8mb4", "root", "12345678as9F0");
        $email = "integration_test@gis.com";
        
        // Очистимо старі тести
        $pdo->prepare("DELETE FROM users WHERE email = ?")->execute([$email]);
        
        // Вставка (використовуємо правильну колонку 'nickname' замість 'username')
        $stmt = $pdo->prepare("INSERT INTO users (nickname, email, password) VALUES (?, ?, ?)");
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
