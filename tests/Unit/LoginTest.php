<?php
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase {
    // Тест 4: Очищення вхідних даних від пробілів
    public function testTrimLoginInput() {
        $input = "  admin_gis   ";
        $this->assertEquals("admin_gis", trim($input));
    }
    // Тест 5: Перевірка на порожні поля
    public function testEmptyLoginFields() {
        $login = "";
        $this->assertTrue(empty($login));
    }
}
