<?php
require_once "../../config/database.php";

$logFilePath = "../logs/functional_tests.log";
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
 * Helper function to search events
 * This is a mock/simplified version - replace with your actual search function
 */
function searchEvents($pdo, $query, $options = [])
{
    $sql = "SELECT * FROM events WHERE 
            (title LIKE :query OR description LIKE :query)";

    // Add filters based on options
    if (!empty($options['category_id'])) {
        $sql .= " AND category_id = :category_id";
    }

    if (!empty($options['event_type'])) {
        $sql .= " AND event_type = :event_type";
    }

    if (!empty($options['start_date'])) {
        $sql .= " AND start_date >= :start_date";
    }

    if (!empty($options['end_date'])) {
        $sql .= " AND end_date <= :end_date";
    }

    // Add limit
    $limit = isset($options['limit']) ? intval($options['limit']) : 10;
    $sql .= " LIMIT :limit";

    // Prepare and execute the query
    $stmt = $pdo->prepare($sql);
    $queryParam = "%" . $query . "%";
    $stmt->bindParam(':query', $queryParam, PDO::PARAM_STR);

    if (!empty($options['category_id'])) {
        $stmt->bindParam(':category_id', $options['category_id'], PDO::PARAM_INT);
    }

    if (!empty($options['event_type'])) {
        $stmt->bindParam(':event_type', $options['event_type'], PDO::PARAM_STR);
    }

    if (!empty($options['start_date'])) {
        $stmt->bindParam(':start_date', $options['start_date'], PDO::PARAM_STR);
    }

    if (!empty($options['end_date'])) {
        $stmt->bindParam(':end_date', $options['end_date'], PDO::PARAM_STR);
    }

    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Run tests when form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_tests'])) {
    try {
        $pdo = $cnx;

        // Test 1: Basic Search
        if (isset($_POST['test_basic_search'])) {
            // Use a search term likely to find results from our mock data
            $searchTerm = 'music';
            $result = searchEvents($pdo, $searchTerm);

            if (is_array($result)) {
                $matchCount = count($result);
                $results[] = logTestResult("Basic Search", "SUCCESS", "Search returned $matchCount results", [
                    "search_term" => $searchTerm,
                    "result_count" => $matchCount,
                    "sample_results" => array_slice($result, 0, 2) // First 2 results as sample
                ]);
            } else {
                $results[] = logTestResult("Basic Search", "FAILURE", "Search failed to execute", [
                    "search_term" => $searchTerm,
                    "error" => $result
                ]);
            }
        }

        // Test 2: Category Filter
        if (isset($_POST['test_category_filter'])) {
            $searchTerm = '';  // Empty search term to get all events in category
            $categoryId = 1;   // Music events

            $result = searchEvents($pdo, $searchTerm, ['category_id' => $categoryId]);

            if (is_array($result)) {
                $matchCount = count($result);
                // Check if all results have the correct category_id
                $allCorrectCategory = true;
                foreach ($result as $event) {
                    if ($event['category_id'] != $categoryId) {
                        $allCorrectCategory = false;
                        break;
                    }
                }

                if ($allCorrectCategory) {
                    $results[] = logTestResult("Category Filter", "SUCCESS", "Search returned $matchCount events in the correct category", [
                        "category_id" => $categoryId,
                        "result_count" => $matchCount
                    ]);
                } else {
                    $results[] = logTestResult("Category Filter", "FAILURE", "Search returned events from wrong categories", [
                        "category_id" => $categoryId,
                        "result_count" => $matchCount,
                        "sample_results" => array_slice($result, 0, 2)
                    ]);
                }
            } else {
                $results[] = logTestResult("Category Filter", "FAILURE", "Category filter search failed to execute", [
                    "category_id" => $categoryId,
                    "error" => $result
                ]);
            }
        }

        // Test 3: Date Range Filter
        if (isset($_POST['test_date_filter'])) {
            $searchTerm = '';  // Empty search term
            $startDate = date('Y-m-d', strtotime('now'));
            $endDate = date('Y-m-d', strtotime('+3 months'));

            $result = searchEvents($pdo, $searchTerm, [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]);

            if (is_array($result)) {
                $matchCount = count($result);
                $results[] = logTestResult("Date Range Filter", "SUCCESS", "Search returned $matchCount events in date range", [
                    "start_date" => $startDate,
                    "end_date" => $endDate,
                    "result_count" => $matchCount
                ]);
            } else {
                $results[] = logTestResult("Date Range Filter", "FAILURE", "Date filter search failed to execute", [
                    "start_date" => $startDate,
                    "end_date" => $endDate,
                    "error" => $result
                ]);
            }
        }

        // Test 4: Event Type Filter
        if (isset($_POST['test_type_filter'])) {
            $searchTerm = '';  // Empty search term
            $eventType = 'in-person';

            $result = searchEvents($pdo, $searchTerm, ['event_type' => $eventType]);

            if (is_array($result)) {
                $matchCount = count($result);
                // Check if all results have the correct event_type
                $allCorrectType = true;
                foreach ($result as $event) {
                    if ($event['event_type'] != $eventType) {
                        $allCorrectType = false;
                        break;
                    }
                }

                if ($allCorrectType) {
                    $results[] = logTestResult("Event Type Filter", "SUCCESS", "Search returned $matchCount events of the correct type", [
                        "event_type" => $eventType,
                        "result_count" => $matchCount
                    ]);
                } else {
                    $results[] = logTestResult("Event Type Filter", "FAILURE", "Search returned events of wrong type", [
                        "event_type" => $eventType,
                        "result_count" => $matchCount,
                        "sample_results" => array_slice($result, 0, 2)
                    ]);
                }
            } else {
                $results[] = logTestResult("Event Type Filter", "FAILURE", "Event type filter search failed to execute", [
                    "event_type" => $eventType,
                    "error" => $result
                ]);
            }
        }

        // Test 5: Combined Filters
        if (isset($_POST['test_combined_filters'])) {
            $searchTerm = 'test';  // Search term
            $categoryId = 1;       // Music events
            $eventType = 'in-person';

            $result = searchEvents($pdo, $searchTerm, [
                'category_id' => $categoryId,
                'event_type' => $eventType
            ]);

            if (is_array($result)) {
                $matchCount = count($result);
                $results[] = logTestResult("Combined Filters", "SUCCESS", "Search returned $matchCount events with combined filters", [
                    "search_term" => $searchTerm,
                    "category_id" => $categoryId,
                    "event_type" => $eventType,
                    "result_count" => $matchCount
                ]);
            } else {
                $results[] = logTestResult("Combined Filters", "FAILURE", "Combined filter search failed to execute", [
                    "search_term" => $searchTerm,
                    "category_id" => $categoryId,
                    "event_type" => $eventType,
                    "error" => $result
                ]);
            }
        }
    } catch (Exception $e) {
        $results[] = logTestResult("Test Suite", "ERROR", "Database connection error: " . $e->getMessage());
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
                                                    <input type="checkbox" name="test_category_filter" class="form-selectgroup-input">
                                                    <span class="form-selectgroup-label">Category Filter</span>
                                                </label>
                                                <label class="form-selectgroup-item">
                                                    <input type="checkbox" name="test_date_filter" class="form-selectgroup-input">
                                                    <span class="form-selectgroup-label">Date Range Filter</span>
                                                </label>
                                                <label class="form-selectgroup-item">
                                                    <input type="checkbox" name="test_type_filter" class="form-selectgroup-input">
                                                    <span class="form-selectgroup-label">Event Type Filter</span>
                                                </label>
                                                <label class="form-selectgroup-item">
                                                    <input type="checkbox" name="test_combined_filters" class="form-selectgroup-input">
                                                    <span class="form-selectgroup-label">Combined Filters</span>
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
                                        <li>Filter events by category</li>
                                        <li>Filter events by date range</li>
                                        <li>Filter events by event type (in-person/online)</li>
                                        <li>Combine multiple search criteria</li>
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