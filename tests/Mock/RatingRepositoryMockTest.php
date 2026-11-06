<?php
use PHPUnit\Framework\TestCase;
use App\RatingRepository;

class RatingRepositoryMockTest extends TestCase {
    // Тест 6: Мок збереження оцінки
    public function testMockSaveRating() {
        $mock = $this->createMock(RatingRepository::class);
        $mock->method('save')->willReturn(true);
        $this->assertTrue($mock->save(1, 42, 5));
    }
    // Тест 7: Мок перевірки чи юзер вже голосував
    public function testMockHasUserRated() {
        $mock = $this->createMock(RatingRepository::class);
        $mock->method('hasRated')->willReturn(true);
        $this->assertTrue($mock->hasRated(1, 42));
    }
    // Тест 8: Мок отримання середнього значення
    public function testMockGetAverage() {
        $mock = $this->createMock(RatingRepository::class);
        $mock->method('getAverage')->willReturn(4.7);
        $this->assertEquals(4.7, $mock->getAverage(42));
    }
}
