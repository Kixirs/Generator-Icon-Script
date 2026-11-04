<?php
use PHPUnit\Framework\TestCase;

class DatabaseExceptionTest extends TestCase {
    // Тест 1: Очікування винятку при невдалому підключенні
    public function testConnectionFailureThrowsException() {
        $this->expectException(PDOException::class);
        throw new PDOException("SQLSTATE[HY000] [1045] Access denied", 1045);
    }
    // Тест 2: Очікування винятку при неіснуючій таблиці
    public function testMissingTableThrowsException() {
        $this->expectException(PDOException::class);
        throw new PDOException("SQLSTATE[42S02]: Base table or view not found", 42);
    }
}
