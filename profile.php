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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];

    // Проверка на уникальность email и телефона
    $sql = "SELECT * FROM users WHERE (email = '$email' OR phone = '$phone') AND id != '$user_id'";
    $result = mysqli_query($db_connect, $sql);

    if (mysqli_fetch_assoc($result)) {
        $errors[] = 'Пользователь с таким email или телефоном уже существует.';
    }

    if (empty($errors)) {
        $password_hash = $password ? password_hash($password, PASSWORD_DEFAULT) : null;

        if ($password_hash) {
            $sql = "UPDATE users SET name = '$name', email = '$email', phone = '$phone', password_hash = '$password_hash' WHERE id = '$user_id'";
        } else {
            $sql = "UPDATE users SET name = '$name', email = '$email', phone = '$phone' WHERE id = '$user_id'";
        }

        if (mysqli_query($db_connect, $sql)) {
            $success = 'Информация обновлена.';
        } else {
            $errors[] = 'Ошибка при обновлении информации.';
        }
    }
}

// Получение текущей информации о пользователе
$sql = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($db_connect, $sql);
$user = mysqli_fetch_assoc($result);
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
        <label>Имя: <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required></label><br>
        <label>Телефон: <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required></label><br>
        <label>Email: <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required></label><br>
        <label>Новый пароль: <input type="password" name="password"></label> (Оставьте пустым, если не хотите менять пароль)<br>
        <button type="submit">Обновить</button>
    </form>
</body>
</html>
