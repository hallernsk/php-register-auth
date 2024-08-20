<?php

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "php-register-auth";

$db_connect = mysqli_connect("$servername", "$username", "$password", "$dbname");

if (!$db_connect) {
    die("Ошибка подключения к базе данных: " . mysqli_connect_error());
}
