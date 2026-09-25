<?php
session_start();
require_once '../database/db.php';

$message = "";
$message_type = "";
$showLoader = false;

if (isset($_POST['login'])) {
    $nickname = trim(htmlspecialchars($_POST['nickname']));
    $password = $_POST['password'];

    if(empty($nickname) || empty($password)) {
        $message = "Заковніть всі поля";
        $message_type = "error";
    }
    else {
        $stmt = $pdo->prepare("select * from users 
                                        where nickname = :nick");
        $stmt->execute([':nick' => $nickname]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if(password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nickname'] = $user['nickname'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                header("Location: home.php");
                exit();
            } 
            else {
                $message = "Невірний пароль";
                $message_type = "error";
            }
        } else {
            header("Location: Reg.php");
            exit();
        }
    }
}
?>