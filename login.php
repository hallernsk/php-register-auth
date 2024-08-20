<?php
session_start();

require_once 'db_connect.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login']);
    $password = $_POST['password'];
    $captchaResponse = $_POST['smart-token'];

    // Проверка капчи
    $secretKey = "ysc2_rcB9zDPJfOi4Wl2ZL1C9LcKd734dxY3NBm1R2MjO94ceb5b0";
    $verifyUrl = "https://captcha-api.yandex.ru/validate?secret=$secretKey&token=$captchaResponse";

    $response = file_get_contents($verifyUrl);
    $capcaResult = json_decode($response, true);

    if ($capchaResult['status'] == 'ok') {
        $sql = "SELECT * FROM users WHERE email = '$login' OR phone = '$login'";
        $result = mysqli_query($db_connect, $sql);
        $user = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            header('Location: profile.php');
            exit;
        } else {
            $errors[] = 'Неверный логин или пароль.';
        }
    } else {
        // Капча не пройдена
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
        <p><?php echo htmlspecialchars($error); ?></p>
    <?php endforeach; ?>
    <form method="post">
        <label>Email или Телефон: <input type="text" name="login" required></label><br>
        <label>Пароль: <input type="password" name="password" required></label><br>
        <!-- Yandex SmartCaptcha -->
        <div
            id="captcha-container"
            data-sitekey="ysc1_rcB9zDPJfOi4Wl2ZL1C9ihhx607NqhP60TbJ7AFpf5d9ad20"
        ></div>

        <button type="submit">Войти</button>
    </form>

</body>
</html>
