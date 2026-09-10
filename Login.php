<?php
    include 'header.php'; 
    include 'Logincontent.php';
?>

<?php if ($showLoader): ?>
    <div id="post-login-loader" style="position: fixed; inset: 0; background: #050505; z-index: 9999; display: flex; justify-content: center; align-items: center; flex-direction: column;">
        <div class="neon-spinner"></div>
        <div class="loading-text">
            Перевірка даних...
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const loader = document.getElementById('post-login-loader');
            setTimeout(() => {
                loader.style.opacity = '0';
                setTimeout(() => {
                    loader.style.display = 'none';
                }, 800);

            }, 2000);

        });
    </script>

<?php endif; ?>
<div class="content">
    <h2>Авторизація</h2>

    <?php if (!empty($message)): ?>
        <div class="message <?php echo $message_type; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <?php if (!$showLoader): ?>
    <form method="post" action="">
        <div class="input-group">
            <label>Нікнейм</label>
            <input type="text" name="nickname" required placeholder="">
        </div>
        <div class="input-group">
            <label>Пароль</label>
            <input type="password" name="password" required placeholder="">
        </div>
        <div class="btn-wrapper">
            <button type="button" class="secondary" onclick="window.location.href='home.php?page=home'">⬅ Назад</button>
            <button type="submit" name="login"> Увійти </button>
        </div>
    </form>
    <?php endif; ?>

<p class="switch-link">
    Немає акаунта?
    <a href="Reg.php">
        Зареєструватися
    </a>
</p>
</div>

<?php include 'footer.php'; ?>