<?php
// Sign-up test suite for LAMA application
// Tests various signup scenarios and logs results

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
    echo $logEntry . "<br> \n";
    file_put_contents($logFilePath, $logEntry, FILE_APPEND);
}

logTestResult("SIGNUP TEST SUITE", "STARTED", "Beginning signup functionality tests");

// Test case 1: Successful signup with valid data
function testSuccessfulSignup($pdo)
{
    $name = "Test User " . rand(1000, 9999);
    $email = "testuser" . rand(1000, 9999) . "@example.com";
    $password = "Password123!";
    $result = registerUser($pdo, $name, $email, $password);
    if ($result === AuthResult::SUCCESS) {
        $user = getUser($pdo, $email);
        $message = "User successfully registered with email: $email";
        logTestResult("Valid User Registration", "SUCCESS", $message, [
            "email" => $email,
            "username" => $user['username']
        ]);
        return true;
    } else {
        logTestResult("Valid User Registration", "FAILURE", "Failed to register valid user", [
            "error_code" => $result
        ]);
        return false;
    }
}

// Test case 2: Duplicate email registration
function testDuplicateEmail($pdo)
{
    $name = "Duplicate Test";
    $email = "duplicate" . rand(1000, 9999) . "@example.com";
    $password = "Password123!";
    $result1 = registerUser($pdo, $name, $email, $password);
    if ($result1 !== AuthResult::SUCCESS) {
        logTestResult("Duplicate Email Test", "FAILURE", "Failed to create initial user for duplicate test");
        return false;
    }
    $result2 = registerUser($pdo, "Another Name", $email, "DifferentPass456!");
    if ($result2 === AuthResult::USER_EXISTS) {
        logTestResult("Duplicate Email Test", "SUCCESS", "Correctly rejected duplicate email registration", [
            "email" => $email,
            "error_code" => $result2
        ]);
        return true;
    } else {
        logTestResult(
            "Duplicate Email Test",
            "FAILURE",
            "System incorrectly allowed duplicate email registration or returned wrong error",
            [
                "email" => $email,
                "expected" => AuthResult::USER_EXISTS,
                "received" => $result2
            ]
        );
        return false;
    }
}

// Test case 3: Empty fields validation
function testEmptyFields($pdo)
{
    $result1 = registerUser($pdo, "", "email" . rand(1000, 9999) . "@example.com", "Password123!");
    $nameCheck = $result1 === AuthResult::FAILURE;
    $result2 = registerUser($pdo, "Test Name", "", "Password123!");
    $emailCheck = $result2 === AuthResult::FAILURE;
    $result3 = registerUser($pdo, "Test Name", "email" . rand(1000, 9999) . "@example.com", "");
    $passwordCheck = $result3 === AuthResult::FAILURE;
    $allChecks = $nameCheck && $emailCheck && $passwordCheck;
    if ($allChecks) {
        logTestResult("Empty Fields Validation", "SUCCESS", "System correctly rejected empty fields", [
            "empty_name_result" => $result1,
            "empty_email_result" => $result2,
            "empty_password_result" => $result3
        ]);
        return true;
    } else {
        logTestResult("Empty Fields Validation", "FAILURE", "System allowed registration with empty fields", [
            "empty_name_result" => $result1,
            "empty_email_result" => $result2,
            "empty_password_result" => $result3
        ]);
        return false;
    }
}

// Test case 4: SQL Injection attempt
function testSQLInjection($pdo)
{
    $name = "Test User";
    $email = "normal" . rand(1000, 9999) . "@example.com";
    $normalPassword = "SecurePass123!";
    $result = registerUser($pdo, $name, $email, $normalPassword);
    if ($result === AuthResult::SUCCESS) {
        $loginResult = loginUser($pdo, $email, "' OR '1'='1");
        if ($loginResult === AuthResult::SUCCESS) {
            logTestResult("SQL Injection Protection", "FAILURE", "System vulnerable to SQL injection attack during login");
            return false;
        }
    } else {
        logTestResult(
            "SQL Injection Protection",
            "INCONCLUSIVE",
            "Could not test SQL injection protection because registration failed",
            [
                "registration_result" => $result
            ]
        );
        return false;
    }
    $maliciousEmail = "' OR '1'='1";
    $loginResult2 = loginUser($pdo, $maliciousEmail, "anything");
    if ($loginResult2 === AuthResult::SUCCESS) {
        logTestResult("SQL Injection Protection", "FAILURE", "System vulnerable to SQL injection attack in email field");
        return false;
    }
    logTestResult("SQL Injection Protection", "SUCCESS", "System protected against SQL injection attempts", [
        "login_with_sql_injection_password" => $loginResult,
        "login_with_sql_injection_email" => $loginResult2
    ]);
    return true;
}

