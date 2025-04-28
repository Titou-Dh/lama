<?php

/**
 * CRUD for the user 
 */



/**
 * Get user by ID
 * 
 * @param PDO $pdo Database connection
 * @param int $id User ID
 * @return array|false User data or false if not found
 */
function getUserById($pdo, $id)
{
    try {
        $sql = "SELECT id, username, email, full_name, profile_image, created_at, is_organizer 
                FROM users 
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Get User Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Change user password
 * 
 * @param PDO $pdo Database connection
 * @param int $id User ID
 * @param string $currentPassword Current password
 * @param string $newPassword New password
 * @return array Success status and message
 */
function changeUserPassword($pdo, $id, $currentPassword, $newPassword)
{
    try {
        $sql = "SELECT password_hash FROM users WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }

        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);

        $sql = "UPDATE users SET password_hash = :password_hash WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':password_hash', $passwordHash);
        $stmt->bindParam(':id', $id);

        $result = $stmt->execute();

        return ['success' => $result, 'message' => $result ? 'Password changed successfully' : 'Failed to change password'];
    } catch (PDOException $e) {
        error_log("Change Password Error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Failed to change password. Please try again.'];
    }
}

/**
 * Request password reset
 * 
 * @param PDO $pdo Database connection
 * @param string $email User email
 * @return array Success status and message
 */
function requestPasswordReset($pdo, $email)
{
    try {
        $sql = "SELECT id, username FROM users WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return ['success' => true, 'message' => 'If your email exists in our system, you will receive a password reset link.'];
        }

        $token = bin2hex(random_bytes(32));
        $tokenExpiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $sql = "INSERT INTO password_resets (user_id, token, expires_at) 
                VALUES (:user_id, :token, :expires_at)
                ON DUPLICATE KEY UPDATE token = :token, expires_at = :expires_at";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $user['id']);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expires_at', $tokenExpiry);
        $stmt->execute();

        $resetLink = "http://{$_SERVER['HTTP_HOST']}/lama/reset-password.php?token=$token";


        return ['success' => true, 'message' => 'If your email exists in our system, you will receive a password reset link.'];
    } catch (PDOException $e) {
        error_log("Password Reset Request Error: " . $e->getMessage());
        return ['success' => false, 'message' => 'An error occurred. Please try again later.'];
    }
}

/**
 * Reset password with token
 * 
 * @param PDO $pdo Database connection
 * @param string $token Reset token
 * @param string $newPassword New password
 * @return array Success status and message
 */
function resetPassword($pdo, $token, $newPassword)
{
    try {
        $sql = "SELECT pr.user_id, pr.expires_at
                FROM password_resets pr
                WHERE pr.token = :token";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        $resetInfo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$resetInfo) {
            return ['success' => false, 'message' => 'Invalid or expired token'];
        }

        if (strtotime($resetInfo['expires_at']) < time()) {
            return ['success' => false, 'message' => 'Token has expired'];
        }

        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);

        $sql = "UPDATE users SET password_hash = :password_hash WHERE id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':password_hash', $passwordHash);
        $stmt->bindParam(':user_id', $resetInfo['user_id']);

        $result = $stmt->execute();

        $sql = "DELETE FROM password_resets WHERE token = :token";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        return ['success' => $result, 'message' => $result ? 'Password has been reset successfully' : 'Failed to reset password'];
    } catch (PDOException $e) {
        error_log("Password Reset Error: " . $e->getMessage());
        return ['success' => false, 'message' => 'An error occurred. Please try again later.'];
    }
}

/**
 * Get list of users with optional filtering (admin function)
 * 
 * @param PDO $pdo Database connection
 * @param array $filters Optional filters
 * @param int $page Page number for pagination
 * @param int $limit Items per page
 * @return array Array of users
 */
function getUsers($pdo, $filters = [], $page = 1, $limit = 10)
{
    try {
        $sql = "SELECT id, username, email, full_name, profile_image, created_at, is_organizer 
                FROM users 
                WHERE 1=1";

        $params = [];

        if (!empty($filters['username'])) {
            $sql .= " AND username LIKE :username";
            $params[':username'] = '%' . $filters['username'] . '%';
        }

        if (!empty($filters['email'])) {
            $sql .= " AND email LIKE :email";
            $params[':email'] = '%' . $filters['email'] . '%';
        }

        if (isset($filters['is_organizer'])) {
            $sql .= " AND is_organizer = :is_organizer";
            $params[':is_organizer'] = $filters['is_organizer'] ? 1 : 0;
        }

        $sql .= " ORDER BY created_at DESC";

        $offset = ($page - 1) * $limit;
        $sql .= " LIMIT :limit OFFSET :offset";
        $params[':limit'] = $limit;
        $params[':offset'] = $offset;

        $stmt = $pdo->prepare($sql);

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
        error_log("Get Users Error: " . $e->getMessage());
        return [];
    }
}

