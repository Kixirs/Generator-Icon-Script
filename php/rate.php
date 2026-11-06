<?php
require_once __DIR__ . '/../database/db.php';
require_once __DIR__ . '/ReliabilityService.php';

if (!ReliabilityService::checkRateLimit('rate_icon', 5, 60)) {
    http_response_code(429); // Too Many Requests
    die(json_encode(["error" => "Too many requests. Please wait a minute."]));
}

try {
    $pdo = getDBConnection();
    ReliabilityService::executeWithRetry(function() use ($pdo) {
        $stmt = $pdo->prepare("INSERT INTO ratings (user_id, icon_id, rating) VALUES (?, ?, ?)");
        $stmt->execute([$_POST['user_id'], $_POST['icon_id'], $_POST['rating']]);
    });
    echo json_encode(["success" => "Rating saved successfully"]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Service temporarily unavailable"]);
}
