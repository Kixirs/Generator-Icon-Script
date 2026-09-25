<?php
require_once '../database/db.php';

$message = "";
$message_type = "";
$step = 1;
$showLoader = false;

if (isset($_POST['step1'])) {
    $email_check = isset($_POST['email']) ? $_POST['email'] : '';
    if (!filter_var($email_check, FILTER_VALIDATE_EMAIL)) {
        $message = "Помилка: введіть коректний email.";
        $message_type = "error";
        $step = 1;
    } else {
        $step = 2;
    }
} elseif (isset($_POST['step2'])) {
    $step = 2;
}

if ($step == 2 && isset($_POST['register_final'])) {
    $nickname = trim(stripslashes(htmlspecialchars($_POST['nickname'])));
    $email = trim(stripslashes(htmlspecialchars($_POST['email'])));
    $country = trim(stripslashes(htmlspecialchars($_POST['country'])));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if(empty($nickname) || empty($email)) {
        $message = "Заповніть всі поля.";
        $message_type = "error";
        $step = 1;
    } elseif ($password !== $confirm_password) {
        $message = "Паролі не співпадають.";
        $message_type = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Невірний формат email.";
        $message_type = "error";
        $step = 1;
    } else {
        $stmt = $pdo->prepare("select id from users where nickname = :nick");
        $stmt->execute([':nick' => $nickname]);
        
        if ($stmt->rowCount() > 0) {
            $message = "Користувач '$nickname' вже існує!";
            $message_type = "error";
            $step = 1;
        } else {
            $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
            $sql = "insert into users (nickname, password, email, country) values (:nick, :pass, :email, :country)";
            try {
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nick'     => $nickname,
                    ':pass'     => $hashed_pass,
                    ':email'    => $email,
                    ':country'  => $country 
                ]);
                $message = "Вітаю, '$nickname'! Реєстрація успішна.";
                $message_type = "success";
                $step = 1;
            } catch (PDOException $e) {
                $message = "Сталася помилка БД: " . $e->getMessage();
                $message_type = "error";
            }
        }
    }
}
?>