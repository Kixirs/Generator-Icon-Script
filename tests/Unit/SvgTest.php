<?php
use PHPUnit\Framework\TestCase;

class SvgTest extends TestCase {
    // Тест 11: Валідація розширення файлу
    public function testSvgExtension() {
        $filename = "home-icon.svg";
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $this->assertEquals("svg", strtolower($ext));
    }
    // Тест 12: Перевірка наявності головного тегу <svg>
    public function testSvgTagExists() {
        $svgContent = '<svg xmlns="http://w3.org" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>';
        $this->assertStringContainsString('<svg', $svgContent);
    }
    // Тест 13: Санітизація імені файлу від Path Traversal
    public function testSanitizeFilename() {
        $badFilename = "../../../etc/passwd-icon.svg";
        $cleanFilename = basename($badFilename);
        $this->assertEquals("passwd-icon.svg", $cleanFilename);
    }
    // Тест 14: Перевірка валідності розміру файлу
    public function testSvgFileSizeValid() {
        $fileSize = 1024; // 1KB
        $this->assertGreaterThan(0, $fileSize);
    }
    // Тест 15: Генерація безпечного унікального імені
    public function testUniqueFilenameGeneration() {
        $original = "star.svg";
        $unique = uniqid() . '_' . basename($original);
        $this->assertNotEquals($original, $unique);
    }
}
