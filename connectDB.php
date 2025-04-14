<?php
function connectDB() {
    $host = 'MySQL-8.0';
    $user = 'root';
    $password = ''; 
    $dbname = 'tabel_fields';

    $conn = new mysqli($host, $user, $password, $dbname);

    if ($conn->connect_error) {
        die("Ошибка подключения к базе данных: " . $conn->connect_error);
    }

    return $conn;
}
?>
