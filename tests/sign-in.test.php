<?php
// Sign-in test suite for LAMA application
// Tests various signin scenarios and logs results

require_once "../config/database.php";
require_once "../controller/auth.php";
$logFilePath = "../tests/test-results.log";

function logTestResult($testName, $status, $message, $response = "")
{
    global $logFilePath;
    $date = date("Y-m-d H:i:s");
    $logEntry = "[$date] TEST: $testName | STATUS: $status | MESSAGE: $message" . PHP_EOL;
    if (!empty($response)) {
        $logEntry .= "RESPONSE: " . json_encode($response) . PHP_EOL;
    }
    $logEntry .= "-------------------------------------------------------------" . PHP_EOL;
    echo $logEntry . "<br>\n";
    file_put_contents($logFilePath, $logEntry, FILE_APPEND);
}

logTestResult("SIGNIN TEST SUITE", "STARTED", "Beginning signin functionality tests");

// Create a test user that we'll use for multiple tests
function createTestUser($pdo)
{
    $name = "Test Signin User";
    $email = "signin_test" . rand(1000, 9999) . "@example.com";
    $password = "Password123!";
    $result = registerUser($pdo, $name, $email, $password);
    if ($result !== AuthResult::SUCCESS) {
        logTestResult("Test User Creation", "FAILURE", "Failed to create test user for signin tests");
        return null;
    }
    return [
        'email' => $email,
        'password' => $password,
        'name' => $name
    ];
}

// Test case 1: Successful sign in with valid credentials
function testSuccessfulSignin($pdo, $testUser)
{
    $result = loginUser($pdo, $testUser['email'], $testUser['password']);
    if ($result === AuthResult::SUCCESS) {
        logTestResult("Valid Sign In", "SUCCESS", "Successfully signed in with valid credentials", [
            "email" => $testUser['email']
        ]);
        return true;
    } else {
        logTestResult("Valid Sign In", "FAILURE", "Failed to sign in with valid credentials", [
            "email" => $testUser['email'],
            "result_code" => $result
        ]);
        return false;
    }
}

// Test case 2: Failed sign in with incorrect password
function testIncorrectPassword($pdo, $testUser)
{
    $result = loginUser($pdo, $testUser['email'], "WrongPassword123!");
    if ($result === AuthResult::INVALID_CREDENTIALS) {
        logTestResult("Incorrect Password", "SUCCESS", "Correctly rejected sign in with wrong password", [
            "email" => $testUser['email']
        ]);
        return true;
    } else {
        logTestResult("Incorrect Password", "FAILURE", "Incorrectly allowed sign in with wrong password", [
            "email" => $testUser['email'],
            "result_code" => $result
        ]);
        return false;
    }
}

// Test case 3: Failed sign in with non-existent email
function testNonExistentEmail($pdo)
{
    $nonExistentEmail = "nonexistent" . rand(1000, 9999) . "@example.com";
    $result = loginUser($pdo, $nonExistentEmail, "Password123!");
    if ($result === AuthResult::INVALID_CREDENTIALS) {
        logTestResult("Non-existent Email", "SUCCESS", "Correctly rejected sign in with non-existent email", [
            "email" => $nonExistentEmail
        ]);
        return true;
    } else {
        logTestResult("Non-existent Email", "FAILURE", "Incorrectly processed sign in with non-existent email", [
            "email" => $nonExistentEmail,
            "result_code" => $result
        ]);
        return false;
    }
}

// Test case 4: Failed sign in with empty credentials
function testEmptyCredentials($pdo)
{
    $emptyEmailResult = loginUser($pdo, "", "Password123!");
    $emptyPasswordResult = loginUser($pdo, "test@example.com", "");
    $emptyBothResult = loginUser($pdo, "", "");
    $allRejected =
        $emptyEmailResult === AuthResult::INVALID_CREDENTIALS &&
        $emptyPasswordResult === AuthResult::INVALID_CREDENTIALS &&
        $emptyBothResult === AuthResult::INVALID_CREDENTIALS;
    if ($allRejected) {
        logTestResult("Empty Credentials", "SUCCESS", "Correctly rejected sign in with empty credentials", [
            "empty_email_result" => $emptyEmailResult,
            "empty_password_result" => $emptyPasswordResult,
            "empty_both_result" => $emptyBothResult
        ]);
        return true;
    } else {
        logTestResult("Empty Credentials", "FAILURE", "Failed to reject sign in with empty credentials", [
            "empty_email_result" => $emptyEmailResult,
            "empty_password_result" => $emptyPasswordResult,
            "empty_both_result" => $emptyBothResult
        ]);
        return false;
    }
}

