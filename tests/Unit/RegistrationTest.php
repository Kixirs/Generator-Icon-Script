<?php
use PHPUnit\Framework\TestCase;

class RegistrationTest extends TestCase {
    // Тест 1: Валідація коректного імені
    public function testValidUsername() {
        $username = "Kixirs_2026";
        $this->assertMatchesRegularExpression('/^[\w\d_]{3,20}$/', $username);
    }
    // Тест 2: Помилка на занадто коротке ім'я
    public function testUsernameTooShort() {
        $username = "ki";
        $this->assertFalse(strlen($username) >= 3);
    }
    // Тест 3: Валідний Email
    public function testValidEmail() {
        $email = "developer@gis.com";
        $this->assertNotFalse(filter_var($email, FILTER_VALIDATE_EMAIL));
    }
}
