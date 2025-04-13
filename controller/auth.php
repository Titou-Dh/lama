<?php

enum AuthResult: int
{
    case SUCCESS = 1;
    case FAILURE = 0;
    case USER_EXISTS = -1;
    case INVALID_CREDENTIALS = -2;
}

/**
 * @param PDO $pdo The database connection
 * @param string $name User's name
 * @param string $email User's email
 * @param string $password User's password
 * @return AuthResult Result of the registration attempt
 */
function registerUser(PDO $pdo, string $name, string $email, string $password): AuthResult
{
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $res = $pdo->query("SELECT * FROM users WHERE email = '$email'");
    if ($res->rowCount() > 0) {
        return AuthResult::USER_EXISTS;
    }

    $username = convertFullNameToUsername($name);

    $stmt = $pdo->prepare("INSERT INTO users (username, full_name, email, password_hash) VALUES (:username, :full_name, :email, :password_hash)");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':full_name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password_hash', $hashedPassword);

    return $stmt->execute() ? AuthResult::SUCCESS : AuthResult::FAILURE;
}

/**
 * @param PDO $pdo The database connection
 * @param string $email User's email
 * @param string $password User's password
 * @return AuthResult Result of the login attempt
 */
function loginUser(PDO $pdo, string $email, string $password): AuthResult
{
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        return AuthResult::INVALID_CREDENTIALS;
    }

    if (password_verify($password, $user['password_hash'])) {
        return AuthResult::SUCCESS;
    } else {
        return AuthResult::INVALID_CREDENTIALS;
    }
}

function convertFullNameToUsername(string $fullName): string
{
    $username = strtolower(str_replace(' ', '_', $fullName));
    $randomId = substr(md5(uniqid()), 0, 4);
    $username = $username . '_' . $randomId;
    return $username;
}


/**
 * Get user by email or ID
 * @param PDO $pdo The database connection
 * @param string $identifier User's email or ID
 * @param bool $isId Whether the identifier is an ID
 * @return array|null User data or null if not found
 */
function getUser(PDO $pdo, string $identifier, bool $isId = false): ?array
{
    $stmt = $pdo->prepare("SELECT * FROM users WHERE " . ($isId ? "id" : "email") . " = :identifier");
    $stmt->bindParam(':identifier', $identifier);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}
