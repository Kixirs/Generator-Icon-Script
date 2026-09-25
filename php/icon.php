<?php
session_start();
require_once '../database/db.php';
include 'header.php';
include "sidebar.php";

if(!isset($_GET['id'])){
    die("<div class='container'><main class='main-content'><h2 style='color:white'>Іконка не знайдена</h2></main></div>");
}

$iconId = intval($_GET['id']);

// Отримуємо поточну іконку
$stmt = $pdo->prepare("SELECT * FROM icon WHERE id = ?");
$stmt->execute([$iconId]);
$icon = $stmt->fetch();

if(!$icon){
    die("<div class='container'><main class='main-content'><h2 style='color:white'>Іконка не знайдена</h2></main></div>");
}

// Перевірка обраного та РЕЙТИНГУ користувача
$isFavorite = false;
$userRating = 0; // 0 - не голосував, 1 - лайк, -1 - дизлайк

if(isset($_SESSION['user_id'])){
    $uid = $_SESSION['user_id'];
    
    // Перевірка обраного
    $favStmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND icon_id = ?");
    $favStmt->execute([$uid, $iconId]);
    $isFavorite = $favStmt->fetch() ? true : false;

    // Перевірка рейтингу
    $rateStmt = $pdo->prepare("SELECT rating FROM ratings WHERE user_id = ? AND icon_id = ?");
    $rateStmt->execute([$uid, $iconId]);
    $userRating = $rateStmt->fetchColumn() ?: 0;
}

// Отримуємо схожі іконки (з тієї ж категорії, випадково, крім поточної)
$relatedStmt = $pdo->prepare("SELECT * FROM icon WHERE category_id = ? AND id != ? ORDER BY RAND() LIMIT 12");
$relatedStmt->execute([$icon['category_id'], $iconId]);
$relatedIcons = $relatedStmt->fetchAll();
?>

<div class="container">
    <main class="main-content">
        
        <div class="icon-page">
            
            <!-- LEFT: PREVIEW -->
            <div class="icon-preview-panel">
                <a href="home.php" class="back-btn-modern">
                    <i class="fa-solid fa-arrow-left"></i> Назад до каталогу
                </a>

                <div class="icon-preview-box" id="previewBox">
                    <i class="<?= htmlspecialchars($icon['icon_class']) ?>"></i>
                </div>

                <div class="icon-preview-meta">
                    ID: #<?= $icon['id'] ?> &bull; Category: <?= $icon['category_id'] ?>
                </div>
            </div>

            <!-- RIGHT: INFO & TOOLS -->
            <div class="icon-info-panel">
                
                <div class="icon-header">
                    <div>
                        <h1 class="icon-title" id="iconTitle"><?= htmlspecialchars($icon['name']) ?></h1>
                        <span style="color:rgba(255,255,255,0.5); font-size:14px;">Free for personal and commercial use</span>
                        
                        <!-- НОВИЙ БЛОК РЕЙТИНГУ -->
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <div class="rating-actions" style="margin-top: 15px; display: flex; gap: 8px; align-items: center;">
                                <button class="rate-btn like-btn <?= $userRating == 1 ? 'active' : '' ?>" data-id="<?= $iconId ?>" data-val="1">
                                    <i class="fa-solid fa-thumbs-up"></i>
                                </button>
                                <button class="rate-btn dislike-btn <?= $userRating == -1 ? 'active' : '' ?>" data-id="<?= $iconId ?>" data-val="-1">
                                    <i class="fa-solid fa-thumbs-down"></i>
                                </button>
                                
                                <?php if($userRating != 0): ?>
                                    <button class="rate-btn remove-rating-btn" data-id="<?= $iconId ?>">
                                        <i class="fa-solid fa-xmark"></i> Забрати відгук
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <p style="color:rgba(255,255,255,0.4); font-size:12px; margin-top:10px;">Увійдіть, щоб оцінити</p>
                        <?php endif; ?>
                    </div>

                    <?php if(isset($_SESSION['user_id'])): ?>
                        <button id="favoriteBtn"
                                class="fav-btn <?= $isFavorite ? 'active' : '' ?>"
                                data-id="<?= $icon['id'] ?>"
                                title="<?= $isFavorite ? 'Видалити з обраного' : 'Додати в обране' ?>">
                            <i class="fa-solid fa-heart"></i>
                        </button>
                    <?php endif; ?>
                </div>

                <!-- HTML CODE -->
                <div class="info-block">
                    <div class="block-title">HTML Code</div>
                    <div class="code-box">
                        <span>&lt;i class="<?= htmlspecialchars($icon['icon_class']) ?>"&gt;&lt;/i&gt;</span>
                        <button class="copy-mini-btn" onclick="copyText(this, '&lt;i class=\'<?= htmlspecialchars($icon['icon_class']) ?>\'&gt;&lt;/i&gt;')">Copy</button>
                    </div>
                </div>

                <!-- CSS CLASS -->
                <div class="info-block">
                    <div class="block-title">CSS Class</div>
                    <div class="code-box">
                        <span><?= htmlspecialchars($icon['icon_class']) ?></span>
                        <button class="copy-mini-btn" onclick="copyText(this, '<?= htmlspecialchars($icon['icon_class']) ?>')">Copy</button>
                    </div>
                </div>

                <!-- DOWNLOAD BUTTONS -->
                <div class="download-grid">
                    <a href="download-svg.php?id=<?= $icon['id'] ?>" class="dl-btn primary">
                        <i class="fa-solid fa-download"></i> Завантажити SVG
                    </a>
                    <a href="#" class="dl-btn" onclick="alert('PNG download logic here')">
                        <i class="fa-solid fa-image"></i> Download PNG
                    </a>
                    <a href="#" class="dl-btn" onclick="alert('React component logic here')">
                        <i class="fa-brands fa-react"></i> React
                    </a>
                    <a href="#" class="dl-btn" onclick="alert('Vue component logic here')">
                        <i class="fa-brands fa-vuejs"></i> Vue
                    </a>
                </div>

            </div>
        </div>

        <!-- RELATED ICONS -->
        <?php if(count($relatedIcons) > 0): ?>
        <div class="related-section">
            <h3 class="related-title">Схожі іконки</h3>
            <div class="related-grid">
                <?php foreach($relatedIcons as $rel): ?>
                    <a href="icon.php?id=<?= $rel['id'] ?>" class="rel-item">
                        <i class="<?= htmlspecialchars($rel['icon_class']) ?>"></i>
                        <span><?= htmlspecialchars($rel['name']) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </main>
