<?php


/**
 * Create an order for a user
 * @param mixed $cnx  Database connection
 * @param mixed $userId User ID
 * @param mixed $ticketId Ticket ID
 * @param mixed $quantity Quantity of tickets
 * @param mixed $attendeeName Attendee's name
 * @param mixed $attendeeEmail Attendee's email 
 * @throws \Exception
 * @return array{error: string, success: bool|array{order_id: mixed, success: bool, total_amount: float|int}}
 */
function createOrder($cnx, $userId, $ticketId, $quantity, $attendeeName, $attendeeEmail)
{
    try {
        $cnx->beginTransaction();

        $stmt = $cnx->prepare("SELECT * FROM tickets WHERE id = ?");
        $stmt->execute([$ticketId]);
        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$ticket) {
            throw new Exception("Ticket not found");
        }

        if ($ticket['quantity_available'] < $quantity) {
            throw new Exception("Not enough tickets available");
        }

        $totalAmount = $ticket['price'] * $quantity;

        $stmt = $cnx->prepare("INSERT INTO orders (user_id, total_amount, status, payment_method) VALUES (?, ?, 'completed', 'free')");
        $stmt->execute([$userId, $totalAmount]);
        $orderId = $cnx->lastInsertId();

        $stmt = $cnx->prepare("INSERT INTO order_items (order_id, ticket_id, quantity, price, attendee_name, attendee_email) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$orderId, $ticketId, $quantity, $ticket['price'], $attendeeName, $attendeeEmail]);

        $stmt = $cnx->prepare("UPDATE tickets SET quantity_available = quantity_available - ? WHERE id = ?");
        $stmt->execute([$quantity, $ticketId]);

        $cnx->commit();

        return [
            'success' => true,
            'order_id' => $orderId,
            'total_amount' => $totalAmount
        ];
    } catch (Exception $e) {
        $cnx->rollBack();
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}


/**
 * Get order details by order ID
 * @param mixed $cnx  Database connection
 * @param mixed $orderId Order ID
 * @return array|false Order details or false if not found
 */
function getOrderById($cnx, $orderId)
{
    $stmt = $cnx->prepare("
        SELECT o.*, oi.*, t.name as ticket_name, t.description as ticket_description,
               e.title as event_title, e.start_date, e.location, e.image as image
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN tickets t ON oi.ticket_id = t.id
        JOIN events e ON t.event_id = e.id
        WHERE o.id = ?
    ");
    $stmt->execute([$orderId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


/**
 * Get all orders for a user
 * @param mixed $cnx  Database connection
 * @param mixed $userId User ID
 * @return array List of orders
 */
function getUserOrders($cnx, $userId)
{
    $stmt = $cnx->prepare("
        SELECT o.*, oi.*, t.name as ticket_name, t.description as ticket_description,
               e.title as event_title, e.start_date, e.location
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN tickets t ON oi.ticket_id = t.id
        JOIN events e ON t.event_id = e.id
        WHERE o.user_id = ?
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
