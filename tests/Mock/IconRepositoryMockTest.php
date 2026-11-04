<?php
use PHPUnit\Framework\TestCase;

class IconRepositoryMockTest extends TestCase {
    // Тест 9: Мок успішного видалення іконки адміном
    public function testMockDeleteIcon() {
        $mock = $this->createMock(stdClass::class);
        $mock->method('delete')->willReturn(true);
        $this->assertTrue($mock->delete(42));
    }
    // Тест 10: Мок підрахунку кількості іконок в системі
    public function testMockCountIcons() {
        $mock = $this->createMock(stdClass::class);
        $mock->method('countAll')->willReturn(150);
        $this->assertEquals(150, $mock->countAll());
    }
}
