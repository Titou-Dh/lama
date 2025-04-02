<?php

$db_server = 'localhost';
$db_user = 'root';
$db_password = '';
$db_name = 'lama_dev';

try {
    $cnx = new PDO(
        "mysql:host=$db_server;dbname=$db_name;charset=utf8",
        $db_user,
        $db_password,

    );
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}