// Test case 5: Password complexity
function testPasswordComplexity($pdo)
{
    $name = "Password Test User";
    $email = "password" . rand(1000, 9999) . "@example.com";
    $shortPassword = "Abc12!";
    $shortResult = registerUser($pdo, $name, $email, $shortPassword);
    $email2 = "password" . rand(1000, 9999) . "@example.com";
    $noSpecialPassword = "Password123";
    $noSpecialResult = registerUser($pdo, $name, $email2, $noSpecialPassword);
    $email3 = "password" . rand(1000, 9999) . "@example.com";
    $noNumbersPassword = "Password!";
    $noNumbersResult = registerUser($pdo, $name, $email3, $noNumbersPassword);
    $results = [
        "short_password" => $shortResult,
        "no_special_chars" => $noSpecialResult,
        "no_numbers" => $noNumbersResult
    ];
    logTestResult("Password Complexity", "INFORMATIONAL", "Password complexity test results", $results);
    return true;
}

// Test case 6: Username generation
function testUsernameGeneration($pdo)
{
    $name = "John Doe";
    $email = "username" . rand(1000, 9999) . "@example.com";
    $password = "Password123!";
    $result = registerUser($pdo, $name, $email, $password);
    if ($result === AuthResult::SUCCESS) {
        $user = getUser($pdo, $email);
        $username = $user['username'];
        $expectedPattern = "/^john_doe_[a-f0-9]{4}$/";
        $matchesPattern = preg_match($expectedPattern, $username);
        if ($matchesPattern) {
            logTestResult(
                "Username Generation",
                "SUCCESS",
                "Username correctly generated from full name",
                [
                    "full_name" => $name,
                    "generated_username" => $username
                ]
            );
            return true;
        } else {
            logTestResult(
                "Username Generation",
                "FAILURE",
                "Username does not match expected pattern",
                [
                    "full_name" => $name,
                    "generated_username" => $username,
                    "expected_pattern" => $expectedPattern
                ]
            );
            return false;
        }
    } else {
        logTestResult(
            "Username Generation",
            "FAILURE",
            "Could not test username generation because registration failed",
            [
                "registration_result" => $result
            ]
        );
        return false;
    }
}

// Test case 7: Special characters in names
function testSpecialCharactersInName($pdo)
{
    $name = "O'Connor-Smith Jr.";
    $email = "special" . rand(1000, 9999) . "@example.com";
    $password = "Password123!";
    $result = registerUser($pdo, $name, $email, $password);
    if ($result === AuthResult::SUCCESS) {
        $user = getUser($pdo, $email);
        if ($user['full_name'] === $name) {
            logTestResult(
                "Special Characters in Name",
                "SUCCESS",
                "System correctly handled special characters in name",
                [
                    "input_name" => $name,
                    "stored_name" => $user['full_name'],
                    "generated_username" => $user['username']
                ]
            );
            return true;
        } else {
            logTestResult(
                "Special Characters in Name",
                "FAILURE",
                "System did not preserve special characters in name",
                [
                    "input_name" => $name,
                    "stored_name" => $user['full_name']
                ]
            );
            return false;
        }
    } else {
        logTestResult(
            "Special Characters in Name",
            "FAILURE",
            "Registration with special characters in name failed",
            [
                "name" => $name,
                "registration_result" => $result
            ]
        );
        return false;
    }
}

// Test case 8: Email format validation
function testEmailFormatValidation($pdo)
{
    $invalidEmails = [
        "notanemail",
        "missing@tld",
        "@nodomain.com",
        "spaces in@email.com",
        "multiple@at@signs.com"
    ];
    $allFailed = true;
    $results = [];
    foreach ($invalidEmails as $email) {
        $result = registerUser($pdo, "Test User", $email, "Password123!");
        $results[$email] = $result;
        if ($result === AuthResult::SUCCESS) {
            $allFailed = false;
        }
    }
    if ($allFailed) {
        logTestResult(
            "Email Format Validation",
            "SUCCESS",
            "System correctly rejected all invalid email formats",
            $results
        );
        return true;
    } else {
        logTestResult(
            "Email Format Validation",
            "FAILURE",
            "System allowed registration with invalid email format(s)",
            $results
        );
        return false;
    }
}

try {
    $testResults = [];
    $testResults["Valid Registration"] = testSuccessfulSignup($cnx);
    $testResults["Duplicate Email"] = testDuplicateEmail($cnx);
    $testResults["Empty Fields"] = testEmptyFields($cnx);
    $testResults["SQL Injection"] = testSQLInjection($cnx);
    $testResults["Password Complexity"] = testPasswordComplexity($cnx);
    $testResults["Username Generation"] = testUsernameGeneration($cnx);
    $testResults["Special Characters"] = testSpecialCharactersInName($cnx);
    $testResults["Email Format"] = testEmailFormatValidation($cnx);
    $passedTests = array_filter($testResults);
    $totalTests = count($testResults);
    $passedCount = count($passedTests);
    $successRate = ($passedCount / $totalTests) * 100;
    logTestResult(
        "SIGNUP TEST SUITE",
        "COMPLETED",
        "Tests completed with $passedCount/$totalTests successful ($successRate% pass rate)",
        $testResults
    );
} catch (Exception $e) {
    logTestResult("SIGNUP TEST SUITE", "ERROR", "An error occurred during testing: " . $e->getMessage());
}
