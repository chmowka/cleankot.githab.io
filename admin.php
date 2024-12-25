<?php
require 'db.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $request_id = $_POST['request_id'];
    $reason = $_POST['cancellation_reason'] ?? null;

    $stmt = $conn->prepare("UPDATE requests SET status = ?, cancellation_reason = ? WHERE request_id = ?");
    $stmt->execute([$status, $reason, $request_id]);
}

$requests = $conn->query("SELECT r.*, u.fullname, u.phone FROM requests r JOIN users u ON r.user_id = u.user_id")->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <link rel="stylesheet" href="style.css">
    <title>Админ-панель</title>
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
    <div class="adm" >
        <h2>Админ-панель</h2><br>
        <a href="lc.php">Создать новую заявку</a>
        <a href="logout.php">Выйти</a>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ФИО</th>
                    <th>Адрес</th>
                    <th>Телефон</th>
                    <th>Услуга</th>
                    <th>Описание другой услуги</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $request): ?>
                    <tr>
                        <td><?= $request['request_id'] ?></td>
                        <td><?= htmlspecialchars($request['fullname']) ?></td>
                        <td><?= htmlspecialchars($request['address']) ?></td>
                        <td><?= htmlspecialchars($request['phone']) ?></td>
                        <td><?= htmlspecialchars($request['service_name'] ?: 'Другая услуга') ?></td>
                        <td>
                            <?php 
                                if ($request['service_id'] == 5) {
                                    echo htmlspecialchars($request['custom_service']);
                                } else {
                                    echo '-';
                                }
                            ?>
                        </td>
                        <td>
                            <?php if ($request['status'] === 'cancelled'): ?>
                                <span>Отменено</span>
                            <?php elseif ($request['status'] === 'completed'): ?>
                                <span>Выполнено</span>
                            <?php elseif ($request['status'] === 'in_progress'): ?>
                                <span>В работе</span>
                            <?php else: ?>
                                <span>Новая</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="request_id" value="<?= $request['request_id'] ?>">
                                <select name="status">
                                    <option value="in_progress">В работе</option>
                                    <option value="completed">Выполнено</option>
                                    <option value="cancelled">Отменено</option>
                                </select>
                                <input type="text" name="cancellation_reason" placeholder="Причина отмены">
                                <button type="submit">Обновить</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
