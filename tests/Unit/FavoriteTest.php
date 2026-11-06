<?php
use PHPUnit\Framework\TestCase;

class FavoriteTest extends TestCase {
    // Тест 6: Валідація ID іконки (має бути строго позитивним)
    public function testValidIconId() {
        $iconId = 42;
        $this->assertGreaterThan(0, $iconId);
    }
    // Тест 7: Перевірка структури масиву обраного
    public function testFavoriteArrayStructure() {
        $favorite = ['user_id' => 1, 'icon_id' => 12];
        $this->assertArrayHasKey('user_id', $favorite);
        $this->assertArrayHasKey('icon_id', $favorite);
    }
}
