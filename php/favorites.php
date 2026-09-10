<?php
session_start();
require_once 'db.php';
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit;
}

$stmt = $pdo->prepare("
    SELECT icon.*
    FROM favorites
    INNER JOIN icon ON favorites.icon_id = icon.id
    WHERE favorites.user_id = ?
    ORDER BY favorites.created_at DESC
");

$stmt->execute([$_SESSION['user_id']]);
$favorites = $stmt->fetchAll();
?>

<div class="container">
    <?php include "sidebar.php"; ?>

    <main class="main-content">

        <section class="hero-section">
            <h1 class="main-title">Обрані іконки</h1>
            <p class="subtitle">Ваша персональна колекція</p>
        </section>

        <section class="icons-section">

            <?php if (count($favorites) > 0): ?>

                <div class="fav-grid-modern">
                    <?php foreach($favorites as $icon): ?>
                        <div class="fav-card-modern" data-id="<?= $icon['id'] ?>">
                            
                            <!-- Icon Link -->
                            <a href="icon.php?id=<?= $icon['id'] ?>" class="fav-icon-box">
                                <i class="<?= htmlspecialchars($icon['icon_class']) ?>"></i>
                            </a>

                            <div class="fav-title">
                                <?= htmlspecialchars($icon['name']) ?>
                            </div>

                            <div class="fav-actions">
                                <a href="icon.php?id=<?= $icon['id'] ?>" class="fav-open">
                                    <i class="fa-solid fa-eye"></i> Деталі
                                </a>
                                <button class="fav-delete" data-id="<?= $icon['id'] ?>">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>

            <?php else: ?>

                <div class="empty-favorites">
                    <i class="fa-regular fa-heart empty-heart"></i>
                    <h2>У вас ще немає обраних іконок</h2>
                    <p>Натисніть на сердечко <i class="fa-solid fa-heart" style="color:var(--success)"></i> на сторінці іконки, щоб зберегти її тут.</p>
                    <a href="home.php" class="back-btn" style="display:inline-block; margin-top:20px;">
                        Перейти до каталогу
                    </a>
                </div>

            <?php endif; ?>

        </section>

    </main>
</div>

<script>
document.querySelectorAll('.fav-delete').forEach(btn => {
    btn.addEventListener('click', function () {
        if(!confirm('Видалити цю іконку з обраного?')) return;

        const id = this.dataset.id;
        const card = this.closest('.fav-card-modern');

        // Анімація видалення
        card.style.transform = "scale(0.8)";
        card.style.opacity = "0";

        fetch('favorite.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'icon_id=' + id
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'removed') {
                setTimeout(() => {
                    card.remove();
                    // Якщо остання картка, можна перезагрузити або показати empty state
                    if(document.querySelectorAll('.fav-card-modern').length === 0) {
                        location.reload(); 
                    }
                }, 300);
            }
        });
    });
});
</script>

<?php include 'footer.php'; ?>