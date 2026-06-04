<?php

try {
    $dsn = 'mysql:host=102.210.149.248;port=3306;dbname=laravel;charset=utf8mb4';
    $username = 'laraveluser';
    $password = 'Ferroh@2024';

    $db = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    echo 'Database connection successful!';
} catch (PDOException $e) {
    exit('Database connection failed: '.$e->getMessage());
}
