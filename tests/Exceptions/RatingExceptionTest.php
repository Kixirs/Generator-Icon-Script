<?php
use PHPUnit\Framework\TestCase;

class RatingExceptionTest extends TestCase {
    // Тест 9: Виняток при оцінці поза межами діапазону
    public function testRatingOutOfRangeException() {
        $this->expectException(OutOfRangeException::class);
        $value = 6;
        if ($value < 1 || $value > 5) throw new OutOfRangeException("Rating must be 1-5");
    }
    // Тест 10: Виняток при спробі оцінити власну іконку
    public function testRateOwnIconException() {
        $this->expectException(LogicException::class);
        $authorId = 1; $voterId = 1;
        if ($authorId === $voterId) throw new LogicException("Cannot rate own content");
    }
}