</div>

<script>
/* COPY FUNCTION */
function copyText(btn, text) {
    navigator.clipboard.writeText(text).then(() => {
        const originalText = btn.innerText;
        btn.innerText = 'Copied!';
        btn.style.background = 'var(--success)';
        btn.style.color = '#000';
        
        setTimeout(() => {
            btn.innerText = originalText;
            btn.style.background = '';
            btn.style.color = '';
        }, 1500);
    });
}

/* FAVORITE LOGIC */
const favBtn = document.getElementById('favoriteBtn');
if(favBtn){
    favBtn.addEventListener('click', function(){
        const iconId = this.dataset.id;
        
        fetch('favorite.php', {
            method:'POST',
            headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:'icon_id=' + iconId
        })
        .then(r=>r.json())
        .then(data=>{
            if(data.status === 'added'){
                this.classList.add('active');
                this.title = "Видалити з обраного";
            } else {
                this.classList.remove('active');
                this.title = "Додати в обране";
            }
        })
        .catch(err => console.error('Error:', err));
    });
}

/* RATING LOGIC (ЛАЙК / ДИЗЛАЙК / ЗАБРАТИ ВІДГУК) */
document.querySelectorAll('.rate-btn').forEach(btn => {
    btn.addEventListener('click', function(e){
        e.preventDefault();
        const iconId = this.dataset.id;
        const val = this.dataset.val || 0; // 0 для кнопки видалення
        
        fetch('rate.php', { 
            method:'POST', 
            headers:{'Content-Type':'application/x-www-form-urlencoded'}, 
            body:`icon_id=${iconId}&rating=${val}` 
        })
        .then(r=>r.json())
        .then(data=>{
            if(data.status === 'success'){
                location.reload(); // Оновлюємо сторінку для коректного відображення кнопок
            } else {
                alert(data.message || 'Помилка при оцінюванні');
            }
        });
    });
});
</script>

<?php include 'footer.php'; ?>