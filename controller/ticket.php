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
        $deleteStmt = $pdo->prepare("DELETE FROM tickets WHERE event_id = :event_id");
        $deleteStmt->bindParam(':event_id', $eventId);
        $deleteStmt->execute();

        $sql = "INSERT INTO tickets (event_id, name, price, quantity_available, sales_start, sales_end) 
                VALUES (:event_id, :name, :price, :quantity_available, :sales_start, :sales_end)";

        $stmt = $pdo->prepare($sql);
        foreach ($tickets as $ticket) {
            $stmt->bindValue(':event_id', $eventId);
            $stmt->bindValue(':name', $ticket['name']);
            $stmt->bindValue(':price', $ticket['price']);
            $stmt->bindValue(':quantity_available', $ticket['quantity']);
            $stmt->bindValue(':sales_start', $ticket['sale_start']);
            $stmt->bindValue(':sales_end', $ticket['sale_end']);
            $stmt->execute();

            event_log("Ticket created: " . json_encode($ticket), 'INFO', [
                'event_id' => $eventId,
                'ticket' => $ticket
            ]);
            error_log("Ticket created: " . json_encode($ticket));
        }

        return true;
    } catch (PDOException $e) {
        event_log("Save Tickets Error: " . $e->getMessage(), 'ERROR', [
            'event_id' => $eventId,
            'error' => $e->getMessage()
        ]);
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
        $deleteStmt = $pdo->prepare("DELETE FROM faqs WHERE event_id = :event_id");
        $deleteStmt->bindParam(':event_id', $eventId);
        $deleteStmt->execute();

        $sql = "INSERT INTO faqs (event_id, question, answer) 
                VALUES (:event_id, :question, :answer)";

        $stmt = $pdo->prepare($sql);
        foreach ($faqs as $faq) {
            $stmt->bindValue(':event_id', $eventId);
            $stmt->bindValue(':question', $faq['question']);
            $stmt->bindValue(':answer', $faq['answer']);
            $stmt->execute();


            error_log("FAQ created: " . json_encode($faq));
            event_log("FAQ created: " . json_encode($faq), 'INFO', [
                'event_id' => $eventId,
                'faq' => $faq
            ]);
        }

        return true;
    } catch (PDOException $e) {
        error_log("Save FAQs Error: " . $e->getMessage());
        event_log("Save FAQs Error: " . $e->getMessage(), 'ERROR', [
            'event_id' => $eventId,
            'error' => $e->getMessage()
        ]);
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

// function getEventTickets($pdo, $eventId)
// {
//     try {
//         $stmt = $pdo->prepare("SELECT * FROM tickets WHERE event_id = :event_id");
//         $stmt->bindParam(':event_id', $eventId);
//         $stmt->execute();
//         return $stmt->fetchAll(PDO::FETCH_ASSOC);
//     } catch (PDOException $e) {
//         error_log("Get Tickets Error: " . $e->getMessage());
//         return [];
//     }
// }


/**
 * Get all FAQs for an event
 * 
 * @param PDO $pdo Database connection
 * @param int $eventId Event ID
 * @return array List of FAQs
 */

// function getEventFaqs($pdo, $eventId)
// {
//     try {
//         $stmt = $pdo->prepare("SELECT * FROM faqs WHERE event_id = :event_id");
//         $stmt->bindParam(':event_id', $eventId);
//         $stmt->execute();
//         return $stmt->fetchAll(PDO::FETCH_ASSOC);
//     } catch (PDOException $e) {
//         error_log("Get FAQs Error: " . $e->getMessage());
//         return [];
//     }
// }

