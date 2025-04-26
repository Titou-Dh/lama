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
 * @param int $offset Number of events to skip for pagination
 * @param int $categoryId Filter by specific category ID (0 = all categories)
 * @param string $sortBy Sort order ('newest', 'oldest', 'title_asc', 'title_desc')
 * @return array Array of recommended events
 */
function getRecommendedEvents(PDO $pdo, int $userId, int $limit = 6, int $offset = 0, int $categoryId = 0, string $sortBy = 'newest'): array
{
    try {
        // Build the WHERE clause
        $where = "WHERE up.user_id = :user_id
                AND e.start_date >= CURRENT_TIMESTAMP()
                AND e.status = 'published'";

        // Add category filter if specified
        if ($categoryId > 0) {
            $where .= " AND e.category_id = :category_id";
        }

        // Determine sort order
        $orderBy = "ORDER BY ";
        switch ($sortBy) {
            case 'oldest':
                $orderBy .= "e.start_date ASC";
                break;
            case 'title_asc':
                $orderBy .= "e.title ASC";
                break;
            case 'title_desc':
                $orderBy .= "e.title DESC";
                break;
            case 'newest':
            default:
                $orderBy .= "e.start_date DESC";
                break;
        }
        $stmt = $pdo->prepare("
            SELECT DISTINCT e.*, c.name as category_name 
            FROM events e
            INNER JOIN user_preferences up ON e.category_id = up.category_id
            INNER JOIN categories c ON e.category_id = c.id
            $where
            $orderBy
            LIMIT :limit
            OFFSET :offset
        ");

        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        if ($categoryId > 0) {
            $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
        }

        $stmt->execute();

        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($events)) {
            $fallbackWhere = "WHERE e.start_date >= CURRENT_TIMESTAMP()
                          AND e.status = 'published'";

            if ($categoryId > 0) {
                $fallbackWhere .= " AND e.category_id = :category_id";
            }
            $stmt = $pdo->prepare("
                SELECT e.*, c.name as category_name 
                FROM events e
                INNER JOIN categories c ON e.category_id = c.id
                $fallbackWhere
                $orderBy
                LIMIT :limit
                OFFSET :offset
            ");

            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

            if ($categoryId > 0) {
                $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
            }

            $stmt->execute();
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $events;
    } catch (PDOException $e) {
        error_log("Get Recommended Events Error: " . $e->getMessage());
        return [];
    }
}

/**
 * Count total number of recommended events for a user
 * 
 * @param PDO $pdo Database connection
 * @param int $userId User ID
 * @param int $categoryId Filter by specific category ID (0 = all categories)
 * @return int Total number of recommended events
 */
function countRecommendedEvents($pdo, $userId, $categoryId = 0)
{
    try {
        $where = "WHERE up.user_id = :user_id
                AND e.start_date >= CURRENT_TIMESTAMP()
                AND e.status = 'published'";

        if ($categoryId > 0) {
            $where .= " AND e.category_id = :category_id";
        }
        $stmt = $pdo->prepare("
            SELECT COUNT(DISTINCT e.id) as total
            FROM events e
            INNER JOIN user_preferences up ON e.category_id = up.category_id
            INNER JOIN categories c ON e.category_id = c.id
            $where
        ");

        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

        if ($categoryId > 0) {
            $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
        }

        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = $result['total'] ?? 0;

        if ($count == 0) {
            $fallbackWhere = "WHERE e.start_date >= CURRENT_TIMESTAMP()
                          AND e.status = 'published'";

            if ($categoryId > 0) {
                $fallbackWhere .= " AND e.category_id = :category_id";
            }
            $stmt = $pdo->prepare("
                SELECT COUNT(DISTINCT e.id) as total
                FROM events e
                INNER JOIN categories c ON e.category_id = c.id
                $fallbackWhere
            ");

            if ($categoryId > 0) {
                $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
            }

            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $count = $result['total'] ?? 0;
        }

        return $count;
    } catch (PDOException $e) {
        error_log("Count Recommended Events Error: " . $e->getMessage());
        return 0;
    }
}
