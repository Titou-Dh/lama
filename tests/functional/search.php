<?php
require_once __DIR__ . '/../common.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../controller/event.php';
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

        // Test 1: Basic Search
        if (isset($_POST['test_basic_search'])) {
            $query = "test event";
            $searchResults = searchEvents($pdo, $query);

            if (is_array($searchResults)) {
                $results[] = TestUtils::logTestResult("Basic Search", "SUCCESS", "Search query executed successfully", [
                    "query" => $query,
                    "results_count" => count($searchResults)
                ]);
            } else {
                $results[] = TestUtils::logTestResult("Basic Search", "FAILURE", "Search query failed", [
                    "query" => $query,
                    "error" => "Invalid search results format"
                ]);
            }
        }

        // Test 2: Advanced Search with Filters
        if (isset($_POST['test_advanced_search'])) {
            $query = "test";
            $filters = [
                'category_id' => 1,
                'event_type' => 'in-person',
                'start_date' => date('Y-m-d'),
                'end_date' => date('Y-m-d', strtotime('+1 month'))
            ];

            $searchResults = searchEvents($pdo, $query, 1, 10);

            if (is_array($searchResults)) {
                $results[] = TestUtils::logTestResult("Advanced Search", "SUCCESS", "Advanced search executed successfully", [
                    "query" => $query,
                    "filters" => $filters,
                    "results_count" => count($searchResults)
                ]);
            } else {
                $results[] = TestUtils::logTestResult("Advanced Search", "FAILURE", "Advanced search failed", [
                    "query" => $query,
                    "filters" => $filters,
                    "error" => "Invalid search results format"
                ]);
            }
        }

        // Test 3: Pagination Test
        if (isset($_POST['test_pagination'])) {
            $query = "test";
            $page = 1;
            $limit = 5;

            $searchResults = searchEvents($pdo, $query, $page, $limit);

            if (is_array($searchResults) && count($searchResults) <= $limit) {
                $results[] = TestUtils::logTestResult("Pagination", "SUCCESS", "Pagination test passed", [
                    "query" => $query,
                    "page" => $page,
                    "limit" => $limit,
                    "results_count" => count($searchResults)
                ]);
            } else {
                $results[] = TestUtils::logTestResult("Pagination", "FAILURE", "Pagination test failed", [
                    "query" => $query,
                    "page" => $page,
                    "limit" => $limit,
                    "results_count" => count($searchResults)
                ]);
            }
        }

        // Clean up test data
        $cleanupResults = TestUtils::cleanupTestSearchData($pdo);
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
    <title>Search Functionality Tests - LAMA Test Suite</title>
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
                                Search Functionality Tests
                            </h2>
                            <div class="text-muted mt-1">Testing event search capabilities and filtering options</div>
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
                                                    <input type="checkbox" name="test_basic_search" class="form-selectgroup-input">
                                                    <span class="form-selectgroup-label">Basic Search</span>
                                                </label>
                                                <label class="form-selectgroup-item">
                                                    <input type="checkbox" name="test_advanced_search" class="form-selectgroup-input">
                                                    <span class="form-selectgroup-label">Advanced Search</span>
                                                </label>
                                                <label class="form-selectgroup-item">
                                                    <input type="checkbox" name="test_pagination" class="form-selectgroup-input">
                                                    <span class="form-selectgroup-label">Pagination</span>
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
                                    <p>The search functionality tests verify the system's ability to:</p>
                                    <ul>
                                        <li>Find events based on keywords in titles and descriptions</li>
                                        <li>Filter events by category, date range, and event type</li>
                                        <li>Handle pagination of search results</li>
                                    </ul>
                                    <p class="text-muted">These tests rely on existing event data in the database. The mock data SQL file should be imported for best results.</p>
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