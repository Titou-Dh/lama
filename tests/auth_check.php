<?php
session_start();

if (!isset($_SESSION['tester_id'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];

    header('Location: /lama/tests/login.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';
try {
    $stmt = $cnx->prepare("SELECT is_active FROM testers WHERE id = ?");
    $stmt->execute([$_SESSION['tester_id']]);
    $tester = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tester || !$tester['is_active']) {
        session_destroy();
        header('Location: /lama/tests/login.php');
        exit;
    }
} catch (Exception $e) {
    error_log("Error checking tester status: " . $e->getMessage());
}
