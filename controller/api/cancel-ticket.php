<?php
header('Content-Type: application/json');
include_once __DIR__ . "/../../config/database.php";
include_once __DIR__ . "/../../config/session.php";
include_once __DIR__ . "/../order.php";

checkSession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['order_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$orderId = intval($input['order_id']);
$userId = $_SESSION['user_id'];

$stmt = $cnx->prepare("
    SELECT id, status 
    FROM orders 
    WHERE id = ? AND user_id = ?
");
$stmt->execute([$orderId, $userId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Order not found or not authorized']);
    exit;
}

if ($order['status'] === 'cancelled') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Order is already cancelled']);
    exit;
}

try {
    $cnx->beginTransaction();

    $stmt = $cnx->prepare("
        UPDATE orders 
        SET status = 'cancelled'
        WHERE id = ?
    ");
    $stmt->execute([$orderId]);


    $stmt = $cnx->prepare("
        SELECT ticket_id, quantity 
        FROM order_items 
        WHERE order_id = ?
    ");
    $stmt->execute([$orderId]);
    $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($orderItems as $item) {
        $stmt = $cnx->prepare("
            UPDATE tickets 
            SET quantity_available = quantity_available + ? 
            WHERE id = ?
        ");
        $stmt->execute([$item['quantity'], $item['ticket_id']]);
    }

    $cnx->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Order cancelled successfully'
    ]);
} catch (Exception $e) {
    $cnx->rollBack();

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while cancelling the order',
        'error' => $e->getMessage()
    ]);
}
