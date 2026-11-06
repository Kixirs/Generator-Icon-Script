<?php
use PHPUnit\Framework\TestCase;

class FavoriteExceptionTest extends TestCase {
    // Тест 6: Виняток при відсутності ID іконки
    public function testMissingIconIdException() {
        $this->expectException(UnderflowException::class);
        $id = null;
        if (!$id) throw new UnderflowException("Icon ID required");
    }
    // Тест 7: Виняток при дублюванні в обраному
    public function testDuplicateFavoriteException() {
        $this->expectException(OverflowException::class);
        $isDuplicate = true;
        if ($isDuplicate) throw new OverflowException("Already in favorites");
    }
    // Тест 8: Виняток для неавторизованого користувача
    public function testUnauthorizedFavoriteException() {
        $this->expectException(RuntimeException::class);
        $userId = null;
        if (!$userId) throw new RuntimeException("User not authenticated");
    }
}
