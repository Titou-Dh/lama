<?php
session_start();
include_once __DIR__ . '/../../config/database.php';
include_once __DIR__ . '/../preferences.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'success',
        'events' => []
    ]);
    exit;
}

$recommendedEvents = getRecommendedEvents($cnx, $_SESSION['user_id'], 3);

echo json_encode([
    'status' => 'success',
    'events' => $recommendedEvents
]);
