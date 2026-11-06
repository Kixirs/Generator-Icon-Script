<?php
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use App\FaultTolerance;

#[CoversNothing]
class ReliabilityTest extends TestCase {

    protected function setUp(): void {
        FaultTolerance::reset(); // Скидаємо стан перед кожним тестом
    }

    // 1. Тестуємо RETRY
    public function testRetryExecutesMultipleTimesOnFailure() {
        $counter = 0;

        try {
            FaultTolerance::retry(function() use (&$counter) {
                $counter++;
                throw new \Exception("Temporary Database Error");
            }, 3, 10); // 3 спроби
        } catch (\Exception $e) {
            // Очікуємо, що помилка врешті випаде
        }

        // Перевіряємо, що функція дійсно викликалася рівно 3 рази
        $this->assertEquals(3, $counter);
    }

    public function testRetryReturnsValueOnSuccessAttempt() {
        $counter = 0;
        
        $result = FaultTolerance::retry(function() use (&$counter) {
            $counter++;
            if ($counter < 2) {
                throw new \Exception("First fail");
            }
            return "SuccessData";
        }, 3, 10);

        $this->assertEquals("SuccessData", $result);
        $this->assertEquals(2, $counter); // Спрацювало з другої спроби
    }

    // 2. Тестуємо TIMEOUT
    public function testTimeoutThrowsExceptionIfOperationIsTooSlow() {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Operation timed out");

        FaultTolerance::timeout(function() {
            usleep(1100000); // Симулюємо довгу відповідь у 1.1 секунди
            return true;
        }, 1); // Ліміт — 1 секунда
    }

    // 3. Тестуємо CIRCUIT BREAKER
    public function testCircuitBreakerOpensOnConsecutiveFailures() {
        // Симулюємо 3 послідовні збої стороннього сервісу
        for ($i = 0; $i < 3; $i++) {
            try {
                FaultTolerance::circuitBreaker(function() {
                    throw new \Exception("API Down");
                });
            } catch (\Exception $e) {
                // Ігноруємо прокидання для накопичення помилок
            }
        }

        // Перевіряємо, що Circuit Breaker перейшов у стан OPEN
        $this->assertEquals('OPEN', FaultTolerance::getState());

        // Наступний запит має заблокуватися автоматично без виклику функції
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Circuit Breaker is OPEN");

        FaultTolerance::circuitBreaker(function() {
            return "Should never be called";
        });
    }
}
