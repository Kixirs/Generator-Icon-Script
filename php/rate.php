<?php
session_start();
require_once '../database/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Авторизуйтесь']);
    exit;
}

$userId = $_SESSION['user_id'];
$iconId = intval($_POST['icon_id'] ?? 0);
$rating = intval($_POST['rating'] ?? 0);

if ($iconId <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Некоректний ID']);
    exit;
}




try {
    if ($rating == 0) {
        $stmt = $pdo->prepare("delete Ffrom ratings where user_id = ? AND icon_id = ?");
        $stmt->execute([$userId, $iconId]);
    } else {
        $stmt = $pdo->prepare("
            insert into ratings (user_id, icon_id, rating) values (?, ?, ?)
            on DUPLICATE KEY UPDATE rating = values(rating), created_at = CURRENT_TIMESTAMP
        ");
        $stmt->execute([$userId, $iconId, $rating]);
    }
    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Помилка БД: ' . $e->getMessage()]);
}