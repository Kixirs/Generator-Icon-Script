<?php
session_start();
require_once 'db.php';

// --- AJAX: Додавання категорії ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    header('Content-Type: application/json');
    
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
        echo json_encode(['status' => 'error', 'message' => 'Недостатньо прав']);
        exit;
    }

    $name = trim($_POST['cat_name'] ?? '');
    $iconClass = trim($_POST['cat_icon'] ?? '');

    if (empty($name) || empty($iconClass)) {
        echo json_encode(['status' => 'error', 'message' => 'Заповніть всі поля']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO category (name, icon_class) VALUES (:name, :icon)");
        $stmt->execute([':name' => $name, ':icon' => $iconClass]);
        
        echo json_encode([
            'status' => 'success', 
            'message' => 'Категорію додано!',
            'new_cat' => [
                'id' => $pdo->lastInsertId(),
                'name' => htmlspecialchars($name),
                'icon_class' => htmlspecialchars($iconClass)
            ]
        ]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Помилка БД: ' . $e->getMessage()]);
    }
    exit;
}

// --- AJAX: Видалення іконки (твій старий код) ---
if (isset($_POST['delete_icon_id'])) {
    header('Content-Type: application/json');
    $iconId = intval($_POST['delete_icon_id']);
    
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
        echo json_encode(['status' => 'error', 'message' => 'Недостатньо прав']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM icon WHERE id = :id");
        $stmt->execute([':id' => $iconId]);
        file_put_contents('delete_log.txt', date('Y-m-d H:i:s') . " Admin deleted icon ID: $iconId\n", FILE_APPEND);
        echo json_encode(['status' => 'success', 'message' => 'Іконку видалено']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Помилка БД: ' . $e->getMessage()]);
    }
    exit; 
}

// Перевірка авторизації
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include "header.php";

// Отримання даних
$users = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();
$categories = $pdo->query("SELECT * FROM category ORDER BY id DESC")->fetchAll();

// Пагінація іконок
$perPage = 10;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;
$categoryId = isset($_GET['cat_id']) ? (int)$_GET['cat_id'] : null;

$countSql = $categoryId ? "SELECT COUNT(*) FROM icon WHERE category_id = :id" : "SELECT COUNT(*) FROM icon";
$countStmt = $pdo->prepare($countSql);
if ($categoryId) $countStmt->execute([':id' => $categoryId]); else $countStmt->execute();
$totalPage = ceil($countStmt->fetchColumn() / $perPage);

$iconsSql = "SELECT icon.*, category.name AS category_name FROM icon LEFT JOIN category ON icon.category_id = category.id";
if ($categoryId) $iconsSql .= " WHERE icon.category_id = :id";
$iconsSql .= " ORDER BY icon.id DESC LIMIT :limit OFFSET :offset";

$stmtIcons = $pdo->prepare($iconsSql);
if ($categoryId) $stmtIcons->bindValue(':id', $categoryId, PDO::PARAM_INT);
$stmtIcons->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmtIcons->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmtIcons->execute();
$icons = $stmtIcons->fetchAll();
?> 

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="admin-container">
    <div class="admin-header">
        <h1 class="admin-title">Admin Panel</h1>
        <a href="logout.php" class="logout-btn">Вийти</a>
    </div>

    <div class="top-actions">
        <!-- Кнопка відкриває поп-ап замість переходу на іншу сторінку -->
        <button onclick="openAddCatModal()" class="top-btn">📁 Додати категорію</button>
        <a href="add_icon.php" class="top-btn">➕ Додати іконку</a>
        <a href="home.php" class="top-btn">🏠 На головну</a>

        <form method="GET" class="filter-form">
            <select name="cat_id" onchange="this.form.submit()">
                <option value="">Всі категорії</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>" <?= (isset($_GET['cat_id']) && $_GET['cat_id'] == $category['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <div class="admin-grid">
        <!-- USERS -->
        <div class="admin-card">
            <h2>Користувачі</h2>
            <div class="admin-list">
                <?php foreach ($users as $user): ?>
                    <div class="admin-item">
                        <div class="admin-item-left">
                            <div class="admin-item-title"><?= htmlspecialchars($user['nickname']) ?></div>
                            <div class="admin-item-subtitle">Роль: <span class="<?= $user['role'] == 'admin' ? 'role-admin' : 'role-user' ?>"><?= htmlspecialchars($user['role']) ?></span></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- CATEGORIES -->
        <div class="admin-card">
            <h2>Категорії</h2>
            <div class="admin-list" id="categoriesList">
                <?php foreach ($categories as $category): ?>
                    <div class="admin-item" data-cat-id="<?= $category['id'] ?>">
                        <div class="admin-item-left">
                            <div class="admin-item-title"><i class="<?= htmlspecialchars($category['icon_class']) ?>" style="margin-right:8px;color:var(--gold)"></i><?= htmlspecialchars($category['name']) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
           
        <!-- ICONS -->
        <div class="admin-card">
            <h2>Іконки</h2>
            <div class="pagination">     
                <?php if ($page > 1): ?><a class="page-btn" href="?page=<?= $page - 1 ?>&cat_id=<?= $categoryId ?>">← Назад</a><?php endif; ?>
                <span class="page-info">Сторінка <?= $page ?> з <?= $totalPage ?></span>
                <?php if ($page < $totalPage): ?><a class="page-btn" href="?page=<?= $page + 1 ?>&cat_id=<?= $categoryId ?>">Вперед →</a><?php endif; ?>
            </div>

            <div class="admin-list" id="iconsList">
                <?php foreach ($icons as $icon): ?>
                    <div class="admin-item" data-id="<?= $icon['id'] ?>">
                        <div class="admin-item-left">
                            <div class="icon-preview"><i class="<?= htmlspecialchars($icon['icon_class']) ?>"></i></div>
                            <div class="admin-item-title"><?= htmlspecialchars($icon['name']) ?></div>
                            <div class="admin-item-subtitle"><?= htmlspecialchars($icon['category_name']) ?></div>
                        </div>
                        <button class="action-btn" onclick="openDeleteModal(<?= $icon['id'] ?>)">Видалити</button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Модальне вікно видалення іконки -->
<div id="deleteModal" class="modal-overlay">
    <div class="modal-content">
        <h3 style="color: #fff;">Підтвердіть видалення</h3>
        <p style="color: #aaa;">Ви дійсно хочете видалити цю іконку?</p>
        <div class="modal-buttons">
            <button class="btn-cancel" onclick="closeDeleteModal()">Скасувати</button>
            <button class="btn-confirm" id="confirmDeleteBtn">Видалити</button>
        </div>
    </div>
</div>

<!-- МОДАЛЬНЕ ВІКНО ДОДАВАННЯ КАТЕГОРІЇ -->
<div id="addCatModal" class="modal-overlay">
    <div class="modal-content" style="max-width: 450px; text-align: left;">
        <h3 style="color: var(--gold); margin-bottom: 20px; text-align: center;">Нова категорія</h3>
        
        <div class="input-group" style="margin-bottom: 20px;">
            <label style="font-size: 16px; margin-bottom: 5px;">Назва категорії</label>
            <input type="text" id="newCatName" placeholder="Наприклад: Медицина" style="background: rgba(255,255,255,0.05); border-radius: 8px; padding: 10px; border: 1px solid rgba(255,255,255,0.1);">
        </div>

        <div class="input-group" style="margin-bottom: 25px;">
            <label style="font-size: 16px; margin-bottom: 5px;">CSS клас іконки</label>
            <input type="text" id="newCatIcon" placeholder="Наприклад: fi fi-sr-heart-pulse" style="background: rgba(255,255,255,0.05); border-radius: 8px; padding: 10px; border: 1px solid rgba(255,255,255,0.1);">
            <small style="color: rgba(255,255,255,0.4); font-size: 12px; display: block; margin-top: 5px;">Використовуй класи з Uicons або FontAwesome</small>
        </div>

        <div class="modal-buttons" style="justify-content: flex-end;">
            <button class="btn-cancel" onclick="closeAddCatModal()" style="padding: 10px 20px;">Скасувати</button>
            <button class="btn-confirm" id="confirmAddCatBtn" style="background: var(--gold); color: #000; padding: 10px 20px;">Додати</button>
        </div>
    </div>
</div>

<script>
/* --- ЛОГІКА ВИДАЛЕННЯ ІКОНОК (ТВОЯ СТАРА) --- */
let iconToDeleteId = null;
const deleteModal = document.getElementById('deleteModal');
const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

function openDeleteModal(id) {
    iconToDeleteId = id;
    deleteModal.style.display = 'flex';
}

function closeDeleteModal() {
    deleteModal.style.display = 'none';
    iconToDeleteId = null;
}

confirmDeleteBtn.addEventListener('click', function() {
    if (!iconToDeleteId) return;
    const formData = new FormData();
    formData.append('delete_icon_id', iconToDeleteId);

    fetch(window.location.href, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            document.querySelector(`.admin-item[data-id="${iconToDeleteId}"]`)?.remove();
            closeDeleteModal();
            alert('Іконку успішно видалено!');
        } else {
            alert('Помилка: ' + data.message);
        }
    });
});

