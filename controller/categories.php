<?php

/**
 * Get all categories from the database
 * 
 * @param PDO $pdo Database connection
 * @return array List of categories
 */
function getCategories(PDO $pdo): array
{
    try {
        $stmt = $pdo->prepare("SELECT * FROM categories ORDER BY name ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Get Categories Error: " . $e->getMessage());
        return [];
    }
}
