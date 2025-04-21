<?php

/**
 * Save event tickets
 * 
 * @param PDO $pdo Database connection
 * @param int $eventId Event ID
 * @param array $tickets Tickets data
 * @return boolean Success or failure
 */
function saveEventTickets($pdo, $eventId, $tickets)
{
    try {
        // First delete any existing tickets for this event
        $deleteStmt = $pdo->prepare("DELETE FROM tickets WHERE event_id = :event_id");
        $deleteStmt->bindParam(':event_id', $eventId);
        $deleteStmt->execute();

        // Insert new tickets
        $sql = "INSERT INTO tickets (event_id, name, price, quantity_available, sales_start, sales_end) 
                VALUES (:event_id, :name, :price, :quantity_available, :sales_start, :sales_end)";

        $stmt = $pdo->prepare($sql);

        foreach ($tickets as $ticket) {
            $stmt->bindParam(':event_id', $eventId);
            $stmt->bindParam(':name', $ticket['name']);
            $stmt->bindParam(':price', $ticket['price']);
            $stmt->bindParam(':quantity_available', $ticket['quantity']);
            $stmt->bindParam(':sales_start', $ticket['sale_start']);
            $stmt->bindParam(':sales_end', $ticket['sale_end']);
            $stmt->execute();
        }

        return true;
    } catch (PDOException $e) {
        error_log("Save Tickets Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Save event FAQs
 * 
 * @param PDO $pdo Database connection
 * @param int $eventId Event ID
 * @param array $faqs FAQs data
 * @return boolean Success or failure
 */
function saveEventFaqs($pdo, $eventId, $faqs)
{
    try {
        // First delete any existing FAQs for this event
        $deleteStmt = $pdo->prepare("DELETE FROM faqs WHERE event_id = :event_id");
        $deleteStmt->bindParam(':event_id', $eventId);
        $deleteStmt->execute();

        // Insert new FAQs
        $sql = "INSERT INTO faqs (event_id, question, answer) 
                VALUES (:event_id, :question, :answer)";

        $stmt = $pdo->prepare($sql);

        foreach ($faqs as $faq) {
            $stmt->bindParam(':event_id', $eventId);
            $stmt->bindParam(':question', $faq['question']);
            $stmt->bindParam(':answer', $faq['answer']);
            $stmt->execute();
        }

        return true;
    } catch (PDOException $e) {
        error_log("Save FAQs Error: " . $e->getMessage());
        return false;
    }
}


/**
 * Get all tickets for an event
 * 
 * @param PDO $pdo Database connection
 * @param int $eventId Event ID
 * @return array List of tickets
 */

function getEventTickets($pdo, $eventId)
{
    try {
        $stmt = $pdo->prepare("SELECT * FROM tickets WHERE event_id = :event_id");
        $stmt->bindParam(':event_id', $eventId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Get Tickets Error: " . $e->getMessage());
        return [];
    }
}


/**
 * Get all FAQs for an event
 * 
 * @param PDO $pdo Database connection
 * @param int $eventId Event ID
 * @return array List of FAQs
 */

function getEventFaqs($pdo, $eventId)
{
    try {
        $stmt = $pdo->prepare("SELECT * FROM faqs WHERE event_id = :event_id");
        $stmt->bindParam(':event_id', $eventId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Get FAQs Error: " . $e->getMessage());
        return [];
    }
}
