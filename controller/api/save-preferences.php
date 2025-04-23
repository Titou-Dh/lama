<?php
session_start();
include_once __DIR__ . '/../../config/database.php';
include_once __DIR__ . '/../../controller/preferences.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please log in to save preferences']);
    exit;
}

// Check if we have categories data
if (!isset($_POST['selectedCategories'])) {
    echo json_encode(['status' => 'error', 'message' => 'No categories selected']);
    exit;
}

$selectedCategories = explode(',', $_POST['selectedCategories']);
$userId = $_SESSION['user_id'];

$result = saveUserPreferences($cnx, $userId, $selectedCategories);

if ($result) {
    echo json_encode(['status' => 'success', 'message' => 'Preferences saved successfully!']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to save preferences']);
}
?>
