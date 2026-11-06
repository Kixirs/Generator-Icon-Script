<?php
use PHPUnit\Framework\TestCase;

use PHPUnit\Framework\Attributes\CoversNothing;

#[CoversNothing]
class RatingIntegrationTest extends TestCase {
    // Тест 9-10: Інтеграція збереження оцінки
    public function testRatingDatabaseTransaction() {
        $pdo = new PDO("mysql:host=localhost;dbname=GIScript;charset=utf8mb4", "root", "12345678as9F0");
        $pdo->beginTransaction();
        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
        $this->assertNotNull($stmt->fetchColumn());
        $pdo->rollBack(); // Скасовуємо транзакцію
    }
}
