<?php

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
        // Get user with password hash for verification
        $sql = "SELECT password_hash FROM users WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }

        // Update password
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
        // Check if email exists
        $sql = "SELECT id, username FROM users WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            // Don't reveal whether email exists for security
            return ['success' => true, 'message' => 'If your email exists in our system, you will receive a password reset link.'];
        }

        // Generate a unique token
        $token = bin2hex(random_bytes(32));
        $tokenExpiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Store token in database (assuming a password_resets table)
        $sql = "INSERT INTO password_resets (user_id, token, expires_at) 
                VALUES (:user_id, :token, :expires_at)
                ON DUPLICATE KEY UPDATE token = :token, expires_at = :expires_at";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $user['id']);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':expires_at', $tokenExpiry);
        $stmt->execute();

        // Send email with reset link
        $resetLink = "http://{$_SERVER['HTTP_HOST']}/lama/reset-password.php?token=$token";

        // Here you would use your email sending function
        // sendEmail($email, 'Password Reset', "Click here to reset your password: $resetLink");

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
        // Get token information
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

        // Check if token is expired
        if (strtotime($resetInfo['expires_at']) < time()) {
            return ['success' => false, 'message' => 'Token has expired'];
        }

        // Update password
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);

        $sql = "UPDATE users SET password_hash = :password_hash WHERE id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':password_hash', $passwordHash);
        $stmt->bindParam(':user_id', $resetInfo['user_id']);

        $result = $stmt->execute();

        // Delete used token
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

        // Apply filters
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

        // Add order by
        $sql .= " ORDER BY created_at DESC";

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
        $result['filepath'] = '/uploads/profiles/' . $filename; // Store relative path
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

        // Apply filters
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

        // Bind parameters
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
        // First get current user data
        $currentUser = getUserById($pdo, $id);
        if (!$currentUser) {
            return false;
        }

        // Check if email is being changed and already exists
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

        // Check if username is being changed and already exists
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

        // Handle image upload if present
        $imagePath = $currentUser['profile_image']; // Default to current image
        if ($fileData && !empty($fileData['name'])) {
            $imageUploadResult = uploadProfileImage($fileData);
            if ($imageUploadResult['success']) {
                $imagePath = $imageUploadResult['filepath'];

                // Delete old image if it exists
                if (!empty($currentUser['profile_image']) && file_exists($_SERVER['DOCUMENT_ROOT'] . $currentUser['profile_image'])) {
                    unlink($_SERVER['DOCUMENT_ROOT'] . $currentUser['profile_image']);
                }
            }
        }

        // Build SQL query based on provided fields
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

        // Set profile image
        $sql .= "profile_image = :profile_image ";
        $params[':profile_image'] = $imagePath;

        // If password change requested, update it
        if (isset($userData['new_password']) && !empty($userData['new_password'])) {
            $sql .= ", password_hash = :password_hash ";
            $passwordHash = password_hash($userData['new_password'], PASSWORD_DEFAULT);
            $params[':password_hash'] = $passwordHash;
        }

        $sql .= "WHERE id = :id";
        $params[':id'] = $id;

        $stmt = $pdo->prepare($sql);

        // Bind parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        // Execute query
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
function deleteUserAccount($pdo, $id, $password)
{
    try {
        // Get user with password hash for verification
        $sql = "SELECT password_hash, profile_image FROM users WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return false;
        }
        if (!$user || !password_verify($password, $user['password_hash'])) {
            error_log("Delete User Error: Password verification failed for user ID $id");
            return false;
        }

        // Delete user's profile image if exists
        if (!empty($user['profile_image']) && file_exists($_SERVER['DOCUMENT_ROOT'] . $user['profile_image'])) {
            unlink($_SERVER['DOCUMENT_ROOT'] . $user['profile_image']);
        }
        if (!empty($user['profile_image']) && file_exists($_SERVER['DOCUMENT_ROOT'] . $user['profile_image'])) {
            if (!unlink($_SERVER['DOCUMENT_ROOT'] . $user['profile_image'])) {
                error_log("Delete User Error: Failed to delete profile image for user ID $id");
            }
        }

        // Delete user
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id);  // Removed the stray hyphen here
        if (!$stmt->execute()) {
            error_log("Delete User Error: Failed to execute DELETE query for user ID $id");
            return false;
        }
        $result = $stmt->execute();

        return $result;
    } catch (PDOException $e) {
        error_log("Delete User Error: " . $e->getMessage());
        return false;
    }
}
