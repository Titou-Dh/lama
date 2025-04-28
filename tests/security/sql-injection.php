<?php
require_once "../../config/database.php";

$logFilePath = "../logs/security_tests.log";
$results = [];

// Create logs directory if it doesn't exist
if (!file_exists("../logs")) {
    mkdir("../logs", 0755, true);
}

/**
 * Log test results to file
 */
function logTestResult($testName, $status, $message, $response = "")
{
    global $logFilePath;
    $date = date("Y-m-d H:i:s");
    $logEntry = "[$date] TEST: $testName | STATUS: $status | MESSAGE: $message" . PHP_EOL;
    if (!empty($response)) {
        $logEntry .= "RESPONSE: " . json_encode($response) . PHP_EOL;
    }
    $logEntry .= "-------------------------------------------------------------" . PHP_EOL;
    file_put_contents($logFilePath, $logEntry, FILE_APPEND);

    return [
        'name' => $testName,
        'status' => $status,
        'message' => $message,
        'response' => $response,
        'timestamp' => $date
    ];
}

// Run tests when form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_tests'])) {
    try {
        $pdo = $cnx;

        // Test 1: Simple SQL Injection Test (Login Form)
        if (isset($_POST['test_basic_injection'])) {
            $maliciousInputs = [
                "' OR '1'='1",
                "admin' --",
                "1' OR 1=1; --",
                "1'; DROP TABLE users; --",
                "' UNION SELECT username, password_hash FROM users; --"
            ];

            $secureResults = true;
            $vulnerableEndpoints = [];

            foreach ($maliciousInputs as $input) {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND password_hash = ?");
                $stmt->execute([$input, 'password_hash']);
                $result1 = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $vulnerableQuery = "SELECT * FROM users WHERE email = '$input' AND password_hash = 'password_hash'";

                try {
                    
                    $rawResult = $pdo->query($vulnerableQuery);
                    $result2 = $rawResult ? $rawResult->fetchAll(PDO::FETCH_ASSOC) : [];

                   
                    if (count($result2) > 0) {
                        $secureResults = false;
                        $vulnerableEndpoints[] = "Direct query vulnerable to: $input";
                    }
                } catch (Exception $e) {
                    // Expected exception for dangerous inputs - this is good!
                }

                // Check if the prepared statement was bypassed
                if (count($result1) > 0) {
                    $secureResults = false;
                    $vulnerableEndpoints[] = "Prepared statement bypassed with: $input";
                }
            }

            if ($secureResults) {
                $results[] = logTestResult("Basic SQL Injection", "SUCCESS", "Application successfully blocked SQL injection attempts");
            } else {
                $results[] = logTestResult("Basic SQL Injection", "FAILURE", "Application may be vulnerable to SQL injection", [
                    "vulnerable_endpoints" => $vulnerableEndpoints
                ]);
            }
        }

        // Test 2: Advanced SQL Injection (UNION, boolean-based, time-based)
        if (isset($_POST['test_advanced_injection'])) {
            $advancedInjections = [
                // UNION-based injections
                "' UNION SELECT 1,2,3,4,5 --",
                "' UNION SELECT table_name,2,3,4,5 FROM information_schema.tables --",

                // Boolean-based injections
                "' OR 1=1 --",
                "' OR '1'='1",

                // Time-based injections
                "' AND (SELECT * FROM (SELECT(SLEEP(0.1)))A) --",
                "'; WAITFOR DELAY '0:0:1' --"
            ];

            $secureResults = true;
            $vulnerableEndpoints = [];

            // Test event search (example endpoint)
            foreach ($advancedInjections as $input) {
                // Secure parameterized query
                $stmt = $pdo->prepare("SELECT * FROM events WHERE title LIKE ? OR description LIKE ? LIMIT 5");
                $param = "%$input%";
                $stmt->execute([$param, $param]);
                $secureResult = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Insecure direct query (for testing purposes only)
                $unsafeQuery = "SELECT * FROM events WHERE title LIKE '%$input%' OR description LIKE '%$input%' LIMIT 5";

                try {
                    $start = microtime(true);
                    $rawResult = $pdo->query($unsafeQuery);
                    $end = microtime(true);
                    $executionTime = $end - $start;

                    // Check for time-based injection success (delayed response)
                    if ($executionTime > 0.5) { // If query took longer than expected
                        $secureResults = false;
                        $vulnerableEndpoints[] = "Possible time-based injection with: $input (execution time: " . round($executionTime, 2) . "s)";
                    }

                    // For UNION or boolean-based, check if we got unexpected results
                    $unsafeResults = $rawResult ? $rawResult->fetchAll(PDO::FETCH_ASSOC) : [];
                    if (count($unsafeResults) > count($secureResult) + 2) {
                        $secureResults = false;
                        $vulnerableEndpoints[] = "Possible UNION/Boolean injection with: $input";
                    }
                } catch (Exception $e) {
                    // Expected for dangerous inputs
                }
            }

            if ($secureResults) {
                $results[] = logTestResult("Advanced SQL Injection", "SUCCESS", "Application successfully blocked advanced SQL injection attempts");
            } else {
                $results[] = logTestResult("Advanced SQL Injection", "FAILURE", "Application may be vulnerable to advanced SQL injection techniques", [
                    "vulnerable_endpoints" => $vulnerableEndpoints
                ]);
            }
        }

        // Test 3: SQL Injection in ORDER BY clause
        if (isset($_POST['test_orderby_injection'])) {
            $orderByInjections = [
                "id DESC",
                "id; DROP TABLE users",
                "id) UNION SELECT password_hash FROM users --",
                "(CASE WHEN (1=1) THEN title ELSE id END)",
                "id DESC; SELECT * FROM users"
            ];

            $secureResults = true;
            $vulnerableEndpoints = [];

            foreach ($orderByInjections as $input) {
                // Whitelist approach (secure)
                $allowedColumns = ['id', 'title', 'start_date', 'location', 'capacity'];
                $direction = (stripos($input, 'desc') !== false) ? 'DESC' : 'ASC';

                // Simple validation
                $orderColumn = 'id'; // Default
                foreach ($allowedColumns as $col) {
                    if (stripos($input, $col) === 0) {
                        $orderColumn = $col;
                        break;
                    }
                }

                // Secure query with validated input
                $secureQuery = "SELECT * FROM events ORDER BY $orderColumn $direction LIMIT 5";
                $secureResult = $pdo->query($secureQuery)->fetchAll(PDO::FETCH_ASSOC);

                // Test unsafe direct input in ORDER BY
                $unsafeQuery = "SELECT * FROM events ORDER BY $input LIMIT 5";

                try {
                    $rawResult = $pdo->query($unsafeQuery);
                    // If query succeeds with dangerous input, it could be vulnerable
                    if ($rawResult && stripos($input, ';') !== false) {
                        $secureResults = false;
                        $vulnerableEndpoints[] = "ORDER BY clause may be vulnerable to: $input";
                    }
                } catch (Exception $e) {
                    // Expected for dangerous inputs
                }
            }

            if ($secureResults) {
                $results[] = logTestResult("ORDER BY Injection", "SUCCESS", "Application successfully handled ORDER BY injection attempts");
            } else {
                $results[] = logTestResult("ORDER BY Injection", "FAILURE", "Application may be vulnerable to ORDER BY SQL injection", [
                    "vulnerable_endpoints" => $vulnerableEndpoints
                ]);
            }
        }

        // Test 4: Database Error Disclosure
        if (isset($_POST['test_error_disclosure'])) {
            $errorDisclosureTests = [
                "' OR 1=''1", // Syntax error
                "' AND 1/(SELECT 0) > 0 --", // Division by zero
                "' AND (SELECT 1 FROM nonexistenttable) > 0 --", // Reference to nonexistent table
                "' GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30 --" // Too many columns
            ];

            $secureErrorHandling = true;
            $disclosedErrors = [];

            foreach ($errorDisclosureTests as $input) {
                // Deliberately trigger errors to see how they're handled
                $query = "SELECT * FROM events WHERE title = '$input'";

                ob_start(); // Capture any output

                try {
                    $pdo->query($query);
                } catch (Exception $e) {
                    $error = $e->getMessage();

                    // Check for sensitive information disclosure in error messages
                    $sensitivePatterns = [
                        '/SQL syntax/i',
                        '/mysql error/i',
                        '/division by zero/i',
                        '/ORA-[0-9]+/i', // Oracle errors
                        '/SQL Server/i',
                        '/syntax error at/i',
                        '/PostgreSQL/i',
                        '/table.*not exist/i'
                    ];

                    foreach ($sensitivePatterns as $pattern) {
                        if (preg_match($pattern, $error)) {
                            $secureErrorHandling = false;
                            $disclosedErrors[] = "Error message disclosed sensitive information: " . substr($error, 0, 100) . "...";
                            break;
                        }
                    }
                }

                $output = ob_get_clean(); // Get any output and clear buffer

                // Check if any error was directly outputted (very bad practice)
                if (
                    stripos($output, 'SQL') !== false ||
                    stripos($output, 'syntax') !== false ||
                    stripos($output, 'mysql') !== false
                ) {
                    $secureErrorHandling = false;
                    $disclosedErrors[] = "Error directly outputted to page: " . substr($output, 0, 100) . "...";
                }
            }

            if ($secureErrorHandling) {
                $results[] = logTestResult("Error Disclosure", "SUCCESS", "Application properly handles database errors without disclosing sensitive information");
            } else {
                $results[] = logTestResult("Error Disclosure", "FAILURE", "Application discloses sensitive database information in error messages", [
                    "disclosed_errors" => $disclosedErrors
                ]);
            }
        }
    } catch (Exception $e) {
        $results[] = logTestResult("SQL Injection Tests", "ERROR", "Testing framework error: " . $e->getMessage());
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
                                        <p>These tests attempt to simulate SQL injection attacks on your database. Please ensure you are running these tests in a safe, non-production environment. Some tests may intentionally attempt to modify or access your database in unauthorized ways to evaluate security.</p>
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
                                            <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                                                <label class="form-selectgroup-item">
                                                    <input type="checkbox" name="test_basic_injection" class="form-selectgroup-input">
                                                    <span class="form-selectgroup-label d-flex flex-row align-items-center p-3">
                                                        <span class="me-3">
                                                            <span class="form-selectgroup-check"></span>
                                                        </span>
                                                        <span class="form-selectgroup-label-content">
                                                            <span class="form-selectgroup-title strong mb-1">Basic SQL Injection</span>
                                                            <span class="d-block text-muted">Tests simple SQL injection techniques on login and search forms</span>
                                                        </span>
                                                    </span>
                                                </label>
                                                <label class="form-selectgroup-item">
                                                    <input type="checkbox" name="test_advanced_injection" class="form-selectgroup-input">
                                                    <span class="form-selectgroup-label d-flex flex-row align-items-center p-3">
                                                        <span class="me-3">
                                                            <span class="form-selectgroup-check"></span>
                                                        </span>
                                                        <span class="form-selectgroup-label-content">
                                                            <span class="form-selectgroup-title strong mb-1">Advanced SQL Injection</span>
                                                            <span class="d-block text-muted">Tests UNION-based, boolean-based, and time-based injection techniques</span>
                                                        </span>
                                                    </span>
                                                </label>
                                                <label class="form-selectgroup-item">
                                                    <input type="checkbox" name="test_orderby_injection" class="form-selectgroup-input">
                                                    <span class="form-selectgroup-label d-flex flex-row align-items-center p-3">
                                                        <span class="me-3">
                                                            <span class="form-selectgroup-check"></span>
                                                        </span>
                                                        <span class="form-selectgroup-label-content">
                                                            <span class="form-selectgroup-title strong mb-1">ORDER BY Injection</span>
                                                            <span class="d-block text-muted">Tests SQL injection attacks in ORDER BY clauses</span>
                                                        </span>
                                                    </span>
                                                </label>
                                                <label class="form-selectgroup-item">
                                                    <input type="checkbox" name="test_error_disclosure" class="form-selectgroup-input">
                                                    <span class="form-selectgroup-label d-flex flex-row align-items-center p-3">
                                                        <span class="me-3">
                                                            <span class="form-selectgroup-check"></span>
                                                        </span>
                                                        <span class="form-selectgroup-label-content">
                                                            <span class="form-selectgroup-title strong mb-1">Error Disclosure</span>
                                                            <span class="d-block text-muted">Tests for sensitive database error information disclosure</span>
                                                        </span>
                                                    </span>
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
                                    <h4>What is SQL Injection?</h4>
                                    <p>SQL Injection is a code injection technique that exploits vulnerabilities in applications that build SQL queries using user-supplied input without proper validation or parameterization.</p>

                                    <h4 class="mt-4">Security Best Practices</h4>
                                    <ul>
                                        <li>Always use prepared statements with parameterized queries</li>
                                        <li>Apply input validation and sanitization</li>
                                        <li>Use stored procedures when possible</li>
                                        <li>Implement the principle of least privilege for database accounts</li>
                                        <li>Hide database error details from users</li>
                                    </ul>

                                    <h4 class="mt-4">What These Tests Check</h4>
                                    <p>These tests attempt common SQL injection techniques against your application to verify if proper protections are in place. The tests include:</p>
                                    <ul>
                                        <li>Classic login bypass attacks</li>
                                        <li>UNION-based attacks that can extract data from other tables</li>
                                        <li>Time-based blind injection techniques</li>
                                        <li>ORDER BY clause injection vulnerabilities</li>
                                        <li>Error-based information disclosure</li>
                                    </ul>
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

                                        <?php
                                        // Check for failures and show detailed info if available
                                        foreach ($results as $result):
                                            if ($result['status'] === 'FAILURE' && !empty($result['response'])):
                                        ?>
                                                <div class="alert alert-danger mt-3">
                                                    <h4>Failure Details: <?= htmlspecialchars($result['name']) ?></h4>
                                                    <pre class="mt-2"><?= htmlspecialchars(json_encode($result['response'], JSON_PRETTY_PRINT)) ?></pre>
                                                </div>
                                        <?php
                                            endif;
                                        endforeach;
                                        ?>
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

                        <!-- Additional Resources -->
                        <div class="col-12 mt-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Additional Resources</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row row-cards">
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h4>OWASP SQL Injection Prevention Cheat Sheet</h4>
                                                    <p>Comprehensive guide on preventing SQL injection vulnerabilities.</p>
                                                    <a href="https://cheatsheetseries.owasp.org/cheatsheets/SQL_Injection_Prevention_Cheat_Sheet.html" target="_blank" class="btn btn-outline-primary mt-2">
                                                        Visit Resource
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h4>OWASP ZAP Testing Tool</h4>
                                                    <p>Free security tool for finding vulnerabilities in web applications.</p>
                                                    <a href="https://www.zaproxy.org/" target="_blank" class="btn btn-outline-primary mt-2">
                                                        Visit Resource
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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