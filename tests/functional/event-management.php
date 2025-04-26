<?php
require_once "../../config/database.php";
require_once "../../controller/event.php";

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

// Run tests when form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['run_tests'])) {
    try {
        $pdo = $cnx;

        // Test 1: Event Creation
        if (isset($_POST['test_event_creation'])) {
            // Create a test event
            $eventData = [
                'organizer_id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1,
                'title' => 'Test Event ' . rand(1000, 9999),
                'description' => 'This is a test event created through automated testing.',
                'start_date' => date('Y-m-d H:i:s', strtotime('+1 week')),
                'end_date' => date('Y-m-d H:i:s', strtotime('+1 week +2 hours')),
                'location' => 'Test Venue, 123 Test Street',
                'event_type' => 'in-person',
                'category_id' => 1,
                'capacity' => 100,
                'status' => 'draft'
            ];

            $result = createEvent($pdo, $eventData);

            if ($result && is_numeric($result)) {
                $results[] = logTestResult("Event Creation", "SUCCESS", "Successfully created new event", [
                    "event_id" => $result,
                    "title" => $eventData['title']
                ]);
                // Store event ID for other tests
                $_SESSION['test_event_id'] = $result;
            } else {
                $results[] = logTestResult("Event Creation", "FAILURE", "Failed to create event", [
                    "error" => "Could not insert event"
                ]);
            }
        }

        // Test 2: Event Retrieval
        if (isset($_POST['test_event_retrieval'])) {
            $eventId = $_SESSION['test_event_id'] ?? null;

            if (!$eventId) {
                // Create a test event first
                $eventData = [
                    'organizer_id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1,
                    'title' => 'Test Event for Retrieval ' . rand(1000, 9999),
                    'description' => 'This is a test event for retrieval testing.',
                    'start_date' => date('Y-m-d H:i:s', strtotime('+1 week')),
                    'end_date' => date('Y-m-d H:i:s', strtotime('+1 week +2 hours')),
                    'location' => 'Test Venue, 123 Test Street',
                    'event_type' => 'in-person',
                    'category_id' => 1,
                    'capacity' => 100,
                    'status' => 'draft'
                ];

                $createResult = createEvent($pdo, $eventData);
                if ($createResult && is_numeric($createResult)) {
                    $eventId = $createResult;
                    $_SESSION['test_event_id'] = $eventId;
                } else {
                    $results[] = logTestResult("Event Retrieval", "ERROR", "Could not create test event for retrieval");
                    $eventId = null;
                }
            }

            if ($eventId) {
                $event = getEventById($pdo, $eventId);

                if ($event && isset($event['id']) && $event['id'] == $eventId) {
                    $results[] = logTestResult("Event Retrieval", "SUCCESS", "Successfully retrieved event", [
                        "event_id" => $event['id'],
                        "title" => $event['title']
                    ]);
                } else {
                    $results[] = logTestResult("Event Retrieval", "FAILURE", "Failed to retrieve event", [
                        "event_id" => $eventId,
                        "result" => $event
                    ]);
                }
            }
        }

        // Test 3: Event Update
        if (isset($_POST['test_event_update'])) {
            $eventId = $_SESSION['test_event_id'] ?? null;

            if (!$eventId) {
                // Create a test event first
                $eventData = [
                    'organizer_id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1,
                    'title' => 'Test Event for Update ' . rand(1000, 9999),
                    'description' => 'This is a test event for update testing.',
                    'start_date' => date('Y-m-d H:i:s', strtotime('+1 week')),
                    'end_date' => date('Y-m-d H:i:s', strtotime('+1 week +2 hours')),
                    'location' => 'Test Venue, 123 Test Street',
                    'event_type' => 'in-person',
                    'category_id' => 1,
                    'capacity' => 100,
                    'status' => 'draft'
                ];

                $createResult = createEvent($pdo, $eventData);
                if ($createResult && is_numeric($createResult)) {
                    $eventId = $createResult;
                    $_SESSION['test_event_id'] = $eventId;
                } else {
                    $results[] = logTestResult("Event Update", "ERROR", "Could not create test event for update");
                    $eventId = null;
                }
            }

            if ($eventId) {
                // Update the event
                $updatedData = [
                    'title' => 'Updated Event Title ' . rand(1000, 9999),
                    'description' => 'This description has been updated through testing.',
                    'capacity' => 200,
                    'id' => $eventId
                ];

                $updateResult = updateEvent($pdo, $eventId, $updatedData);

                if ($updateResult) {
                    // Verify the update
                    $event = getEventById($pdo, $eventId);

                    if ($event && $event['title'] == $updatedData['title']) {
                        $results[] = logTestResult("Event Update", "SUCCESS", "Successfully updated event", [
                            "event_id" => $eventId,
                            "new_title" => $updatedData['title']
                        ]);
                    } else {
                        $results[] = logTestResult("Event Update", "FAILURE", "Update reported success but data wasn't changed", [
                            "event_id" => $eventId,
                            "expected_title" => $updatedData['title'],
                            "actual_title" => $event['title'] ?? 'Unknown'
                        ]);
                    }
                } else {
                    $results[] = logTestResult("Event Update", "FAILURE", "Failed to update event", [
                        "event_id" => $eventId,
                        "error" => "Update operation returned false"
                    ]);
                }
            }
        }

        // Test 4: Event Deletion
        if (isset($_POST['test_event_deletion'])) {
            $eventId = $_SESSION['test_event_id'] ?? null;

            if (!$eventId) {
                // Create a test event first
                $eventData = [
                    'organizer_id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1,
                    'title' => 'Test Event for Deletion ' . rand(1000, 9999),
                    'description' => 'This is a test event for deletion testing.',
                    'start_date' => date('Y-m-d H:i:s', strtotime('+1 week')),
                    'end_date' => date('Y-m-d H:i:s', strtotime('+1 week +2 hours')),
                    'location' => 'Test Venue, 123 Test Street',
                    'event_type' => 'in-person',
                    'category_id' => 1,
                    'capacity' => 100,
                    'status' => 'draft'
                ];

                $createResult = createEvent($pdo, $eventData);
                if ($createResult && is_numeric($createResult)) {
                    $eventId = $createResult;
                    $_SESSION['test_event_id'] = $eventId;
                } else {
                    $results[] = logTestResult("Event Deletion", "ERROR", "Could not create test event for deletion");
                    $eventId = null;
                }
            }

            if ($eventId) {
                $userId =  $_SESSION['user_id'] ?? 1; // Use session user ID or default to 1
                $deleteResult = deleteEvent($pdo, $eventId, $userId);

                if ($deleteResult) {
                    // Verify the deletion
                    $event = getEventById($pdo, $eventId);

                    if (!$event) {
                        $results[] = logTestResult("Event Deletion", "SUCCESS", "Successfully deleted event", [
                            "event_id" => $eventId
                        ]);
                        // Clear the stored event ID since it's deleted
                        unset($_SESSION['test_event_id']);
                    } else {
                        $results[] = logTestResult("Event Deletion", "FAILURE", "Deletion reported success but event still exists", [
                            "event_id" => $eventId
                        ]);
                    }
                } else {
                    $results[] = logTestResult("Event Deletion", "FAILURE", "Failed to delete event", [
                        "event_id" => $eventId,
                        "error" => "Delete operation returned false"
                    ]);
                }
            }
        }

        // Test 5: Event Listing
        if (isset($_POST['test_event_listing'])) {
            // Create multiple test events first
            for ($i = 0; $i < 3; $i++) {
                $eventData = [
                    'organizer_id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1,
                    'title' => 'Test Event for Listing ' . rand(1000, 9999),
                    'description' => 'This is test event #' . ($i + 1) . ' for listing testing.',
                    'start_date' => date('Y-m-d H:i:s', strtotime('+' . ($i + 1) . ' week')),
                    'end_date' => date('Y-m-d H:i:s', strtotime('+' . ($i + 1) . ' week +2 hours')),
                    'location' => 'Test Venue, 123 Test Street',
                    'event_type' => 'in-person',
                    'category_id' => 1,
                    'capacity' => 100,
                    'status' => 'published'
                ];

                createEvent($pdo, $eventData);
            }

            // Try to retrieve the events
            $events = getEvents($pdo, ['limit' => 10, 'status' => 'published']);

            if ($events && is_array($events) && count($events) >= 3) {
                $results[] = logTestResult("Event Listing", "SUCCESS", "Successfully listed events", [
                    "event_count" => count($events)
                ]);
            } else {
                $results[] = logTestResult("Event Listing", "FAILURE", "Failed to list events or fewer than expected", [
                    "event_count" => is_array($events) ? count($events) : 0,
                    "expected" => "At least 3 events"
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
    <title>Event Management Tests - LAMA Test Suite</title>
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
                                Event Management Tests
                            </h2>
                            <div class="text-muted mt-1">Testing event creation, retrieval, updating, and deletion</div>
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
                                                    <input type="checkbox" name="test_event_creation" class="form-selectgroup-input">
                                                    <span class="form-selectgroup-label">Event Creation</span>
                                                </label>
                                                <label class="form-selectgroup-item">
                                                    <input type="checkbox" name="test_event_retrieval" class="form-selectgroup-input">
                                                    <span class="form-selectgroup-label">Event Retrieval</span>
                                                </label>
                                                <label class="form-selectgroup-item">
                                                    <input type="checkbox" name="test_event_update" class="form-selectgroup-input">
                                                    <span class="form-selectgroup-label">Event Update</span>
                                                </label>
                                                <label class="form-selectgroup-item">
                                                    <input type="checkbox" name="test_event_deletion" class="form-selectgroup-input">
                                                    <span class="form-selectgroup-label">Event Deletion</span>
                                                </label>
                                                <label class="form-selectgroup-item">
                                                    <input type="checkbox" name="test_event_listing" class="form-selectgroup-input">
                                                    <span class="form-selectgroup-label">Event Listing</span>
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
                                    <p>The event management tests verify the system's ability to:</p>
                                    <ul>
                                        <li>Create new events with various parameters</li>
                                        <li>Retrieve individual event details</li>
                                        <li>Update existing event information</li>
                                        <li>Delete events from the system</li>
                                        <li>List multiple events with filtering options</li>
                                    </ul>
                                    <p class="text-muted">These tests create temporary events in the database which are removed during the deletion test.</p>
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