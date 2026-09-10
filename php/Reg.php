<?php
    include 'header.php';
    include 'Regcontent.php';
?>

<?php if ($showLoader): ?>
<div id="post-reg-loader" style="position: fixed; inset: 0; background: #050505; z-index: 9999; display: flex; justify-content: center; align-items: center; flex-direction: column;">
    <div class="neon-spinner"></div>
    <div class="loading-text">INITIALIZING USER...</div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const loader = document.getElementById('post-reg-loader');
        setTimeout(() => {
            loader.style.opacity = '0';
            setTimeout(() => {
                loader.style.display = 'none';
            }, 800);
        }, 2000);
    });
</script>
<?php endif; ?>

<!-- ОСНОВНА КАРТКА -->
<div class="content">
    <h2>Реєстрація</h2>
    
    <?php if (!empty($message)): ?>
        <div class="message <?php echo $message_type; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <?php if ($step == 1 && !$showLoader): ?> 
        <form method="post" action="">
            <div class="input-group">
                <label>Нікнейм</label>
                <input type="text" name="nickname" required placeholder="">
            </div>
            
            <div class="input-group">
                <label>Email</label>
                <input type="email" name="email" required placeholder="@gmail.com">
            </div>
            
            <div class="input-group">
                <label>Країна</label>
                    <select name="country" required>
                        <option value="" disabled selected>Оберіть країну зі списку</option>
                        <option value="Україна">🇺🇦 Україна</option>
                        <option value="Польща">🇵 Польща</option>
                        <option value="Німеччина">🇩🇪 Німеччина</option>
                        <option value="США">🇺🇸 США</option>
                        <option value="Велика Британія">🇬🇧 Велика Британія</option>
                        <option value="Франція">🇫🇷 Франція</option>
                        <option value="Італія">🇮🇹 Італія</option>
                        <option value="Іспанія">🇪🇸 Іспанія</option>
                        <option value="Канада">🇨🇦 Канада</option>
                        <option value="Австралія">🇦🇺 Австралія</option>
                        <option value="Японія">🇯🇵 Японія</option>
                        <option value="Південна Корея">🇰🇷 Південна Корея</option>
                        <option value="Китай">🇨🇳 Китай</option>
                        <option value="Індія">🇮🇳 Індія</option>
                        <option value="Бразилія">🇧🇷 Бразилія</option>
                        <option value="Мексика">🇲🇽 Мексика</option>
                        <option value="Аргентина">🇦🇷 Аргентина</option>
                        <option value="Чехія">🇨🇿 Чехія</option>
                        <option value="Швеція">🇸🇪 Швеція</option>
                        <option value="Норвегія">🇳🇴 Норвегія</option>
                        <option value="Фінляндія">🇫 Фінляндія</option>
                        <option value="Нідерланди">🇳🇱 Нідерланди</option>
                        <option value="Бельгія">🇧🇪 Бельгія</option>
                        <option value="Швейцарія">🇨🇭 Швейцарія</option>
                        <option value="Австрія">🇦🇹 Австрія</option>
                        <option value="Інша">🌍 Інша</option>
                    </select>
            </div>

            <div class="btn-wrapper">
                <button type="button" class="secondary" onclick="window.location.href='home.php?page=home'">⬅ Назад</button>                <button type="submit" name="step1" value="1">Далі ➡</button>
            </div>
        </form>

    <?php elseif ($step == 2): ?>
        <form method="post" action="">
            <p style="margin-bottom: 20px; text-align: center; color: var(--text-muted);">
                Вітаємо, <b style="color: var(--neon-color); text-shadow: var(--shadow-glow);">
                <?php echo htmlspecialchars($_POST['nickname'] ?? 'Користувач'); ?>
                </b>!
                <br>Придумайте надійний пароль.
            </p>

            <input type="hidden" name="nickname" value="<?php echo htmlspecialchars($_POST['nickname'] ?? ''); ?>">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            <input type="hidden" name="country" value="<?php echo htmlspecialchars($_POST['country'] ?? ''); ?>">
            <input type="hidden" name="step2" value="1">

            <div class="input-group">
                <label>Пароль</label>
                <input type="password" name="password" required placeholder="">
            </div>

            <div class="input-group">
                <label>Повторіть пароль</label>
                <input type="password" name="confirm_password" required placeholder="">
            </div>

            <div style="display: flex; gap: 15px; margin-top: 10px;">
                <button type="button" class="secondary" onclick="history.back()">⬅ Назад</button>
                <button type="submit" name="register_final">Зареєструватися </button>
            </div>
        </form>
    <?php endif; ?>

<p class="switch-link">
    Вже зареєстровані?
    <a href="login.php">
        Авторизуватися
    </a>
</p>
</div>

<?php include 'footer.php'; ?>