// Test case 5: SQL Injection prevention in login
function testSQLInjectionSignin($pdo)
{
    $injectionAttempts = [
        ["' OR '1'='1", "anything"],
        ["anything", "' OR '1'='1"],
        ["' OR '1'='1'; --", "anything"],
        ["admin@example.com' --", "anything"],
        ["' UNION SELECT 1,2,3,4,5,6 --", "anything"]
    ];
    $allProtected = true;
    $results = [];
    foreach ($injectionAttempts as $index => $attempt) {
        $email = $attempt[0];
        $password = $attempt[1];
        $result = loginUser($pdo, $email, $password);
        $results["attempt_" . ($index + 1)] = [
            "email" => $email,
            "password" => $password,
            "result" => $result
        ];
        if ($result === AuthResult::SUCCESS) {
            $allProtected = false;
        }
    }
    if ($allProtected) {
        logTestResult("SQL Injection in Signin", "SUCCESS", "System protected against all SQL injection attempts", [
            "results" => $results
        ]);
        return true;
    } else {
        logTestResult("SQL Injection in Signin", "FAILURE", "System vulnerable to SQL injection in signin", [
            "results" => $results
        ]);
        return false;
    }
}

// Test case 6: Case sensitivity in email
function testEmailCaseSensitivity($pdo, $testUser)
{
    $upperCaseEmail = strtoupper($testUser['email']);
    $result = loginUser($pdo, $upperCaseEmail, $testUser['password']);
    if ($result === AuthResult::SUCCESS) {
        logTestResult("Email Case Sensitivity", "SUCCESS", "System correctly handles case-insensitive emails", [
            "original_email" => $testUser['email'],
            "uppercase_email" => $upperCaseEmail
        ]);
        return true;
    } else {
        logTestResult("Email Case Sensitivity", "FAILURE", "System incorrectly treats emails as case-sensitive", [
            "original_email" => $testUser['email'],
            "uppercase_email" => $upperCaseEmail,
            "result_code" => $result
        ]);
        return false;
    }
}

// Test case 7: Special characters in password
function testSpecialCharactersPassword($pdo)
{
    $name = "Special Chars Password User";
    $email = "special_pass" . rand(1000, 9999) . "@example.com";
    $password = "P@\$\$w0rd!*&^%";
    $regResult = registerUser($pdo, $name, $email, $password);
    if ($regResult !== AuthResult::SUCCESS) {
        logTestResult("Special Characters in Password", "FAILURE", "Failed to create user with special characters in password");
        return false;
    }
    $loginResult = loginUser($pdo, $email, $password);
    if ($loginResult === AuthResult::SUCCESS) {
        logTestResult("Special Characters in Password", "SUCCESS", "Successfully authenticated with special character password", [
            "password" => $password
        ]);
        return true;
    } else {
        logTestResult("Special Characters in Password", "FAILURE", "Failed to authenticate with special character password", [
            "password" => $password,
            "result_code" => $loginResult
        ]);
        return false;
    }
}

// Test case 8: Rate limiting simulation (optional, depending on if rate limiting is implemented)
function testRateLimiting($pdo, $testUser)
{
    $attempts = 10;
    $results = [];
    for ($i = 0; $i < $attempts; $i++) {
        $result = loginUser($pdo, $testUser['email'], "WrongPassword" . $i);
        $results[] = $result;
    }
    $finalResult = loginUser($pdo, $testUser['email'], $testUser['password']);
    logTestResult("Rate Limiting", "INFORMATIONAL", "Rate limiting behavior after multiple failed attempts", [
        "failed_attempts" => $results,
        "subsequent_correct_login" => $finalResult
    ]);
    return $finalResult === AuthResult::SUCCESS;
}

try {
    $testUser = createTestUser($cnx);
    if ($testUser) {
        $testResults = [];
        $testResults["Valid Sign In"] = testSuccessfulSignin($cnx, $testUser);
        $testResults["Incorrect Password"] = testIncorrectPassword($cnx, $testUser);
        $testResults["Non-existent Email"] = testNonExistentEmail($cnx);
        $testResults["Empty Credentials"] = testEmptyCredentials($cnx);
        $testResults["SQL Injection"] = testSQLInjectionSignin($cnx);
        $testResults["Email Case Sensitivity"] = testEmailCaseSensitivity($cnx, $testUser);
        $testResults["Special Characters Password"] = testSpecialCharactersPassword($cnx);
        $testResults["Rate Limiting"] = testRateLimiting($cnx, $testUser);
        $passedTests = array_filter($testResults);
        $totalTests = count($testResults);
        $passedCount = count($passedTests);
        $successRate = ($passedCount / $totalTests) * 100;
        logTestResult(
            "SIGNIN TEST SUITE",
            "COMPLETED",
            "Tests completed with $passedCount/$totalTests successful ($successRate% pass rate)",
            $testResults
        );
    } else {
        logTestResult("SIGNIN TEST SUITE", "ERROR", "Could not create test user for signin tests");
    }
} catch (Exception $e) {
    logTestResult("SIGNIN TEST SUITE", "ERROR", "An error occurred during testing: " . $e->getMessage());
}
