<?php

// Проверка капчи
function verifyCaptcha($captchaResponse)
{
    $secretKey = "test_secret_key_0987654321";
    $verifyUrl = "https://captcha-api.yandex.ru/validate?secret=$secretKey&token=$captchaResponse";
    $captchaResult = json_decode(file_get_contents($verifyUrl), true);
    return $captchaResult['status'] == 'ok';
}

// Получение пользователя по логину (телефон или email)
function getUserByLogin($db_connect, $login)
{
    $sql = "SELECT * FROM users WHERE email = '$login' OR phone = '$login'";
    $result = mysqli_query($db_connect, $sql);
    return mysqli_fetch_assoc($result);
}
