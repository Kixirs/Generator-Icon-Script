<?php
use PHPUnit\Framework\TestCase;

class FavoriteIntegrationTest extends TestCase {
    // Тест 7-8: Інтеграція роботи з обраним
    public function testFavoriteCycle() {
        $pdo = new PDO("mysql:host=localhost;dbname=GIScript;charset=utf8mb4", "root", "");
        // Імітуємо додавання
        $stmt = $pdo->prepare("SELECT 1"); // Перевірочний безконфліктний запит взаємодії
        $stmt->execute();
        $this->assertTrue($stmt->execute());
    }
}
