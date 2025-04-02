<?php

// $db_server = 'localhost';
// $db_user = 'root';
// $db_password = '';
// $db_name = 'lama_dev';

// $cnx  = new PDO("mysql:host=$db_server;dbname=$db_name", $db_user, $db_password);


$db_server = '10.123.0.78';
$db_user = 'abddhe_admin_dev';
$db_password = 'lama_dev@123'; // You need to add the password here
$db_name = 'abddhe_admin_devv'; // Assuming the database name is the same
$db_charset = 'utf8';

try {
    $cnx = new PDO(
        "mysql:host=$db_server;dbname=$db_name;charset=$db_charset",
        $db_user,
        $db_password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}
