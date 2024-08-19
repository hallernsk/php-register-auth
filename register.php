<?php
require_once 'db_connect.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $errors[] = 'Пароли не совпадают.';
    }

    $sql = "SELECT * FROM users WHERE phone = '$phone' OR email = '$email'";
    $result = mysqli_query($db_connect, $sql);

    if (mysqli_fetch_assoc($result)) {
        $errors[] = 'Пользователь с таким телефоном или email уже существует.';
    }

    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (name, phone, email, password_hash) VALUES ('$name', '$phone', '$email', '$password_hash')";
        if (mysqli_query($db_connect, $sql)) {
            header('Location: login.php');
            exit;
        } else {
            $errors[] = 'Ошибка регистрации.';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Регистрация</title>
</head>
<body>
    <h2>Регистрация</h2>
    <?php foreach ($errors as $error): ?>
        <p ><?php echo $error; ?></p>
    <?php endforeach; ?>
    <form method="post">
        <label>Имя: <input type="text" name="name" required></label><br>        
        <label>Телефон: <input type="text" name="phone" required></label><br>
        <label>Email: <input type="email" name="email" required></label><br>
        <label>Пароль: <input type="password" name="password" required></label><br>
        <label>Повторите пароль: <input type="password" name="confirm_password" required></label><br>
        <button type="submit">Зарегистрироваться</button>
    </form>
</body>
</html>