/**
 * Upload profile image
 * 
 * @param array $fileData File data from $_FILES
 * @return array Result with success status and filepath
 */
function uploadProfileImage($fileData)
{
    $result = [
        'success' => false,
        'filepath' => '',
        'error' => ''
    ];

    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/profiles/';

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $filename = uniqid() . '_' . basename($fileData['name']);
    $target_file = $upload_dir . $filename;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($fileData['tmp_name']);
    if ($check === false) {
        $result['error'] = "File is not an image.";
        return $result;
    }

    if (!in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
        $result['error'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        return $result;
    }

    if (move_uploaded_file($fileData['tmp_name'], $target_file)) {
        $result['success'] = true;
        $result['filepath'] = '/uploads/profiles/' . $filename;
        return $result;
    } else {
        $result['error'] = "Sorry, there was an error uploading your file.";
        return $result;
    }
}

/**
 * Count total users (for pagination)
 * 
 * @param PDO $pdo Database connection
 * @param array $filters Optional filters
 * @return int Total count of users matching criteria
 */
function countUsers($pdo, $filters = [])
{
    try {
        $sql = "SELECT COUNT(*) FROM users WHERE 1=1";

        $params = [];

        if (!empty($filters['username'])) {
            $sql .= " AND username LIKE :username";
            $params[':username'] = '%' . $filters['username'] . '%';
        }

        if (!empty($filters['email'])) {
            $sql .= " AND email LIKE :email";
            $params[':email'] = '%' . $filters['email'] . '%';
        }

        if (isset($filters['is_organizer'])) {
            $sql .= " AND is_organizer = :is_organizer";
            $params[':is_organizer'] = $filters['is_organizer'] ? 1 : 0;
        }

        $stmt = $pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();

        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Count Users Error: " . $e->getMessage());
        return 0;
    }
}
/**
 * Update user profile
 * 
 * @param PDO $pdo Database connection
 * @param int $id User ID
 * @param array $userData Updated user data
 * @param array $fileData Optional profile image
 * @return boolean Success or failure
 */
function updateUserProfile($pdo, $id, $userData, $fileData = null)
{
    try {
        $currentUser = getUserById($pdo, $id);
        if (!$currentUser) {
            return false;
        }

        if (isset($userData['email']) && $userData['email'] !== $currentUser['email']) {
            $sql = "SELECT id FROM users WHERE email = :email AND id != :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':email', $userData['email']);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return false;
            }
        }

        if (isset($userData['username']) && $userData['username'] !== $currentUser['username']) {
            $sql = "SELECT id FROM users WHERE username = :username AND id != :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':username', $userData['username']);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return false;
            }
        }

        $imagePath = $currentUser['profile_image'];
        if ($fileData && !empty($fileData['name'])) {
            $imageUploadResult = uploadProfileImage($fileData);
            if ($imageUploadResult['success']) {
                $imagePath = $imageUploadResult['filepath'];

                if (!empty($currentUser['profile_image']) && file_exists($_SERVER['DOCUMENT_ROOT'] . $currentUser['profile_image'])) {
                    unlink($_SERVER['DOCUMENT_ROOT'] . $currentUser['profile_image']);
                }
            }
        }

        $sql = "UPDATE users SET ";
        $params = [];

        if (isset($userData['email'])) {
            $sql .= "email = :email, ";
            $params[':email'] = $userData['email'];
        }

        if (isset($userData['full_name'])) {
            $sql .= "full_name = :full_name, ";
            $params[':full_name'] = $userData['full_name'];
        }

        if (isset($userData['username'])) {
            $sql .= "username = :username, ";
            $params[':username'] = $userData['username'];
        }

        if (isset($userData['is_organizer'])) {
            $sql .= "is_organizer = :is_organizer, ";
            $isOrganizer = $userData['is_organizer'] ? 1 : 0;
            $params[':is_organizer'] = $isOrganizer;
        }

        $sql .= "profile_image = :profile_image ";
        $params[':profile_image'] = $imagePath;

        if (isset($userData['new_password']) && !empty($userData['new_password'])) {
            $sql .= ", password_hash = :password_hash ";
            $passwordHash = password_hash($userData['new_password'], PASSWORD_DEFAULT);
            $params[':password_hash'] = $passwordHash;
        }

        $sql .= "WHERE id = :id";
        $params[':id'] = $id;

        $stmt = $pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $result = $stmt->execute();

        return $result;
    } catch (PDOException $e) {
        error_log("Update User Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete a user account
 * 
 * @param PDO $pdo Database connection
 * @param int $id User ID
 * @param string $password Password confirmation
 * @return boolean Success or failure
 */
function deleteUserAccount($pdo, $userId, $password)
{
    try {
        $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = :id");
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return false;
        }

        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (PDOException $e) {
        error_log("Error deleting user: " . $e->getMessage());
        return false;
    }
}
