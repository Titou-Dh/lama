<?php

/**
 * Save user preferences for categories
 * 
 * @param PDO $pdo Database connection
 * @param int $userId The user ID
 * @param array $categoryIds Array of category IDs
 * @return bool True if successful, false otherwise
 */
function saveUserPreferences(PDO $pdo, int $userId, array $categoryIds): bool
{
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("DELETE FROM user_preferences WHERE user_id = ?");
        $stmt->execute([$userId]);

        $stmt = $pdo->prepare("INSERT INTO user_preferences (user_id, category_id) VALUES (?, ?)");
        foreach ($categoryIds as $categoryId) {
            $stmt->execute([$userId, $categoryId]);
        }

        $pdo->commit();
        return true;
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Save Preferences Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get user preferences
 * 
 * @param PDO $pdo Database connection
 * @param int $userId The user ID
 * @return array Array of category IDs
 */
function getUserPreferences(PDO $pdo, int $userId): array
{
    try {
        $stmt = $pdo->prepare("
            SELECT category_id
            FROM user_preferences
            WHERE user_id = ? 
            
        ");
        $stmt->execute([$userId]);
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'category_id');
    } catch (PDOException $e) {
        error_log("Get User Preferences Error: " . $e->getMessage());
        return [];
    }
}

/**
 * Get recommended events based on user preferences
 * 
 * @param PDO $pdo Database connection
 * @param int $userId The user ID
 * @param int $limit Maximum number of events to return
 * @return array Array of recommended events
 */
function getRecommendedEvents(PDO $pdo, int $userId, int $limit = 6): array
{
    try {
        $stmt = $pdo->prepare("
            SELECT DISTINCT e.*, c.name as category_name 
            FROM events e
            INNER JOIN user_preferences up ON e.category_id = up.category_id
            INNER JOIN categories c ON e.category_id = c.id
            WHERE up.user_id = :user_id
            AND e.start_date >= CURRENT_TIMESTAMP()
            AND e.status = 'published'
            ORDER BY e.start_date ASC
            LIMIT :limit
        ");

        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($events)) {
            $stmt = $pdo->prepare("
                SELECT e.*, c.name as category_name 
                FROM events e
                INNER JOIN categories c ON e.category_id = c.id
                WHERE e.start_date >= CURRENT_TIMESTAMP()
                AND e.status = 'published'
                ORDER BY e.start_date ASC
                LIMIT :limit
            ");

            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $events;
    } catch (PDOException $e) {
        error_log("Get Recommended Events Error: " . $e->getMessage());
        return [];
    }
}
