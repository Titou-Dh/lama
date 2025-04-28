<?php
require_once __DIR__ . '/../common.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controller/event.php';
require_once __DIR__ . '/../../controller/user.php';
require_once __DIR__ . '/../../controller/auth.php';
require_once __DIR__ . "../../auth_check.php";


$logFilePath = __DIR__ . '/../logs/test_results.log';
$results = [];

if (isset($_GET['clear_log']) && $_GET['clear_log'] === 'true') {
    if (file_exists($logFilePath)) {
        file_put_contents($logFilePath, '');
        $results[] = TestUtils::logTestResult("System", "SUCCESS", "Log file cleared successfully");
    }
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}

if (!file_exists(__DIR__ . '/../logs')) {
    mkdir(__DIR__ . '/../logs', 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_tests'])) {
    try {
        $pdo = $cnx;

        // Test 1: Login SQL Injection
        if (isset($_POST['test_login_injection'])) {
            $maliciousInputs = [
                "' OR '1'='1",
                "admin' --",
                "1' OR 1=1; --",
                "1'; DROP TABLE users; --",
                "' UNION SELECT username, password_hash FROM users; --"
            ];

            $vulnerableAttempts = [];
            $secureAttempts = [];

            foreach ($maliciousInputs as $input) {
                try {
                    $result = loginUser($pdo, $input, 'password');

                    if ($result::SUCCESS) {
                        $vulnerableAttempts[] = "Login bypassed with: " . htmlspecialchars($input);
                    } else {
                        $secureAttempts[] = "Login blocked with: " . htmlspecialchars($input);
                    }
                } catch (Exception $e) {
                    $secureAttempts[] = "Login blocked with: " . htmlspecialchars($input);
                }
            }

            if (empty($vulnerableAttempts)) {
                $results[] = TestUtils::logTestResult("Login SQL Injection", "SUCCESS", "Login system protected against SQL injection", [
                    "secure_attempts" => count($secureAttempts)
                ]);
            } else {
                $results[] = TestUtils::logTestResult("Login SQL Injection", "FAILURE", "Login system vulnerable to SQL injection", [
                    "vulnerable_attempts" => $vulnerableAttempts
                ]);
            }
        }

        // Test 2: Event Search SQL Injection
        if (isset($_POST['test_search_injection'])) {
            $maliciousInputs = [
                "' OR '1'='1",
                "'; DROP TABLE events; --",
                "' UNION SELECT * FROM users; --",
            ];

            $vulnerableAttempts = [];
            $secureAttempts = [];

            foreach ($maliciousInputs as $input) {
                try {
                    // Test using the event controller's search function
                    $result = searchEvents($pdo, $input);

                    if (is_array($result) && count($result) > 0) {
                        $vulnerableAttempts[] = "Search vulnerable with: " . htmlspecialchars($input);
                    } else {
                        $secureAttempts[] = "Search secure with: " . htmlspecialchars($input);
                    }
                } catch (Exception $e) {
                    $secureAttempts[] = "Search blocked with: " . htmlspecialchars($input);
                }
            }

            if (empty($vulnerableAttempts)) {
                $results[] = TestUtils::logTestResult("Search SQL Injection", "SUCCESS", "Search system protected against SQL injection", [
                    "secure_attempts" => count($secureAttempts)
                ]);
            } else {
                $results[] = TestUtils::logTestResult("Search SQL Injection", "FAILURE", "Search system vulnerable to SQL injection", [
                    "vulnerable_attempts" => $vulnerableAttempts
                ]);
            }
        }

        // Test 3: User Profile SQL Injection
        if (isset($_POST['test_profile_injection'])) {
            $maliciousInputs = [
                "1' OR '1'='1",
                "1; DROP TABLE users; --",
                "1' UNION SELECT * FROM users; --"
            ];

            $vulnerableAttempts = [];
            $secureAttempts = [];

            foreach ($maliciousInputs as $input) {
                try {
                    // Test using the user controller's getProfile function
                    $result = getUserById($pdo, $input);

                    if ($result) {
                        $vulnerableAttempts[] = "Profile access vulnerable with: " . htmlspecialchars($input);
                    } else {
                        $secureAttempts[] = "Profile access secure with: " . htmlspecialchars($input);
                    }
                } catch (Exception $e) {
                    $secureAttempts[] = "Profile access blocked with: " . htmlspecialchars($input);
                }
            }

            if (empty($vulnerableAttempts)) {
                $results[] = TestUtils::logTestResult("Profile SQL Injection", "SUCCESS", "Profile system protected against SQL injection", [
                    "secure_attempts" => count($secureAttempts)
                ]);
            } else {
                $results[] = TestUtils::logTestResult("Profile SQL Injection", "FAILURE", "Profile system vulnerable to SQL injection", [
                    "vulnerable_attempts" => $vulnerableAttempts
                ]);
            }
        }

        // Clean up test data
        $cleanupResults = TestUtils::cleanupAll($pdo);
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
    <title>SQL Injection Tests - LAMA Test Suite</title>
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
                                SQL Injection Tests
                            </h2>
                            <div class="text-muted mt-1">Testing protection against SQL injection vulnerabilities</div>
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
                        <!-- Warning Message -->
                        <div class="col-12">
                            <div class="alert alert-warning mb-4">
                                <div class="d-flex">
                                    <div>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M12 9v2m0 4v.01"></path>
                                            <path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4>Warning: Security Testing</h4>
                                        <p>These tests attempt to simulate SQL injection attacks on your database. Please ensure you are running these tests in a safe, non-production environment.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

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
                                                    <input type="checkbox" name="test_login_injection" class="form-selectgroup-input">
                                                    <span class="form-selectgroup-label">Login SQL Injection</span>
                                                </label>
                                                <label class="form-selectgroup-item">
                                                    <input type="checkbox" name="test_search_injection" class="form-selectgroup-input">
                                                    <span class="form-selectgroup-label">Search SQL Injection</span>
                                                </label>
                                                <label class="form-selectgroup-item">
                                                    <input type="checkbox" name="test_profile_injection" class="form-selectgroup-input">
                                                    <span class="form-selectgroup-label">Profile SQL Injection</span>
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
                                    <h3 class="card-title">About SQL Injection Testing</h3>
                                </div>
                                <div class="card-body">
                                    <p>These tests verify that the application's controller functions are protected against SQL injection attacks by:</p>
                                    <ul>
                                        <li>Testing login authentication with malicious inputs</li>
                                        <li>Testing event search functionality with SQL injection attempts</li>
                                        <li>Testing user profile access with malicious IDs</li>
                                    </ul>
                                    <p class="text-muted">The tests use the actual controller functions to ensure they are properly protected against SQL injection.</p>
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