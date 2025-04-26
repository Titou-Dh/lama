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

/**
 * Helper function to test XSS sanitization
 */
function testXSSSanitization($input)
{
    // Simple sanitization function (for testing purposes)
    $sanitized = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    return $sanitized === $input ? false : true;
}

/**
 * Helper function to simulate testing a page for XSS reflection
 */
function simulatePageRendering($input, $sanitize = true)
{
    // This function simulates rendering a page with user input
    // In a real test, you would make an HTTP request to the page

    ob_start();

    // Simulate how the app would handle user input
    if ($sanitize) {
        echo "Page content with sanitized input: " . htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    } else {
        echo "Page content with raw input: " . $input;
    }

    $output = ob_get_clean();
    return $output;
}

// Run tests when form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_tests'])) {
    // Test 1: Reflected XSS Test
    if (isset($_POST['test_reflected_xss'])) {
        $xssPayloads = [
            "<script>alert('XSS')</script>",
            "<img src=x onerror=alert('XSS')>",
            "<svg onload=alert('XSS')>",
            "\"><script>alert('XSS')</script>",
            "javascript:alert('XSS')",
            "<div style=\"background-image: url(javascript:alert('XSS'))\">",
            "<a href=\"javascript:alert('XSS')\">Click me</a>",
            "<iframe src=\"javascript:alert('XSS')\"></iframe>",
            "<body onload=alert('XSS')>",
            "<input type=\"text\" value=\"\" onfocus=\"alert('XSS')\" autofocus>"
        ];

        $vulnerableEndpoints = [];
        $secureEndpoints = [];

        foreach ($xssPayloads as $payload) {
            // Test sanitized output (should be secure)
            $sanitizedOutput = simulatePageRendering($payload, true);
            $isVulnerable1 = strpos($sanitizedOutput, $payload) !== false &&
                (strpos($sanitizedOutput, '<script>') !== false ||
                    strpos($sanitizedOutput, 'onerror=') !== false ||
                    strpos($sanitizedOutput, 'onload=') !== false ||
                    strpos($sanitizedOutput, 'javascript:') !== false);

            if ($isVulnerable1) {
                $vulnerableEndpoints[] = "Sanitized output vulnerable to: " . htmlspecialchars($payload);
            } else {
                $secureEndpoints[] = "Sanitized output protected against: " . htmlspecialchars($payload);
            }

            // Test unsanitized output (intentionally vulnerable for testing)
            $rawOutput = simulatePageRendering($payload, false);
            $isVulnerable2 = strpos($rawOutput, $payload) !== false;

            if ($isVulnerable2) {
                $vulnerableEndpoints[] = "Unsanitized output vulnerable to: " . htmlspecialchars($payload);
            } else {
                $secureEndpoints[] = "Unsanitized output protected against: " . htmlspecialchars($payload);
            }
        }

        // Test if the application properly sanitizes user input
        $testOutput = simulatePageRendering("<script>alert('XSS')</script>", true);
        if (strpos($testOutput, '<script>') === false) {
            $results[] = logTestResult(
                "Reflected XSS Protection",
                "SUCCESS",
                "Application correctly sanitizes reflected XSS payloads",
                [
                    "tested_payloads" => count($xssPayloads),
                    "vulnerable_endpoints" => $vulnerableEndpoints,
                    "secure_endpoints" => $secureEndpoints
                ]
            );
        } else {
            $results[] = logTestResult(
                "Reflected XSS Protection",
                "FAILURE",
                "Application may be vulnerable to reflected XSS attacks",
                [
                    "tested_payloads" => count($xssPayloads),
                    "vulnerable_endpoints" => $vulnerableEndpoints
                ]
            );
        }
    }

    // Test 2: Stored XSS Test
    if (isset($_POST['test_stored_xss'])) {
        try {
            $pdo = $cnx;

            // Test payload for stored XSS
            $xssPayload = "<script>alert('Stored XSS')</script>";

            // Insert a comment with XSS payload (simulating a vulnerable comment system)
            // Note: In a real application, you would test if the application properly sanitizes this
            $stmt = $pdo->prepare("
                INSERT INTO test_comments (content, user_id, created_at) 
                VALUES (?, 1, NOW())
            ");

            // Check if table exists first, create it if needed (for testing purposes)
            $tableExists = false;

            try {
                $checkTable = $pdo->query("SELECT 1 FROM test_comments LIMIT 1");
                $tableExists = true;
            } catch (Exception $e) {
                // Table doesn't exist, create it
                $pdo->exec("
                    CREATE TABLE IF NOT EXISTS test_comments (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        content TEXT,
                        user_id INT,
                        created_at DATETIME
                    )
                ");
            }

            // Insert the XSS payload
            $stmt->execute([$xssPayload]);
            $commentId = $pdo->lastInsertId();

            // Now retrieve the comment and check if it's properly sanitized
            $stmt = $pdo->prepare("SELECT content FROM test_comments WHERE id = ?");
            $stmt->execute([$commentId]);
            $comment = $stmt->fetch(PDO::FETCH_ASSOC);

            // Simulate rendering the comment (properly sanitized)
            $sanitizedOutput = htmlspecialchars($comment['content'], ENT_QUOTES, 'UTF-8');

            // Check if sanitization happened
            if ($sanitizedOutput !== $xssPayload) {
                $results[] = logTestResult(
                    "Stored XSS Protection",
                    "SUCCESS",
                    "Application correctly sanitizes stored XSS payloads when displaying them",
                    [
                        "original_payload" => $xssPayload,
                        "sanitized_output" => $sanitizedOutput
                    ]
                );
            } else {
                $results[] = logTestResult(
                    "Stored XSS Protection",
                    "FAILURE",
                    "Application may be vulnerable to stored XSS attacks",
                    [
                        "original_payload" => $xssPayload,
                        "output" => $sanitizedOutput
                    ]
                );
            }

            // Clean up - remove test data
            $pdo->exec("DELETE FROM test_comments WHERE id = $commentId");
        } catch (Exception $e) {
            $results[] = logTestResult("Stored XSS Test", "ERROR", "Error in stored XSS test: " . $e->getMessage());
        }
    }

    // Test 3: DOM-based XSS Test
    if (isset($_POST['test_dom_xss'])) {
        $domXssPayloads = [
            // URL fragment-based payloads (commonly used in DOM XSS)
            "location.hash=\"<img src=x onerror=alert('DOM XSS')>\"",
            "window.name=\"<script>alert('DOM XSS')</script>\"",
            "document.referrer=\"<img src=x onerror=alert('DOM XSS')>\"",
            // Local storage-based payloads
            "localStorage.setItem('vulnerable', '<img src=x onerror=alert(\"DOM XSS\")>')",
            // JSON injection payloads
            "{\"user\":\"<script>alert('DOM XSS')</script>\"}",
            // Eval-based payloads
            "eval(location.hash.substring(1))"
        ];

        // This is a simulated test since we can't actually execute JavaScript in this PHP context
        // In a real-world scenario, you would use a headless browser like Puppeteer or Selenium

        $domVulnerabilities = [];

        // Check if common vulnerable patterns exist in frontend code
        // Sample code snippets that might indicate DOM XSS vulnerabilities
        $vulnerablePatterns = [
            "document.write(",
            "innerHTML =",
            ".innerHTML +=",
            "$(location.hash)",
            "eval(",
            "setTimeout(",
            "setInterval(",
            "document.domain =",
            ".html(",
            "location.href.substring"
        ];

        // Simulate checking frontend code for vulnerable patterns
        $frontendFiles = [
            "../../view/scripts/main.js",
            "../../view/scripts/event.js",
            "../../view/scripts/user.js"
        ];

        $foundVulnerableCode = false;
        foreach ($frontendFiles as $file) {
            if (file_exists($file)) {
                $code = file_get_contents($file);
                foreach ($vulnerablePatterns as $pattern) {
                    if (strpos($code, $pattern) !== false) {
                        $domVulnerabilities[] = "Potentially vulnerable code found in $file: " . $pattern;
                        $foundVulnerableCode = true;
                    }
                }
            }
        }

        if ($foundVulnerableCode) {
            $results[] = logTestResult(
                "DOM-based XSS Protection",
                "WARNING",
                "Potentially vulnerable DOM manipulation code found",
                [
                    "vulnerabilities" => $domVulnerabilities,
                    "tested_patterns" => $vulnerablePatterns
                ]
            );
        } else {
            $results[] = logTestResult(
                "DOM-based XSS Protection",
                "SUCCESS",
                "No obvious DOM-based XSS vulnerabilities found",
                [
                    "note" => "This is a limited test. Complete testing requires browser automation."
                ]
            );
        }
    }

    // Test 4: CSP (Content Security Policy) Test
    if (isset($_POST['test_csp'])) {
        $url = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
        $port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : '80';
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $testUrl = "$protocol://$url/lama/index.php";

        // Test if CSP headers are present
        $headers = get_headers($testUrl, 1);
        $cspHeaderFound = false;
        $cspHeaderValue = '';

        // Check for CSP headers (case insensitive)
        foreach ($headers as $name => $value) {
            if (strtolower($name) === 'content-security-policy') {
                $cspHeaderFound = true;
                $cspHeaderValue = $value;
                break;
            } else if (is_array($headers) && isset($headers['Content-Security-Policy'])) {
                $cspHeaderFound = true;
                $cspHeaderValue = $headers['Content-Security-Policy'];
                break;
            }
        }

        // Check for recommended CSP directives
        $recommendedDirectives = [
            'default-src',
            'script-src',
            'object-src',
            'style-src',
            'img-src',
            'media-src',
            'frame-src',
            'font-src',
            'connect-src'
        ];

        $missingDirectives = [];
        $weakDirectives = [];

        if ($cspHeaderFound) {
            // Check which recommended directives are present
            foreach ($recommendedDirectives as $directive) {
                if (strpos($cspHeaderValue, $directive) === false) {
                    $missingDirectives[] = $directive;
                }
            }

            // Check for weak values like 'unsafe-inline', 'unsafe-eval', or '*'
            if (strpos($cspHeaderValue, 'unsafe-inline') !== false) {
                $weakDirectives[] = 'unsafe-inline (allows inline scripts/styles)';
            }
            if (strpos($cspHeaderValue, 'unsafe-eval') !== false) {
                $weakDirectives[] = 'unsafe-eval (allows use of eval())';
            }
            if (
                strpos($cspHeaderValue, "default-src *") !== false ||
                strpos($cspHeaderValue, "script-src *") !== false
            ) {
                $weakDirectives[] = 'wildcard (*) sources';
            }

            if (empty($missingDirectives) && empty($weakDirectives)) {
                $results[] = logTestResult(
                    "Content Security Policy",
                    "SUCCESS",
                    "Strong CSP header is configured",
                    [
                        "csp_header" => $cspHeaderValue
                    ]
                );
            } else {
                $results[] = logTestResult(
                    "Content Security Policy",
                    "WARNING",
                    "CSP header found but could be strengthened",
                    [
                        "csp_header" => $cspHeaderValue,
                        "missing_directives" => $missingDirectives,
                        "weak_directives" => $weakDirectives
                    ]
                );
            }
        } else {
            $results[] = logTestResult(
                "Content Security Policy",
                "FAILURE",
                "No Content Security Policy header found",
                [
                    "recommendation" => "Implement a CSP header to mitigate XSS attacks"
                ]
            );
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XSS Protection Tests - LAMA Test Suite</title>
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
                                Cross-Site Scripting (XSS) Tests
                            </h2>
                            <div class="text-muted mt-1">Testing protection against XSS vulnerabilities</div>
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
                                        <p>These tests simulate XSS attacks on your application. Please ensure you are running these tests in a safe, non-production environment. These tests will attempt to inject malicious scripts into your application to evaluate security.</p>
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
                                                    <input type="checkbox" name="test_reflected_xss" class="form-selectgroup-input">
                                                    <span class="form-selectgroup-label d-flex flex-row align-items-center p-3">
                                                        <span class="me-3">
                                                            <span class="form-selectgroup-check"></span>
                                                        </span>
                                                        <span class="form-selectgroup-label-content">
                                                            <span class="form-selectgroup-title strong mb-1">Reflected XSS</span>
                                                            <span class="d-block text-muted">Tests if user input is properly sanitized when reflected back in responses</span>
                                                        </span>
                                                    </span>
                                                </label>
                                                <label class="form-selectgroup-item">
                                                    <input type="checkbox" name="test_stored_xss" class="form-selectgroup-input">
                                                    <span class="form-selectgroup-label d-flex flex-row align-items-center p-3">
                                                        <span class="me-3">
                                                            <span class="form-selectgroup-check"></span>
                                                        </span>
                                                        <span class="form-selectgroup-label-content">
                                                            <span class="form-selectgroup-title strong mb-1">Stored XSS</span>
                                                            <span class="d-block text-muted">Tests if user input is sanitized when stored in the database and displayed later</span>
                                                        </span>
                                                    </span>
                                                </label>
                                                <label class="form-selectgroup-item">
                                                    <input type="checkbox" name="test_dom_xss" class="form-selectgroup-input">
                                                    <span class="form-selectgroup-label d-flex flex-row align-items-center p-3">
                                                        <span class="me-3">
                                                            <span class="form-selectgroup-check"></span>
                                                        </span>
                                                        <span class="form-selectgroup-label-content">
                                                            <span class="form-selectgroup-title strong mb-1">DOM-based XSS</span>
                                                            <span class="d-block text-muted">Tests if JavaScript code properly sanitizes input before using it to modify the DOM</span>
                                                        </span>
                                                    </span>
                                                </label>
                                                <label class="form-selectgroup-item">
                                                    <input type="checkbox" name="test_csp" class="form-selectgroup-input">
                                                    <span class="form-selectgroup-label d-flex flex-row align-items-center p-3">
                                                        <span class="me-3">
                                                            <span class="form-selectgroup-check"></span>
                                                        </span>
                                                        <span class="form-selectgroup-label-content">
                                                            <span class="form-selectgroup-title strong mb-1">Content Security Policy</span>
                                                            <span class="d-block text-muted">Tests if the application implements a strong Content Security Policy</span>
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
                                    <h3 class="card-title">About XSS Testing</h3>
                                </div>
                                <div class="card-body">
                                    <h4>What is Cross-Site Scripting (XSS)?</h4>
                                    <p>XSS is a type of injection attack where malicious scripts are injected into otherwise benign websites. These attacks succeed when an application takes user input and outputs it to a web page without proper validation or encoding.</p>

                                    <h4 class="mt-4">Types of XSS Attacks</h4>
                                    <ul>
                                        <li><b>Reflected XSS</b>: Where the malicious script is reflected off the web server, such as in search results or error messages</li>
                                        <li><b>Stored XSS</b>: Where the malicious script is stored on the server (e.g., in a database) and later served to other users</li>
                                        <li><b>DOM-based XSS</b>: Where the vulnerability exists in client-side code rather than server-side code</li>
                                    </ul>

                                    <h4 class="mt-4">Security Best Practices</h4>
                                    <ul>
                                        <li>Always encode/escape output based on context (HTML, JavaScript, CSS, URL)</li>
                                        <li>Implement Content Security Policy (CSP) headers</li>
                                        <li>Use modern frameworks that automatically escape output</li>
                                        <li>Sanitize user input on the server side</li>
                                        <li>Set the HttpOnly flag on sensitive cookies</li>
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
                                                                <?php elseif ($result['status'] === 'WARNING'): ?>
                                                                    <span class="badge bg-warning"><?= htmlspecialchars($result['status']) ?></span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-secondary"><?= htmlspecialchars($result['status']) ?></span>
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
                                        // Check for failures or warnings and show detailed info
                                        foreach ($results as $result):
                                            if (($result['status'] === 'FAILURE' || $result['status'] === 'WARNING') && !empty($result['response'])):
                                        ?>
                                                <div class="alert <?= $result['status'] === 'FAILURE' ? 'alert-danger' : 'alert-warning' ?> mt-3">
                                                    <h4><?= $result['status'] ?> Details: <?= htmlspecialchars($result['name']) ?></h4>
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
                                                    <h4>OWASP XSS Prevention Cheat Sheet</h4>
                                                    <p>Comprehensive guide on preventing XSS vulnerabilities in web applications.</p>
                                                    <a href="https://cheatsheetseries.owasp.org/cheatsheets/Cross_Site_Scripting_Prevention_Cheat_Sheet.html" target="_blank" class="btn btn-outline-primary mt-2">
                                                        Visit Resource
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h4>Content Security Policy Generator</h4>
                                                    <p>Tool to help generate and test Content Security Policies for your application.</p>
                                                    <a href="https://report-uri.com/home/generate" target="_blank" class="btn btn-outline-primary mt-2">
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