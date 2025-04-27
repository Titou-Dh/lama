<?php
require_once __DIR__ . '/../common.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controller/auth.php';
require_once __DIR__ . "../../auth_check.php";

$logFilePath = __DIR__ . '/../logs/test_results.log';
$results = [];

// Handle log clearing
if (isset($_GET['clear_log']) && $_GET['clear_log'] === 'true') {
    if (file_exists($logFilePath)) {
        file_put_contents($logFilePath, '');
        $results[] = TestUtils::logTestResult("System", "SUCCESS", "Log file cleared successfully");
    }
    // Redirect to remove clear_log parameter
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

if (!file_exists(__DIR__ . '/../logs')) {
    mkdir(__DIR__ . '/../logs', 0755, true);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_tests'])) {
    try {
        $pdo = $cnx;

        // Test 1: User Registration
        if (isset($_POST['test_registration'])) {
            $name = "Test User " . rand(1000, 9999);
            $email = "testuser" . rand(1000, 9999) . "@example.com";
            $password = "Password123!";

            $result = registerUser($pdo, $name, $email, $password);

            if ($result === AuthResult::SUCCESS) {
                $user = getUser($pdo, $email);
                $message = "User successfully registered with email: $email";
                $results[] = TestUtils::logTestResult("User Registration", "SUCCESS", $message, [
                    "email" => $email,
                    "username" => $user['username'] ?? 'N/A'
                ]);
            } else {
                $results[] = TestUtils::logTestResult("User Registration", "FAILURE", "Failed to register user", [
                    "error_code" => $result
                ]);
            }
        }

        // Test 2: User Login
        if (isset($_POST['test_login'])) {
            $name = "Login Test User";
            $email = "login_test" . rand(1000, 9999) . "@example.com";
            $password = "Password123!";

            $registerResult = registerUser($pdo, $name, $email, $password);

            if ($registerResult === AuthResult::SUCCESS) {
                $loginResult = loginUser($pdo, $email, $password);

                if ($loginResult === AuthResult::SUCCESS) {
                    $results[] = TestUtils::logTestResult("User Login", "SUCCESS", "Successfully logged in with valid credentials", [
                        "email" => $email
                    ]);
                } else {
                    $results[] = TestUtils::logTestResult("User Login", "FAILURE", "Failed to login with valid credentials", [
                        "email" => $email,
                        "result_code" => $loginResult
                    ]);
                }
            } else {
                $results[] = TestUtils::logTestResult("User Login", "ERROR", "Could not create test user for login test", [
                    "registration_result" => $registerResult
                ]);
            }
        }

        // Test 3: Invalid Login Attempt
        if (isset($_POST['test_invalid_login'])) {
            $email = "nonexistent" . rand(1000, 9999) . "@example.com";
            $password = "WrongPassword123!";

            $loginResult = loginUser($pdo, $email, $password);

            if ($loginResult === AuthResult::INVALID_CREDENTIALS) {
                $results[] = TestUtils::logTestResult("Invalid Login", "SUCCESS", "Correctly rejected login with invalid credentials", [
                    "email" => $email
                ]);
            } else {
                $results[] = TestUtils::logTestResult("Invalid Login", "FAILURE", "System allowed login with invalid credentials", [
                    "email" => $email,
                    "result_code" => $loginResult
                ]);
            }
        }

        // Test 4: Session Management
        if (isset($_POST['test_session'])) {
            $name = "Session Test User";
            $email = "session_test" . rand(1000, 9999) . "@example.com";
            $password = "Password123!";

            $registerResult = registerUser($pdo, $name, $email, $password);

            if ($registerResult === AuthResult::SUCCESS) {
                $loginResult = loginUser($pdo, $email, $password);

                if ($loginResult === AuthResult::SUCCESS && isset($_SESSION['user_id'])) {
                    $hasUserId = isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
                    $hasEmail = isset($_SESSION['email']) && $_SESSION['email'] === $email;

                    if ($hasUserId && $hasEmail) {
                        $results[] = TestUtils::logTestResult("Session Management", "SUCCESS", "Session correctly initialized after login", [
                            "session_data" => array_map(function ($key) {
                                return is_scalar($_SESSION[$key]) ? $_SESSION[$key] : gettype($_SESSION[$key]);
                            }, array_keys($_SESSION))
                        ]);
                    } else {
                        $results[] = TestUtils::logTestResult("Session Management", "FAILURE", "Session missing required data", [
                            "has_user_id" => $hasUserId,
                            "has_email" => $hasEmail
                        ]);
                    }
                } else {
                    $results[] = TestUtils::logTestResult("Session Management", "FAILURE", "Login didn't create a valid session", [
                        "login_result" => $loginResult,
                        "has_session" => isset($_SESSION['user_id'])
                    ]);
                }
            } else {
                $results[] = TestUtils::logTestResult("Session Management", "ERROR", "Could not create test user for session test");
            }
        }

        // Test 5: Logout
        if (isset($_POST['test_logout'])) {
            $name = "Logout Test User";
            $email = "logout_test" . rand(1000, 9999) . "@example.com";
            $password = "Password123!";

            $registerResult = registerUser($pdo, $name, $email, $password);

            if ($registerResult === AuthResult::SUCCESS) {
                $loginResult = loginUser($pdo, $email, $password);

                if ($loginResult === AuthResult::SUCCESS && isset($_SESSION['user_id'])) {
                    session_destroy();
                    $sessionCleared = !isset($_SESSION['user_id']);

                    if ($sessionCleared) {
                        $results[] = TestUtils::logTestResult("Logout", "SUCCESS", "Session successfully destroyed on logout");
                    } else {
                        $results[] = TestUtils::logTestResult("Logout", "FAILURE", "Session was not properly destroyed", [
                            "session_data" => isset($_SESSION) ? array_keys($_SESSION) : null
                        ]);
                    }
                } else {
                    $results[] = TestUtils::logTestResult("Logout", "ERROR", "Could not login for logout test");
                }
            } else {
                $results[] = TestUtils::logTestResult("Logout", "ERROR", "Could not create test user for logout test");
            }
        }

        $cleanupResults = TestUtils::cleanupTestUsers($pdo);
        if (is_array($cleanupResults)) {
            $results = array_merge($results, $cleanupResults);
        }
    } catch (Exception $e) {
        $results[] = TestUtils::logTestResult("Test Suite", "ERROR", "Database connection error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication Tests - LAMA Test Suite</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.2.0/dist/css/tabler.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/@tabler/core@1.2.0/dist/js/tabler.min.js"></script>
</head>

<body>
    <div class="page">
        <div class="page-wrapper">
            <div class="container-xl">
                <div class="page-header d-print-none">
                    <div class="row align-items-center">
                        <div class="col">
                            <h2 class="page-title">
                                Authentication Tests
                            </h2>
                            <div class="text-muted mt-1">Testing user registration, login, session management and security</div>
                            <div class="mt-2">
                                <a href="../index.php" class="btn btn-outline-primary btn-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M5 12l14 0"></path>
                                        <path d="M5 12l6 6"></path>
                                        <path d="M5 12l6 -6"></path>
                                    </svg>
                                    Back to Test Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="page-body">
                <div class="container-xl">
                    <div class="row row-cards">
                        <!-- Test Selection Form -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Available Tests</h3>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="">
                                        <div class="form-group mb-3">
                                            <div class="form-label">Select tests to run</div>
                                            <div class="form-selectgroup">
                                                <label class="form-selectgroup-item">
                                                    <input type="checkbox" name="test_registration" class="form-selectgroup-input">
                                                    <span class="form-selectgroup-label">User Registration</span>
                                                </label>
                                                <label class="form-selectgroup-item">
                                                    <input type="checkbox" name="test_login" class="form-selectgroup-input">
                                                    <span class="form-selectgroup-label">User Login</span>
                                                </label>
                                                <label class="form-selectgroup-item">
                                                    <input type="checkbox" name="test_invalid_login" class="form-selectgroup-input">
                                                    <span class="form-selectgroup-label">Invalid Login</span>
                                                </label>
                                                <label class="form-selectgroup-item">
                                                    <input type="checkbox" name="test_session" class="form-selectgroup-input">
                                                    <span class="form-selectgroup-label">Session Management</span>
                                                </label>
                                                <label class="form-selectgroup-item">
                                                    <input type="checkbox" name="test_logout" class="form-selectgroup-input">
                                                    <span class="form-selectgroup-label">Logout</span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-footer">
                                            <button type="submit" name="run_tests" class="btn btn-primary">Run Selected Tests</button>
                                            <button type="submit" name="run_tests" class="btn btn-outline-primary" onclick="document.querySelectorAll('.form-selectgroup-input').forEach(cb => cb.checked = true);">Run All Tests</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Test Description -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">About These Tests</h3>
                                </div>
                                <div class="card-body">
                                    <p>The authentication tests verify the system's ability to:</p>
                                    <ul>
                                        <li>Register new users securely</li>
                                        <li>Validate login credentials correctly</li>
                                        <li>Reject invalid login attempts</li>
                                        <li>Manage user sessions properly</li>
                                        <li>Handle logout operations</li>
                                    </ul>
                                    <p class="text-muted">These tests use randomized usernames and emails to ensure they don't conflict with existing user data.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Test Results -->
                        <?php if (!empty($results)): ?>
                            <div class="col-12 mt-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Test Results</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-vcenter">
                                                <thead>
                                                    <tr>
                                                        <th>Test Name</th>
                                                        <th>Status</th>
                                                        <th>Message</th>
                                                        <th>Timestamp</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($results as $result): ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($result['name']) ?></td>
                                                            <td>
                                                                <?php if ($result['status'] === 'SUCCESS'): ?>
                                                                    <span class="badge bg-success"><?= htmlspecialchars($result['status']) ?></span>
                                                                <?php elseif ($result['status'] === 'FAILURE'): ?>
                                                                    <span class="badge bg-danger"><?= htmlspecialchars($result['status']) ?></span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-warning"><?= htmlspecialchars($result['status']) ?></span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td><?= htmlspecialchars($result['message']) ?></td>
                                                            <td><?= htmlspecialchars($result['timestamp']) ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Test Log File -->
                        <div class="col-12 mt-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Test Log File</h3>
                                </div>
                                <div class="card-body">
                                    <p>All test results are logged to: <code><?= htmlspecialchars($logFilePath) ?></code></p>
                                    <?php if (file_exists($logFilePath)): ?>
                                        <div class="form-group">
                                            <label class="form-label">Recent log entries</label>
                                            <textarea class="form-control font-monospace" rows="10" readonly><?= htmlspecialchars(file_exists($logFilePath) ? file_get_contents($logFilePath) : '') ?></textarea>
                                        </div>
                                        <div class="mt-3">
                                            <a href="?clear_log=true" class="btn btn-outline-danger">Clear Log File</a>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">
                                            No log file exists yet. Run some tests to generate logs.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>