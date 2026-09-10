<?php
session_start();
require_once 'db.php';
include 'header.php';

// query => На те щоб просто вивести дані(select). 
// fetchAll => Отримання всіх даних. 
// execute => заповнення шаблону даними і відправка у базу(тільки після prepare) 
// prepare => Захист від SQL-ін'єкцій. 
// intval => перетворення будь якого знаачення в ціле число




// (Отримання категорій.)
// ======================
$stmtCategories = $pdo->query("select * from category 
                                order by id");
$categories = $stmtCategories->fetchAll();

// (Отримання ID категорії.)
// ======================
$categoryId = isset($_GET['cat_id']) ? intval($_GET['cat_id']) : null;

// (Визначення поточної сторінки.)
// ======================
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    if($page < 1) {
        $page = 1;
    }

// (Кількість іконок на сторфнці.)
// ======================
$iconsPrePage = 25;
$offset = ($page - 1) * $iconsPrePage; // Скільки пропустити записів (offset).

// (Отримання іконок.)
// ======================
if ($categoryId) {
    // Загальна кількість іконок категорії
    $countStmt = $pdo->prepare("select count(*) from icon 
                                    where category_id = :category_id");
    $countStmt->execute([
        ':category_id' => $categoryId
    ]);
        $totalicon = $countStmt->fetchColumn();

    $stmticon = $pdo->prepare("select * from icon 
                                    where category_id = :category_id 
                                        order by name
                                            limit :limit 
                                                offset :offset");

    $stmticon->bindValue(':category_id' , $categoryId, PDO::PARAM_INT);
    $stmticon->bindValue(':limit' , $iconsPrePage, PDO::PARAM_INT);
    $stmticon->bindValue(':offset' , $offset, PDO::PARAM_INT);

        $stmticon->execute();
        $icons = $stmticon->fetchAll();
} else {
    $icons = [];
    $totalicon = 0;
}

// (Кількість сторінок.)
// ======================
$totalPages = ceil($totalicon / $iconsPrePage);
?>


<div class="container">
    <?php include "sidebar.php"; ?>

    <main class="main-content">
        <!-- Верхня частина: Заголовок та Пошук -->
        <section class="hero-section">
            <h1 class="main-title">Каталог Іконок</h1>
            <p class="subtitle">Переглядай та обирай іконки для своїх проектів</p>
            
            <div class="search-wrapper">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" id="searchInput" class="search-input" placeholder="Пошук іконок...">
            </div>
        </section>



        <!-- Картка категорії -->
        <section class="categories-section">
            <?php foreach ($categories as $category): ?>
                <a
                    href="?cat_id=<?php echo $category['id']; ?>"
                    class="category-card"
                >
                    <i class="<?= htmlspecialchars($category['icon_class'] ?? '') ?>"></i>
                        <span>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </span>
                </a>
            <?php endforeach; ?>
        </section>
        

        <!-- вивід іконок -->
        <section class="icons-section">
        <div class="icons-header">
            <h2 class="icons-title">
                <?php
                    if($categoryId) {
                        foreach ($categories as $category) {
                            if ($category['id'] == $categoryId) {
                                echo htmlspecialchars($category['name']);
                            }
                        }
                    }
                ?>
            </h2>
                <?php if ($categoryId): ?>
                    <a href="home.php" class="back-btn">
                        ← Назад
                    </a>
                <?php endif; ?>
        </div>

            <div class="icons-grid" id="iconsGrid">
            <?php if (count($icons) > 0): ?>
                <?php foreach ($icons as $icon): ?>
                            <a
                                href="icon.php?id=<?php echo $icon['id']; ?>"
                                class="icon-card"
                                data-name="<?php echo strtolower($icon['name']); ?>"
                            >
                        <div class="icon-preview">
                            <i class="<?= htmlspecialchars($icon['icon_class']) ?>"></i>
                        </div>
                        <p>
                            <?= htmlspecialchars($icon['name']) ?>
                        </p>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
<?php endif; ?>
            </div>

            <!-- Пагінація -->
            <div class="pagination">
                <!-- Кнопка назад -->
                 <?php if ($page > 1) : ?>
                    <a
                        href="?<?php
                            if ($categoryId) {
                                echo 'cat_id=' . $categoryId . '&';
                            } echo 'page=' . ($page - 1); ?>"
                            class="pagination-btn pagination-arrow"
                    >
                        ←
                    </a>
                <?php endif; ?>

                <!-- Номери сторінок -->
                <?php for ($i = 1; $i <=$totalPages; $i++): ?>
                    <a 
                        href="?<?php
                            if ($categoryId) {
                                echo 'cat_id=' . $categoryId . '&';
                            } echo 'page=' . $i;
                        ?>"
                        class="pagination-btn <?= ($page == $i) ? 'active' : '' ?>"
                    >
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <!-- Кнопка вперед  -->
                 <?php if ($page < $totalPages) : ?>
                    <a
                        href="?<?php
                            if ($categoryId) {
                                echo 'cat_id=' . $categoryId . '&';
                            } echo 'page=' . ($page + 1); ?>"
                            class="pagination-btn pagination-arrow"
                    >
                        →
                    </a>
                <?php endif; ?>
            </div>
        </section>
    </main>
</div>


<!-- Пошук іконок -->
<script>
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('keyup', 
    function () {
        const value = this.value.toLowerCase();
        const cards = document.querySelectorAll('.icon-card');
            cards.forEach(card => {
                const iconName = card.dataset.name;
                    if (iconName.includes(value)) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
        });

        // Якщо користувач щось шукає, ховаємо пагінацію, бо вона показує лише частину результатів
        if (value.length > 0) {
            if(paginationBlock) paginationBlock.style.display = 'none';
        } else {
            // Якщо поле порожнє, повертаємо пагінацію
            if(paginationBlock) paginationBlock.style.display = 'flex';
        }
    });
</script>

<?php 
include 'footer.php';
?>
