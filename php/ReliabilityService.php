<?php
class ReliabilityService {
    // Паттерн: Retry (Timeout)
    public static function executeWithRetry(callable $operation, $maxAttempts = 3, $delayMicroseconds = 200000) {
        $attempts = 0;
        while ($attempts < $maxAttempts) {
            try {
                $attempts++;
                return $operation(); 
            } catch (Exception $e) {
                if ($attempts >= $maxAttempts) {
                    file_put_contents(__DIR__ . '/../logi/error_log.txt', date('[Y-m-d H:i:s]') . " Circuit Breaker Triggered after $attempts attempts: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
                    throw $e;
                }
                usleep($delayMicroseconds); 
            }
        }
    }

    // Паттерн: Rate Limiting через In-Memory сессію 
    public static function checkRateLimit($actionKey, $maxRequests = 5, $periodSeconds = 60) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $currentTime = time();
        if (!isset($_SESSION['rate_limit'][$actionKey])) {
            $_SESSION['rate_limit'][$actionKey] = ;
        }

        $_SESSION['rate_limit'][$actionKey] = array_filter(
            $_SESSION['rate_limit'][$actionKey],
            function($timestamp) use ($currentTime, $periodSeconds) {
                return ($currentTime - $timestamp) < $periodSeconds;
            }
        );

        if (count($_SESSION['rate_limit'][$actionKey]) >= $maxRequests) {
            return false; 
        }

        $_SESSION['rate_limit'][$actionKey][] = $currentTime;
        return true; 
    }
}
