<?php
session_start();
require_once '../database/db.php';
include 'header.php';

// Перевірка авторизації
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$message = '';
$error = '';
$isEditing = false;

// Обробка дій форми
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Вхід у режим редагування тексту
    if (isset($_POST['start_edit'])) {
        $isEditing = true;
    }
    
    // 2. Скасування редагування
    elseif (isset($_POST['cancel_edit'])) {
        $isEditing = false;
    }
    
    // 3. Збереження текстових даних
    elseif (isset($_POST['save_profile'])) {
        $nickname = trim($_POST['nickname']);
        $email = trim($_POST['email']);

        if (!empty($nickname) && !empty($email)) {
            $stmt = $pdo->prepare("UPDATE users SET nickname = ?, email = ? WHERE id = ?");
            if ($stmt->execute([$nickname, $email, $userId])) {
                $message = "Дані успішно оновлено!";
                $isEditing = false;
            } else {
                $error = "Помилка SQL при оновленні даних.";
                $isEditing = true;
            }
        } else {
            $error = "Поля не можуть бути порожніми.";
            $isEditing = true;
        }
    }

    // 4. Обробка завантаження файлу (незалежно від інших дій)
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $maxSize = 2 * 1024 * 1024; // 2MB
        
        if (!in_array($_FILES['avatar']['type'], $allowedTypes)) {
            $error = "Недопустимий формат файлу. Дозволено: JPG, PNG, WebP.";
        } elseif ($_FILES['avatar']['size'] > $maxSize) {
            $error = "Файл завеликий (максимум 2MB).";
        } else {
            $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $newName = 'user_' . $userId . '_' . time() . '.' . $ext;
            $uploadDir = __DIR__ . '/uploads/avatars/';
            
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    $error = "Не вдалося створити папку для завантажень.";
                }
            }

            if (empty($error) && move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadDir . $newName)) {
                $oldStmt = $pdo->prepare("SELECT avatar FROM users WHERE id = ?");
                $oldStmt->execute([$userId]);
                $oldAvatar = $oldStmt->fetchColumn();
                
                if ($oldAvatar && file_exists(__DIR__ . '/' . $oldAvatar)) {
                    unlink(__DIR__ . '/' . $oldAvatar);
                }

                $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
                if ($stmt->execute(['uploads/avatars/' . $newName, $userId])) {
                    $message = "Аватарку успішно оновлено!";
                } else {
                    $error = "Файл завантажено, але не вдалося оновити БД.";
                }
            } elseif (empty($error)) {
                $error = "Помилка переміщення файлу.";
            }
        }
    } elseif (isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
        switch ($_FILES['avatar']['error']) {
            case UPLOAD_ERR_INI_SIZE: 
            case UPLOAD_ERR_FORM_SIZE: 
                $error = "Розмір файлу перевищує ліміт сервера."; break;
            case UPLOAD_ERR_PARTIAL: 
                $error = "Файл завантажено лише частково."; break;
            default: 
                $error = "Помилка завантаження (код: " . $_FILES['avatar']['error'] . ")";
        }
    }
}

