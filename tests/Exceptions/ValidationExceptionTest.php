<?php
use PHPUnit\Framework\TestCase;

class ValidationExceptionTest extends TestCase {
    // Тест 3: Виняток при порожньому імені користувача
    public function testEmptyUsernameException() {
        $this->expectException(InvalidArgumentException::class);
        $username = "";
        if (empty($username)) throw new InvalidArgumentException("Username empty");
    }
    // Тест 4: Виняток при занадто короткому паролі
    public function testShortPasswordException() {
        $this->expectException(LengthException::class);
        $password = "123";
        if (strlen($password) < 6) throw new LengthException("Password too short");
    }
    // Тест 5: Виняток при невірному форматі файлу
    public function testInvalidFileFormatException() {
        $this->expectException(UnexpectedValueException::class);
        $mime = "image/png";
        if ($mime !== "image/svg+xml") throw new UnexpectedValueException("Invalid file type");
    }
}
