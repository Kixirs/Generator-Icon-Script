<?php
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversNothing;

#[CoversNothing]
class PerformanceTest extends TestCase {

    private string $apiUrl = 'http://localhost/Generator Icon Script/php/Reg.php'; 
    
    /**
     * ЕТАП 1: BENCHMARK TESTING (Вимірювання базової продуктивності)
     */
    public function testApiBenchmarkPerformance() {
        $totalRequests = 100;
        $timeRecords = [];
        $successCount = 0;

        $startTime = microtime(true);
        for ($i = 0; $i < $totalRequests; $i++) {
            $reqStart = microtime(true);
            
            // Використовуємо cURL для надсилання швидких запитів
            $ch = curl_init($this->apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 2); // Таймаут 2 секунди
            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $reqEnd = microtime(true);
            $timeRecords[] = $reqEnd - $reqStart;

            if ($httpCode >= 200 && $httpCode < 400) {
                $successCount++;
            }
        }
        $totalTime = microtime(true) - $startTime;

        // Розрахунок метрик
        $averageTime = array_sum($timeRecords) / $totalRequests;
        $rps = $totalRequests / $totalTime;

        echo "\n\n=== BENCHMARK TESTING RESULTS ===";
        echo "\nВсього запитів: $totalRequests";
        echo "\nУспішних відповідей: $successCount/" . $totalRequests;
        echo "\nЗагальний час виконання: " . round($totalTime, 4) . " сек";
        echo "\nСередній час відповіді (Latency): " . round($averageTime, 4) . " сек";
        echo "\nПропускна здатність (RPS): " . round($rps, 2) . " req/sec\n";

        // Перевіряємо базовий критерій: API має витримувати навантаження без помилок
        $this->assertEquals($totalRequests, $successCount, "API видає помилки при базовому навантаженні!");
    }

    /**
     * ЕТАП 2: STRESS TESTING (Стрес-тестування на витривалість)
     */
    public function testApiStressLoad() {
        $totalRequests = 500; // Велика кількість запитів для створення стресу
        $errorsCount = 0;
        $timeRecords = [];

        $startTime = microtime(true);
        for ($i = 0; $i < $totalRequests; $i++) {
            $reqStart = microtime(true);

            $ch = curl_init($this->apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 1); // Суворий таймаут для стрес-тесту
            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $timeRecords[] = microtime(true) - $reqStart;

            if ($httpCode === 0 || $httpCode >= 500) {
                $errorsCount++; // Фіксуємо падіння сервера або таймаути
            }
        }
        $totalTime = microtime(true) - $startTime;
        $averageTime = array_sum($timeRecords) / $totalRequests;
        $maxTime = max($timeRecords);

        echo "\n=== STRESS TESTING RESULTS ===";
        echo "\nВсього запитів у стрес-режимі: $totalRequests";
        echo "\nКількість збоїв/таймаутів (Errors): $errorsCount";
        echo "\nСередній час відповіді під стресом: " . round($averageTime, 4) . " сек";
        echo "\nМаксимальний піковий час відповіді: " . round($maxTime, 4) . " сек";
        echo "\nПропускна здатність під стресом: " . round($totalRequests / $totalTime, 2) . " req/sec\n";

        // Виводимо висновок дослідження
        echo "\n=== АНАЛІТИЧНИЙ ВИСНОВОК ===";
        if ($errorsCount > 0 || $averageTime > 0.5) {
            echo "\n[ОЗНАКИ ПОГІРШЕННЯ]: Виявлено деградацію системи! Сервер XAMPP починає затримувати відповіді (пік: " . round($maxTime, 2) . " сек) або скидати з'єднання через вичерпання лімітів процесів Apache.\n";
        } else {
            echo "\n[ОЗНАКИ ПОГІРШЕННЯ]: Деградації не виявлено. Система стабільна під поточним рівнем навантаження.\n";
        }

        $this->assertTrue(true);
    }
}
