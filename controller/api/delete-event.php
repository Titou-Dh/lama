<?php
header('Content-Type: application/json');
require_once '../../config/session.php';
require_once '../../config/database.php';
require_once '../event.php';

checkSession();

$eventId = $_GET['id'] ?? null;

if (!$eventId) {
    http_response_code(400);
    echo json_encode(['error' => 'Event ID is required']);
    exit;
}

try {
    $event = getEventById($cnx, $eventId);

    if (!$event) {
        http_response_code(404);
        echo json_encode(['error' => 'Event not found']);
        exit;
    }

    if ($event['organizer_id'] != $_SESSION['user_id'] && !isset($_SESSION['is_admin'])) {
        http_response_code(403);
        echo json_encode(['error' => 'You do not have permission to delete this event']);
        exit;
    }

    $result = deleteEvent($cnx, $eventId, $_SESSION['user_id'], isset($_SESSION['is_admin']));

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Event deleted successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete event']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
