<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT role FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();


$is_admin = ($user['role'] === 'admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $service_id = $_POST['service_id'];

    $custom_service = isset($_POST['custom_service']) ? $_POST['custom_service'] : null;


    $payment = $_POST['payment'];
    $desired_date = $_POST['desired_date'];

    

    $service_name = null;
    if ($service_id != 5) {
        $stmt = $conn->prepare("SELECT service_name FROM services WHERE service_id = ?");
        $stmt->execute([$service_id]);
        $service_name = $stmt->fetchColumn();
    }

    try {
        $stmt = $conn->prepare("
            INSERT INTO requests (user_id, address, phone, service_id, service_name, custom_service, preferred_payment, desired_date)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$user_id, $address, $phone, $service_id, $service_name, $custom_service, $payment, $desired_date]);
        $success = "Заявка успешно отправлена!";
    } catch (PDOException $e) {
        die("Ошибка выполнения запроса: " . $e->getMessage());
    }
}


$services = $conn->query("SELECT * FROM services")->fetchAll();

$requests = $conn->prepare("SELECT * FROM requests WHERE user_id = ?");
$requests->execute([$user_id]);
$requests = $requests->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<link rel="stylesheet" href="style.css">
    <title>Личный кабинет</title>
</head>
<body>

<header>
        <div class="head">
            <div class="logo">
                <a href="index.php">
                    <img src="img/Logo.png" alt="логотип">
                </a>>
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

<div class="lich">
    <h2>Личный кабинет</h2><br>
    <a href="logout.php">Выйти</a>

    <?php if ($is_admin): ?>
        <a href="admin.php">Админ Панель</a>
    <?php endif; ?>
    
    <?php if (isset($success)) echo "<div class='alert'>$success</div>"; ?>
    
    <h4>Создать новую заявку</h4> <br>
    <form method="POST">
        <div class="lc_now">
            <label>Адрес</label>
            <input type="text" name="address"required>
        </div>
        <div class="lc_now">
            <label>Телефон</label>
            <input type="text" name="phone" placeholder="+7(XXX)-XXX-XX-XX" required>
        </div>
        <div class="lc_now">
        <label>Вид услуги</label>
        <select name="service_id" onchange="toggleCustomServiceField(this)">
            <?php foreach ($services as $service): ?>
                <option value="<?= $service['service_id'] ?>"><?= $service['service_name'] ?></option>
            <?php endforeach; ?>
        </select>
        </div class="lc_now">
        <div id="custom_service_field" >
            <label>Опишите услугу</label>
            <textarea name="custom_service" id="custom_service"></textarea>
        </div>

        <div class="lc_now">
            <label>Желаемая дата и время</label>
            <input type="datetime-local" name="desired_date" required>
        </div>
        <div class="lc_now">
            <label>Тип оплаты</label>
            <select name="payment">
                <option value="cash">Наличные</option>
                <option value="card">Банковская карта</option>
            </select>
        </div>
        <button type="submit">Отправить заявку</button>
    </form> <br>
    
    <h4>Мои заявки</h4> <br>
    <div class="table1">
        <table class="tab1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Адрес</th>
                <th>Услуга</th>
                <th>Статус</th>
                <th>Причина отмены</th>
                <th>Дата создания</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($requests as $request): ?>
                <tr>
                    <td><?= $request['request_id'] ?></td>
                    <td><?= $request['address'] ?></td>
                    <td><?= $request['service_name'] ?: $request['custom_service'] ?></td>
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
                        <?php if ($request['status'] === 'cancelled'): ?>
                            <?= htmlspecialchars($request['cancellation_reason'] ?: 'Не указана') ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><?= $request['created_at'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

<script>
function toggleCustomServiceField(select) {
    const customServiceField = document.getElementById('custom_service_field');
    customServiceField.style.display = (select.value === '5') ? 'block' : 'none';
}
</script>

</body>
</html>
