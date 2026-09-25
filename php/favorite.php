<?php
session_start();
require_once '../database/db.php';

if(!isset($_SESSION['user_id']))
{
    exit;
}

$userId = $_SESSION['user_id'];
$iconId = intval($_POST['icon_id']);

$check = $pdo->prepare("
    SELECT id
    FROM favorites
    WHERE user_id = ?
    AND icon_id = ?
");

$check->execute([
    $userId,
    $iconId
]);

if($check->fetch())
{
    $delete = $pdo->prepare("
        DELETE FROM favorites
        WHERE user_id = ?
        AND icon_id = ?
    ");

    $delete->execute([
        $userId,
        $iconId
    ]);

    echo json_encode([
        'status' => 'removed'
    ]);
}
else
{
    $insert = $pdo->prepare("
        INSERT INTO favorites(user_id, icon_id)
        VALUES(?,?)
    ");

    $insert->execute([
        $userId,
        $iconId
    ]);

    echo json_encode([
        'status' => 'added'
    ]);
}