<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'];
    $password = MD5($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE login = ? AND password = ?");
    $stmt->execute([$login, $password]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];
        header("Location: " . ($user['role'] === 'admin' ? "admin.php" : "lc.php"));
    } else {
        $error = "Неверный логин или пароль!";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <link rel="stylesheet" href="style.css">
    <title>Авторизация</title>
</head>
<body>
    <header>
        <div class="head">
            <div class="logo">
                <a href="index.php">
                    <img src="img/Logo.png" alt="логотип">
                </a>
            </div>
            <div class="head_info">
                <div class="head_info_2">
                    Уборка офисов и квартир
                </div>
                <div class="head_adres">
                    г.Уфа ул.Белякова 25
                </div>
            </div>
            <div class="head_button">
                <button id="entry" onclick="location.href='login.php'">Вход</button>
            </div>
        </div>
    </header>
    <div class="Form_login">
        <h2>Авторизация</h2>
        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="POST">
            <div>
                <label>Логин</label> 
                <input type="text" name="login" required>
            </div>
            <div>
                <label>Пароль</label> 
                <input type="password" name="password" required>
            </div>
            <button type="submit">Войти</button> <br>
        </form><a href="register.php">Нет аккаунта? Зарегистрироваться</a>
    </div>
</body>
</html>
