<?php
namespace App;

use Exception;
use RuntimeException;

class FaultTolerance {
    // Налаштування для Circuit Breaker
    private static int $failureThreshold = 3; // Максимум помилок перед відкриттям збоку
    private static int $retryTimeout = 5;      // Час (у секундах) на охолодження залежності
    
    // Стан Circuit Breaker (зберігаємо у статичних змінних для простоти при прикладі)
    private static string $state = 'CLOSED';   // Може бути CLOSED, OPEN, HALF-OPEN
    private static int $failureCount = 0;
    private static int $lastStateChangeTime = 0;

    /**
     * Паттерн: RETRY (Повторні спроби у разі помилок)
     */
    public static function retry(callable $callback, int $maxAttempts = 3, int $delayMicroseconds = 100000) {
        $attempts = 0;
        
        while (true) {
            try {
                $attempts++;
                return $callback();
            } catch (Exception $e) {
                if ($attempts >= $maxAttempts) {
                    throw $e; // Якщо спроби вичерпані, прокидаємо помилку далі
                }
                usleep($delayMicroseconds); // Затримка перед наступною спробою
            }
        }
    }

    /**
     * Паттерн: TIMEOUT (Обмеження часу очікування операції)
     */
    public static function timeout(callable $callback, int $seconds) {
        // Для CLI терміналу (PHPUnit) використовуємо pcntl, якщо він є, 
        // або мікросекундний замір часу виконання для універсальності на Windows:
        $startTime = microtime(true);
        $result = $callback();
        $endTime = microtime(true);

        if (($endTime - $startTime) > $seconds) {
            throw new RuntimeException("Operation timed out after {$seconds} seconds");
        }

        return $result;
    }

    /**
     * Паттерн: CIRCUIT BREAKER (Припинення запитів до несправних систем)
     */
    public static function circuitBreaker(callable $callback) {
        $currentTime = time();

        // Перевірка стану OPEN: чи настав час перевести в HALF-OPEN?
        if (self::$state === 'OPEN') {
            if (($currentTime - self::$lastStateChangeTime) > self::$retryTimeout) {
                self::$state = 'HALF-OPEN';
            } else {
                throw new RuntimeException("Circuit Breaker is OPEN. Request blocked.");
            }
        }

        try {
            $result = $callback();

            // Якщо в стані HALF-OPEN все пройшло добре — закриваємо мережу
            if (self::$state === 'HALF-OPEN') {
                self::reset();
            }
            return $result;
        } catch (Exception $e) {
            self::$failureCount++;
            
            if (self::$state === 'HALF-OPEN' || self::$failureCount >= self::$failureThreshold) {
                self::$state = 'OPEN';
                self::$lastStateChangeTime = time();
            }
            throw $e;
        }
    }

    public static function reset(): void {
        self::$state = 'CLOSED';
        self::$failureCount = 0;
        self::$lastStateChangeTime = 0;
    }

    public static function getState(): string {
        return self::$state;
    }
}
