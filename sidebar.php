<aside class="sidebar" id="sidebar">
                <div class="sidebar-logo">
                    <i class="fi fi-sr-layout-fluid"></i>
                </div>

<ul class="sidebar-menu">
        <li>
            <a href="home.php?page=home" class="<?php echo ($page == 'home') ? 'active' : ''; ?>" data-title="Головна">
                <i class="fa-solid fa-house"></i>
            </a>
        </li>
        <li>
            <a href="library.php?page=library" class="<?php echo ($page == 'library') ? 'active' : ''; ?>" data-title="Бібліотека">
                <i class="fa-solid fa-folder-open"></i>
            </a>
        </li>
        <li>
            <a href="create.php?page=create" class="<?php echo ($page == 'create') ? 'active' : ''; ?>" data-title="Створити">
                <i class="fa-solid fa-pen-nib"></i>
            </a>
        </li>
        <li>
            <a href="favorites.php?page=favorites"
            class="<?php echo ($page == 'favorites') ? 'active' : ''; ?>"
            data-title="Обране">

                <i class="fa-regular fa-heart"></i>

            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <?php if(isset($_SESSION['user_id'])) : ?>
            <li>
                <a href="profile.php?page=profile" data-title="Профіль">
                    <i class="fa-solid fa-user-check"></i>
                </a>
            </li>

            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
            <li>
                <a href="#" id="adminEnterBtn">
                    <i class="fa-solid fa-shield-halved"></i>
                </a>
            </li>
        <?php endif; ?>

        <?php else :?>
            <li>
                <a href="Login.php?page=login" data-title="Увійти">
                    <i class="fa-solid fa-right-to-bracket"></i>
                </a>
            </li>
        <?php endif; ?>
    </div>
</aside>