/* --- НОВА ЛОГІКА: ДОДАВАННЯ КАТЕГОРІЇ ЧЕРЕЗ ПОП-АП --- */
const addCatModal = document.getElementById('addCatModal');
const confirmAddCatBtn = document.getElementById('confirmAddCatBtn');

function openAddCatModal() {
    addCatModal.style.display = 'flex';
    document.getElementById('newCatName').focus();
}

function closeAddCatModal() {
    addCatModal.style.display = 'none';
    document.getElementById('newCatName').value = '';
    document.getElementById('newCatIcon').value = '';
}

confirmAddCatBtn.addEventListener('click', function() {
    const name = document.getElementById('newCatName').value.trim();
    const icon = document.getElementById('newCatIcon').value.trim();

    if (!name || !icon) {
        alert('Будь ласка, заповніть обидва поля!');
        return;
    }

    const formData = new FormData();
    formData.append('add_category', '1');
    formData.append('cat_name', name);
    formData.append('cat_icon', icon);

    confirmAddCatBtn.disabled = true;
    confirmAddCatBtn.innerText = 'Збереження...';

    fetch(window.location.href, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(data => {
        confirmAddCatBtn.disabled = false;
        confirmAddCatBtn.innerText = 'Додати';

        if (data.status === 'success') {
            // Миттєве додавання нової категорії в список без перезавантаження
            const list = document.getElementById('categoriesList');
            const newItem = document.createElement('div');
            newItem.className = 'admin-item';
            newItem.dataset.catId = data.new_cat.id;
            newItem.innerHTML = `
                <div class="admin-item-left">
                    <div class="admin-item-title">
                        <i class="${data.new_cat.icon_class}" style="margin-right:8px;color:var(--gold)"></i>${data.new_cat.name}
                    </div>
                </div>
            `;
            list.prepend(newItem); // Додаємо на початок списку
            
            // Також оновлюємо селект фільтра
            const select = document.querySelector('select[name="cat_id"]');
            const option = document.createElement('option');
            option.value = data.new_cat.id;
            option.text = data.new_cat.name;
            select.insertBefore(option, select.options[1]);

            closeAddCatModal();
            alert('Категорію успішно додано!');
        } else {
            alert('Помилка: ' + data.message);
        }
    })
    .catch(err => {
        confirmAddCatBtn.disabled = false;
        confirmAddCatBtn.innerText = 'Додати';
        alert('Сталася помилка мережі');
    });
});

// Закриття модалок при кліку поза ними
window.onclick = function(event) {
    if (event.target == deleteModal) closeDeleteModal();
    if (event.target == addCatModal) closeAddCatModal();
}
</script>

</body>
</html>