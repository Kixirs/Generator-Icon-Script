<?php
use PHPUnit\Framework\TestCase;
use App\UserRepository; 

class UserRepositoryMockTest extends TestCase {
    // Тест 1: Мок успішного знаходження юзера
    public function testMockFindUser() {
        $mock = $this->createMock(UserRepository::class);
        $mock->method('findByEmail')->willReturn(['id' => 1, 'email' => 'admin@gis.com']);
        $this->assertEquals(1, $mock->findByEmail('admin@gis.com')['id']);
    }
    // Тест 2: Мок реєстрації (повертає True)
    public function testMockUserRegisterSuccess() {
        $mock = $this->createMock(UserRepository::class);
        $mock->method('register')->willReturn(true);
        $this->assertTrue($mock->register('user', 'pass'));
    }
    // Тест 3: Мок невдалої авторизації
    public function testMockLoginFail() {
        $mock = $this->createMock(UserRepository::class);
        $mock->method('login')->willReturn(false);
        $this->assertFalse($mock->login('wrong_user', 'pass'));
    }
}
