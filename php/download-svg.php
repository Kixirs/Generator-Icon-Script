<?php
require_once 'db.php';

// Перевірка наявності ID
if (!isset($_GET['id'])) {
    die('ID не вказано');
}

$id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT name, svg_path FROM icon WHERE id = ?");
$stmt->execute([$id]);
$icon = $stmt->fetch();

if (!$icon || empty($icon['svg_path'])) {
    die('Іконку не знайдено або у неї відсутній SVG-шлях');
}

// Формуємо безпечну назву файлу
$filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $icon['name']) . '.svg';

// Заголовки для примусового завантаження
header('Content-Type: image/svg+xml; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-cache, must-revalidate');

// Виводимо красивий SVG з відступами
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="512" height="512">' . "\n";
echo '  <path d="' . htmlspecialchars($icon['svg_path']) . '" fill="#EACC80"/>' . "\n";
echo '</svg>';
exit;