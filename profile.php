<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

include 'db_connect.php';
$user_id = $_SESSION['user_id'];

$errors = [];
$success = '';

// Получение текущей информации о пользователе
$sql = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($db_connect, $sql);
$user = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];

    // Проверка на уникальность email и телефона (только если они изменены)
    if ($email !== $user['email'] || $phone !== $user['phone']) {
        $sql = "SELECT * FROM users WHERE (email = '$email' OR phone = '$phone') AND id != '$user_id'";
        $result = mysqli_query($db_connect, $sql);

        if (mysqli_fetch_assoc($result)) {
            $errors[] = 'Пользователь с таким email или телефоном уже существует.';
        }
    }

    if (empty($errors)) {
        $update_fields = [];

        // Обновляем только измененные данные
        if ($name !== $user['name']) {
            $update_fields[] = "name = '$name'";
        }

        if ($phone !== $user['phone']) {
            $update_fields[] = "phone = '$phone'";
        }

        if ($email !== $user['email']) {
            $update_fields[] = "email = '$email'";
        }

        if (!empty($password)) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $update_fields[] = "password_hash = '$password_hash'";
        }

        if (!empty($update_fields)) {
            $sql = "UPDATE users SET " . implode(', ', $update_fields) . " WHERE id = '$user_id'";

            if (mysqli_query($db_connect, $sql)) {
                $success = 'Информация обновлена.';
                // Обновление данных пользователя в текущем массиве $user
                $user = array_merge($user, ['name' => $name, 'email' => $email, 'phone' => $phone]);
            } else {
                $errors[] = 'Ошибка при обновлении информации.';
            }
        } else {
            $success = 'Данные не были изменены.';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Профиль</title>
</head>
<body>
    <h2>Профиль</h2>
    <?php foreach ($errors as $error): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endforeach; ?>
    <?php if ($success): ?>
        <p style="color: green;"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>
    <form method="post">
        <label>Имя: <input type="text" name="name" value="<?= htmlspecialchars($user['name']); ?>" required></label><br>
        <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required></label><br>
        <label>Телефон: <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']); ?>" required></label><br>
        <label>Новый пароль: <input type="password" name="password"></label> (Оставьте пустым, если не хотите менять пароль)<br>
        <button type="submit">Обновить</button>
    </form>
</body>
</html>
