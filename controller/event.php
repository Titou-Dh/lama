<?php

/**
 * Event Controller Functions
 * 
 * Contains all functions for managing events using a procedural approach
 */

/**
 * Create a new event
 * 
 * @param PDO $pdo Database connection
 * @param array $eventData Event data
 * @param array $fileData Optional file data for image
 * @return int|false ID of created event or false on failure
 */
function createEvent($pdo, $eventData, $fileData = null)
{
    try {
        // Handle image upload if present
        $imagePath = null;
        if ($fileData && !empty($fileData['name'])) {
            $imageUploadResult = uploadEventImage($fileData);
            if ($imageUploadResult['success']) {
                $imagePath = $imageUploadResult['filepath'];
            }
        }

        // Prepare SQL query
        $sql = "INSERT INTO events (organizer_id, title, description, start_date, end_date, 
                door_time, location, online_link, event_type, image, category_id, capacity, 
                age_restriction, status) 
                VALUES (:organizer_id, :title, :description, :start_date, :end_date, 
                :door_time, :location, :online_link, :event_type, :image, :category_id, :capacity, 
                :age_restriction, :status)";

        $stmt = $pdo->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':organizer_id', $eventData['organizer_id']);
        $stmt->bindParam(':title', $eventData['title']);
        $stmt->bindParam(':description', $eventData['description']);
        $stmt->bindParam(':start_date', $eventData['start_date']);
        $stmt->bindParam(':end_date', $eventData['end_date']);
        $stmt->bindParam(':door_time', $eventData['door_time']);
        $stmt->bindParam(':location', $eventData['location']);
        $stmt->bindParam(':online_link', $eventData['online_link']);
        $stmt->bindParam(':event_type', $eventData['event_type']);
        $stmt->bindParam(':image', $imagePath);
        $stmt->bindParam(':category_id', $eventData['category_id']);
        $stmt->bindParam(':capacity', $eventData['capacity']);
        $stmt->bindParam(':age_restriction', $eventData['age_restriction']);
        $stmt->bindParam(':status', $eventData['status']);

        // Execute query
        $stmt->execute();

        // Return ID of the created event
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        error_log("Event Creation Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get a single event by ID
 * 
 * @param PDO $pdo Database connection
 * @param int $id Event ID
 * @return array|false Event data or false if not found
 */
function getEventById($pdo, $id)
{
    try {
        $sql = "SELECT e.*, c.name as category_name, u.username as organizer_name 
               FROM events e
               LEFT JOIN categories c ON e.category_id = c.id
               LEFT JOIN users u ON e.organizer_id = u.id
               WHERE e.id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Get Event Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get list of events with optional filtering
 * 
 * @param PDO $pdo Database connection
 * @param array $filters Optional filters (category, date range, etc.)
 * @param int $page Page number for pagination
 * @param int $limit Items per page
 * @return array Array of events
 */
function getEvents($pdo, $filters = [], $page = 1, $limit = 10)
{
    try {
        $sql = "SELECT e.*, c.name as category_name, u.username as organizer_name 
               FROM events e
               LEFT JOIN categories c ON e.category_id = c.id
               LEFT JOIN users u ON e.organizer_id = u.id
               WHERE 1=1";

        $params = [];

        // Apply filters
        if (!empty($filters['category_id'])) {
            $sql .= " AND e.category_id = :category_id";
            $params[':category_id'] = $filters['category_id'];
        }

        if (!empty($filters['organizer_id'])) {
            $sql .= " AND e.organizer_id = :organizer_id";
            $params[':organizer_id'] = $filters['organizer_id'];
        }

        if (!empty($filters['event_type'])) {
            $sql .= " AND e.event_type = :event_type";
            $params[':event_type'] = $filters['event_type'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND e.status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['date_from'])) {
            $sql .= " AND e.start_date >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $sql .= " AND e.start_date <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }

        // Add order by
        $sql .= " ORDER BY e.start_date ASC";

        // Add pagination
        $offset = ($page - 1) * $limit;
        $sql .= " LIMIT :limit OFFSET :offset";
        $params[':limit'] = $limit;
        $params[':offset'] = $offset;

        $stmt = $pdo->prepare($sql);

        // Bind parameters
        foreach ($params as $key => $value) {
            if ($key == ':limit' || $key == ':offset') {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value);
            }
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Get Events Error: " . $e->getMessage());
        return [];
    }
}

/**
 * Update an existing event
 * 
 * @param PDO $pdo Database connection
 * @param int $id Event ID
 * @param array $eventData Updated event data
 * @param array $fileData Optional file data for image
 * @return boolean Success or failure
 */
function updateEvent($pdo, $id, $eventData, $fileData = null)
{
    try {
        // First get current event data
        $currentEvent = getEventById($pdo, $id);
        if (!$currentEvent) {
            return false;
        }

        // Handle image upload if present
        $imagePath = $currentEvent['image']; // Default to current image
        if ($fileData && !empty($fileData['name'])) {
            $imageUploadResult = uploadEventImage($fileData);
            if ($imageUploadResult['success']) {
                $imagePath = $imageUploadResult['filepath'];

                // Delete old image if it exists
                if (!empty($currentEvent['image']) && file_exists($_SERVER['DOCUMENT_ROOT'] . $currentEvent['image'])) {
                    unlink($_SERVER['DOCUMENT_ROOT'] . $currentEvent['image']);
                }
            }
        }

        $sql = "UPDATE events SET 
                title = :title, 
                description = :description, 
                start_date = :start_date, 
                end_date = :end_date, 
                door_time = :door_time, 
                location = :location, 
                online_link = :online_link, 
                event_type = :event_type, 
                image = :image,
                category_id = :category_id, 
                capacity = :capacity, 
                age_restriction = :age_restriction, 
                status = :status
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':title', $eventData['title']);
        $stmt->bindParam(':description', $eventData['description']);
        $stmt->bindParam(':start_date', $eventData['start_date']);
        $stmt->bindParam(':end_date', $eventData['end_date']);
        $stmt->bindParam(':door_time', $eventData['door_time']);
        $stmt->bindParam(':location', $eventData['location']);
        $stmt->bindParam(':online_link', $eventData['online_link']);
        $stmt->bindParam(':event_type', $eventData['event_type']);
        $stmt->bindParam(':image', $imagePath);
        $stmt->bindParam(':category_id', $eventData['category_id']);
        $stmt->bindParam(':capacity', $eventData['capacity']);
        $stmt->bindParam(':age_restriction', $eventData['age_restriction']);
        $stmt->bindParam(':status', $eventData['status']);

        // Execute query
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Update Event Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete an event
 * 
 * @param PDO $pdo Database connection
 * @param int $id Event ID
 * @param int $userId User ID (for authorization)
 * @param bool $isAdmin Whether the user is an admin
 * @return boolean Success or failure
 */
function deleteEvent($pdo, $id, $userId, $isAdmin = false)
{
    try {
        // Verify event exists and user has permission
        $currentEvent = getEventById($pdo, $id);
        if (!$currentEvent || ($currentEvent['organizer_id'] != $userId && !$isAdmin)) {
            return false; // Event not found or user doesn't have permission
        }

        // Remove event image if exists
        if (!empty($currentEvent['image']) && file_exists($_SERVER['DOCUMENT_ROOT'] . $currentEvent['image'])) {
            unlink($_SERVER['DOCUMENT_ROOT'] . $currentEvent['image']);
        }

        $sql = "DELETE FROM events WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Delete Event Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Search for events by keywords
 * 
 * @param PDO $pdo Database connection
 * @param string $query Search query
 * @param int $page Page number
 * @param int $limit Items per page
 * @return array Search results
 */
function searchEvents($pdo, $query, $page = 1, $limit = 10)
{
    try {
        // Using FULLTEXT search for better performance and relevancy
        $sql = "SELECT e.*, c.name as category_name, u.username as organizer_name,
               MATCH(e.title, e.description, e.location) AGAINST(:query) as relevance
               FROM events e
               LEFT JOIN categories c ON e.category_id = c.id
               LEFT JOIN users u ON e.organizer_id = u.id
               WHERE MATCH(e.title, e.description, e.location) AGAINST(:query)
               ORDER BY relevance DESC";

        // Add pagination
        $offset = ($page - 1) * $limit;
        $sql .= " LIMIT :limit OFFSET :offset";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':query', $query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Search Events Error: " . $e->getMessage());
        return [];
    }
}

/**
 * Get events featured or upcoming for homepage
 * 
 * @param PDO $pdo Database connection
 * @param int $limit Number of events to fetch
 * @return array Featured events
 */
function getFeaturedEvents($pdo, $limit = 5)
{
    try {
        $sql = "SELECT e.*, c.name as category_name, u.username as organizer_name 
               FROM events e
               LEFT JOIN categories c ON e.category_id = c.id
               LEFT JOIN users u ON e.organizer_id = u.id
               WHERE e.start_date >= CURRENT_DATE()
               ORDER BY e.start_date ASC
               LIMIT :limit";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Get Featured Events Error: " . $e->getMessage());
        return [];
    }
}

/**
 * Get events organized by a specific user
 * 
 * @param PDO $pdo Database connection
 * @param int $userId User ID
 * @param int $page Page number
 * @param int $limit Items per page
 * @return array User's events
 */
function getUserEvents($pdo, $userId, $page = 1, $limit = 10)
{
    try {
        $sql = "SELECT e.*, c.name as category_name
               FROM events e
               LEFT JOIN categories c ON e.category_id = c.id
               WHERE e.organizer_id = :user_id
               ORDER BY e.created_at DESC";

        // Add pagination
        $offset = ($page - 1) * $limit;
        $sql .= " LIMIT :limit OFFSET :offset";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Get User Events Error: " . $e->getMessage());
        return [];
    }
}

/**
 * Upload event image
 * 
 * @param array $fileData File data from $_FILES
 * @return array Result with success status and filepath
 */
function uploadEventImage($fileData)
{
    $result = [
        'success' => false,
        'filepath' => '',
        'error' => ''
    ];

    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/events/';

    // Create directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $filename = uniqid() . '_' . basename($fileData['name']);
    $target_file = $upload_dir . $filename;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is actual image
    $check = getimagesize($fileData['tmp_name']);
    if ($check === false) {
        $result['error'] = "File is not an image.";
        return $result;
    }

    // Allow certain file formats
    if (!in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
        $result['error'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        return $result;
    }

    // Try to upload file
    if (move_uploaded_file($fileData['tmp_name'], $target_file)) {
        $result['success'] = true;
        $result['filepath'] = '/uploads/events/' . $filename; // Store relative path
        return $result;
    } else {
        $result['error'] = "Sorry, there was an error uploading your file.";
        return $result;
    }
}

/**
 * Count total events (for pagination)
 * 
 * @param PDO $pdo Database connection
 * @param array $filters Optional filters
 * @return int Total count of events matching criteria
 */
function countEvents($pdo, $filters = [])
{
    try {
        $sql = "SELECT COUNT(*) FROM events e WHERE 1=1";

        $params = [];

        // Apply filters
        if (!empty($filters['category_id'])) {
            $sql .= " AND e.category_id = :category_id";
            $params[':category_id'] = $filters['category_id'];
        }

        if (!empty($filters['organizer_id'])) {
            $sql .= " AND e.organizer_id = :organizer_id";
            $params[':organizer_id'] = $filters['organizer_id'];
        }

        if (!empty($filters['event_type'])) {
            $sql .= " AND e.event_type = :event_type";
            $params[':event_type'] = $filters['event_type'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND e.status = :status";
            $params[':status'] = $filters['status'];
        }

        if (!empty($filters['date_from'])) {
            $sql .= " AND e.start_date >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $sql .= " AND e.start_date <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }

        $stmt = $pdo->prepare($sql);

        // Bind parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();

        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Count Events Error: " . $e->getMessage());
        return 0;
    }
}
