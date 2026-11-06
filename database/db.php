<?php
// Примусово прописуємо змінні для середовища тестування
$_ENV['DB_HOST'] = 'localhost';
$_ENV['DB_NAME'] = 'GIScript';
$_ENV['DB_USER'] = 'root';
$_ENV['DB_PASS'] = '12345678as9F0';

$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value, " \t\n\r\0\x0B\"'");
    }
}

function getDBConnection() {
    static $pdo = null;
    if ($pdo === null) {
        // Явно прописуємо підключення до XAMPP прямо тут для тестів
        $host = 'localhost';
        $db   = 'GIScript';
        $user = 'root';
        $pass = '12345678as9F0'; 

        try {
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT => 2, 
            ];
            $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, $options);
        } catch (PDOException $e) {
            file_put_contents(__DIR__ . '/../logi/error_log.txt', date('[Y-m-d H:i:s]') . " Connection Fail: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
            die("Database connection failed. Please try again later.");
        }
    }
    return $pdo;
}

