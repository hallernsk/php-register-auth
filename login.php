<?php
session_start();

require_once 'db_connect.php';
require_once 'functions.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $password = $_POST['password'];
    $captchaResponse = $_POST['smart-token'];

    // Проверка капчи
    if ($verifyCaptcha($captchaResponse)) {
        // Получение данных пользователя
        $user = getUserByLogin($db_connect, $login);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            header('Location: profile.php');
            exit;
        } else {
            $errors[] = 'Неверный логин или пароль.';
        }
    } else {
        $errors[] = 'Ошибка: капча не пройдена. Попробуйте еще раз.';
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <script src="https://smartcaptcha.yandexcloud.net/captcha.js" defer></script>
    <title>Авторизация</title>
</head>
<body>
    <h2>Авторизация</h2>
    <?php foreach ($errors as $error): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endforeach; ?>
    <form method="post">
        <label>Email или Телефон: <input type="text" name="login" required></label><br>
        <label>Пароль: <input type="password" name="password" required></label><br>
        <!-- Yandex SmartCaptcha -->
        <div
            class="smart-captcha"
            data-sitekey="test_site_key_1234567890"
        ></div>

        <button type="submit">Войти</button>
    </form>

</body>
</html>
