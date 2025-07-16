<?php

// Check if running in Docker environment
$db_server = isset($_ENV['DB_HOST']) ? $_ENV['DB_HOST'] : (getenv('DB_HOST') ?: 'db');
$db_user = isset($_ENV['DB_USER']) ? $_ENV['DB_USER'] : (getenv('DB_USER') ?: 'root');
$db_password = isset($_ENV['DB_PASSWORD']) ? $_ENV['DB_PASSWORD'] : (getenv('DB_PASSWORD') ?: 'root');
$db_name = isset($_ENV['DB_NAME']) ? $_ENV['DB_NAME'] : (getenv('DB_NAME') ?: 'lama_dev');

// Fallback to localhost for local development (XAMPP)
if ($db_server === 'db' && !gethostbyname('db')) {
    $db_server = 'localhost';
    $db_password = '';
}

try {
    $cnx = new PDO(
        "mysql:host=$db_server;dbname=$db_name;charset=utf8",
        $db_user,
        $db_password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}
