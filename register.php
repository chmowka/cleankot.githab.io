<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $login = $_POST['login'];
    $password = MD5($_POST['password']);

    $stmt = $conn->prepare("INSERT INTO users (fullname, phone, email, login, password) VALUES (?, ?, ?, ?, ?)");
    try {
        $stmt->execute([$fullname, $phone, $email, $login, $password]);
        header("Location: lc.php");
    } catch (PDOException $e) {
        echo "Ошибка: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <link rel="stylesheet" href="style.css">
    <title>Регистрация</title>

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
    <div class="Form_register">
        <h2>Регистрация</h2>
        <form method="POST">
            <div>
                <label>ФИО</label>
                <input type="text" name="fullname" required>
            </div>
            <div>
                <label>Телефон</label>
                <input type="text" name="phone" placeholder="+7(XXX)-XXX-XX-XX" required>
            </div>
            <div>
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div>
                <label>Логин</label>
                <input type="text" name="login" required>
            </div>
            <div>
                <label>Пароль</label>
                <input type="password" name="password" required minlength="6">
            </div>
            <button type="submit">Зарегистрироваться</button>
        </form>
        <a href="login.php">Уже есть аккаунт? Войти</a>
    </div>
</body>
</html>