// Отримання актуальних даних користувача
$stmt = $pdo->prepare("SELECT nickname, email, avatar FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Формування шляху до аватарки
$defaultAvatar = 'https://ui-avatars.com/api/?name=' . urlencode($user['nickname'] ?? 'User') . '&background=EACC80&color=141414&size=256';
$avatarSrc = (!empty($user['avatar']) && file_exists(__DIR__ . '/' . $user['avatar'])) 
    ? htmlspecialchars($user['avatar']) 
    : $defaultAvatar;

// --- ЛОГІКА РЕЙТИНГУВАННЯ ДЛЯ ПРОФІЛЮ ---
$ratePage = max(1, (int)($_GET['rate_page'] ?? 1));
$ratePerPage = 10;
$rateOffset = ($ratePage - 1) * $ratePerPage;

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM ratings WHERE user_id = ?");
$countStmt->execute([$userId]);
$totalRatings = $countStmt->fetchColumn();
$totalRatePages = max(1, ceil($totalRatings / $ratePerPage));

// ВИПРАВЛЕНО: Використовуємо тільки іменовані параметри (:uid, :limit, :offset)
$ratingsStmt = $pdo->prepare("
    SELECT r.id as rating_id, r.rating, r.created_at, i.name, i.icon_class, i.id as icon_id 
    FROM ratings r 
    JOIN icon i ON r.icon_id = i.id 
    WHERE r.user_id = :uid 
    ORDER BY r.created_at DESC 
    LIMIT :limit OFFSET :offset
");

$ratingsStmt->bindValue(':uid', $userId, PDO::PARAM_INT);
$ratingsStmt->bindValue(':limit', $ratePerPage, PDO::PARAM_INT);
$ratingsStmt->bindValue(':offset', $rateOffset, PDO::PARAM_INT);
$ratingsStmt->execute();
$myRatings = $ratingsStmt->fetchAll();
?>

<div class="container">
    <?php include "sidebar.php"; ?>

    <main class="main-content">
        <section class="hero-section">
            <h1 class="main-title">Мій Профіль</h1>
            <p class="subtitle">Керуй своїми особистими даними</p>
        </section>

        <div class="profile-card-modern wide-profile">
            <?php if ($message): ?>
                <div class="message success"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="message error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="profile-form">
                <div class="avatar-section">
                    <div class="avatar-preview">
                        <img src="<?= $avatarSrc . (strpos($avatarSrc, 'http') === false ? '?t='.time() : '') ?>" alt="Avatar" id="avatarImg">
                    </div>
                    <label for="avatarInput" class="avatar-upload-btn">
                        <i class="fa-solid fa-camera"></i> Обрати фото
                    </label>
                    <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display:none;" onchange="previewAvatar(this)">
                    <small style="color:rgba(255,255,255,0.4); display:block; margin-top:8px;">JPG, PNG, WebP (макс. 2MB)</small>
                </div>

                <div class="input-group">
                    <label>Нікнейм</label>
                    <input type="text" name="nickname" value="<?= htmlspecialchars($user['nickname'] ?? '') ?>" <?= !$isEditing ? 'readonly' : '' ?> class="<?= $isEditing ? 'editable-field' : 'readonly-field' ?>">
                </div>

                <div class="input-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" <?= !$isEditing ? 'readonly' : '' ?> class="<?= $isEditing ? 'editable-field' : 'readonly-field' ?>">
                </div>

                <div class="profile-actions">
                    <?php if ($isEditing): ?>
                        <button type="submit" name="save_profile" class="btn-profile-save">
                            <i class="fa-solid fa-check"></i> Зберегти дані
                        </button>
                        <button type="submit" name="cancel_edit" class="btn-profile-cancel">
                            <i class="fa-solid fa-xmark"></i> Скасувати
                        </button>
                    <?php else: ?>
                        <button type="submit" name="start_edit" class="btn-profile-edit">
                            <i class="fa-solid fa-pen"></i> Змінити дані
                        </button>
                        <a href="logout.php" class="btn-profile-logout">
                            <i class="fa-solid fa-right-from-bracket"></i> Вийти
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- ІСТОРІЯ ОЦІНОК У СТИЛІ АДМІН-ПАНЕЛІ -->
        <div class="admin-card ratings-admin-list" style="margin-top: 50px;">
            <h2><i class="fa-solid fa-star" style="color: var(--gold); margin-right: 10px;"></i>Мої оцінки</h2>
            
            <?php if ($totalRatings > 0): ?>
            <div class="pagination">     
                <?php if ($ratePage > 1): ?><a class="page-btn" href="?rate_page=<?= $ratePage - 1 ?>">← Назад</a><?php endif; ?>
                <span class="page-info">Сторінка <?= $ratePage ?> з <?= $totalRatePages ?></span>
                <?php if ($ratePage < $totalRatePages): ?><a class="page-btn" href="?rate_page=<?= $ratePage + 1 ?>">Вперед →</a><?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="admin-list" id="ratingsList">
                <?php if(count($myRatings) > 0): ?>
                    <?php foreach($myRatings as $r): ?>
                        <div class="admin-item" data-rating-id="<?= $r['rating_id'] ?>">
                            <div class="admin-item-left">
                                <div class="icon-preview"><i class="<?= htmlspecialchars($r['icon_class']) ?>"></i></div>
                                <div class="admin-item-title"><?= htmlspecialchars($r['name']) ?></div>
                                <div class="admin-item-subtitle"><?= date('d.m.Y H:i', strtotime($r['created_at'])) ?></div>
                            </div>
                            
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <?php if($r['rating'] == 1): ?>
                                    <span style="color: var(--success); font-size: 18px;" title="Лайк"><i class="fa-solid fa-thumbs-up"></i></span>
                                <?php else: ?>
                                    <span style="color: var(--danger); font-size: 18px;" title="Дизлайк"><i class="fa-solid fa-thumbs-down"></i></span>
                                <?php endif; ?>
                                
                                <button class="action-btn remove-rating-ajax" data-icon="<?= $r['icon_id'] ?>">
                                    <i class="fa-solid fa-xmark"></i> Забрати відгук
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="admin-item" style="justify-content: center; padding: 30px;">
                        <p style="color:rgba(255,255,255,0.5); margin:0;">Ви ще не оцінювали жодну іконку.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatarImg').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// AJAX видалення рейтингу зі списку без перезавантаження
document.querySelectorAll('.remove-rating-ajax').forEach(btn => {
    btn.addEventListener('click', function() {
        if(!confirm('Видалити цю оцінку?')) return;
        
        const item = this.closest('.admin-item');
        
        fetch('rate.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `icon_id=${this.dataset.icon}&rating=0`
        })
        .then(r => r.json())
        .then(data => {
            if(data.status === 'success') {
                item.style.transition = 'all 0.3s ease';
                item.style.opacity = '0';
                item.style.transform = 'translateX(20px)';
                setTimeout(() => {
                    item.remove();
                    const list = document.getElementById('ratingsList');
                    if(list.children.length === 0) {
                        list.innerHTML = `<div class="admin-item" style="justify-content: center; padding: 30px;"><p style="color:rgba(255,255,255,0.5); margin:0;">Ви ще не оцінювали жодну іконку.</p></div>`;
                    }
                }, 300);
            } else {
                alert('Помилка: ' + data.message);
            }
        });
    });
});
</script>

<?php include 'footer.php'; ?>