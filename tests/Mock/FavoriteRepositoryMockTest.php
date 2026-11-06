<?php
use PHPUnit\Framework\TestCase;
use App\FavoriteRepository; 

class FavoriteRepositoryMockTest extends TestCase {
    // Тест 4: Мок додавання в обране
    public function testMockAddFavorite() {
        $mock = $this->createMock(FavoriteRepository::class);
        $mock->method('add')->with(1, 42)->willReturn(true);
        $this->assertTrue($mock->add(1, 42));
    }
    // Тест 5: Мок отримання списку обраного
    public function testMockGetUserFavorites() {
        $mock = $this->createMock(FavoriteRepository::class);
        $mock->method('getFavs')->willReturn([42, 100, 105]);
        $this->assertCount(3, $mock->getFavs(1));
    }
}
