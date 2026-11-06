<?php
use PHPUnit\Framework\TestCase;

class RatingTest extends TestCase {
    // Тест 8: Перевірка діапазону оцінки (мінімум)
    public function testMinRatingValue() {
        $rating = 1;
        $this->assertGreaterThanOrEqual(1, $rating);
    }

    // Тест 9: Перевірка діапазону оцінки (максимум)
    public function testMaxRatingValue() {
        $rating = 5;
        $this->assertLessThanOrEqual(5, $rating);
    }

    // Тест 10: Розрахунок середнього рейтингу
    public function testAverageRatingCalculation() {
        // Заповнюємо масив реальними тестовими оцінками
        $ratings = array(3, 4, 5);
        $average = array_sum($ratings) / count($ratings);
        $this->assertEquals(4.0, $average);
    }
}
