<?php
$root_dir = dirname(__DIR__);
set_include_path(get_include_path() . PATH_SEPARATOR . $root_dir);

class TestUtils
{
    /**
     * Initialize test environment
     */
    public static function init()
    {
        // Create logs directory if it doesn't exist
        $log_dir = __DIR__ . '/logs';
        if (!file_exists($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
    }

    /**
     * Log test results to file
     */
    public static function logTestResult($testName, $status, $message, $response = "")
    {
        $log_dir = __DIR__ . '/logs';
        $log_file = $log_dir . '/test_results.log';
        $date = date("Y-m-d H:i:s");

        $log_entry = "[$date] TEST: $testName | STATUS: $status | MESSAGE: $message";
        if (!empty($response)) {
            $log_entry .= " | RESPONSE: " . json_encode($response);
        }
        $log_entry .= "\n";

        file_put_contents($log_file, $log_entry, FILE_APPEND);

        return [
            'name' => $testName,
            'status' => $status,
            'message' => $message,
            'response' => $response,
            'timestamp' => $date
        ];
    }

    /**
     * Run a test and capture its output
     */
    public static function runTest($testFile)
    {
        ob_start();
        include $testFile;
        $output = ob_get_clean();
        return $output;
    }

    /**
     * Save test results to individual log file
     */
    public static function saveTestResult($testName, $status, $output)
    {
        $log_dir = __DIR__ . '/logs';
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0777, true);
        }

        $log_file = $log_dir . '/' . $testName . '.log';
        $content = "Test: $testName\n";
        $content .= "Status: $status\n";
        $content .= "Time: " . date('Y-m-d H:i:s') . "\n";
        $content .= "Output:\n$output\n";

        file_put_contents($log_file, $content);
        return $status === 'Passed';
    }

    /**
     * Assert that a condition is true
     */
    public static function assert($condition, $message)
    {
        if (!$condition) {
            throw new Exception("Assertion failed: $message");
        }
        return true;
    }

    /**
     * Assert that two values are equal
     */
    public static function assertEquals($expected, $actual, $message = "")
    {
        if ($expected !== $actual) {
            throw new Exception("Assertion failed: Expected '$expected', got '$actual'. $message");
        }
        return true;
    }

    /**
     * Assert that a value is not null
     */
    public static function assertNotNull($value, $message = "")
    {
        if ($value === null) {
            throw new Exception("Assertion failed: Value is null. $message");
        }
        return true;
    }

    /**
     * Clean up test data
     */
    public static function cleanup()
    {
        // Add any cleanup logic here
    }

    /**
     * Delete test users from the database
     */
    public static function cleanupTestUsers($pdo)
    {
        try {
            $results = [];
            $sql = "DELETE FROM users WHERE email LIKE 'testuser%' OR email LIKE 'login_test%' OR email LIKE 'session_test%' OR email LIKE 'logout_test%'";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $deleted = $stmt->rowCount();
            
            if ($deleted > 0) {
                $results[] = self::logTestResult("Cleanup", "SUCCESS", "Deleted $deleted test users");
            }
            return $results;
        } catch (Exception $e) {
            return [self::logTestResult("Cleanup", "ERROR", "Failed to cleanup test users: " . $e->getMessage())];
        }
    }

    public static function cleanupTestEvents($pdo)
    {
        try {
            $results = [];
            $sql = "DELETE FROM events WHERE title LIKE 'Test Event%' OR title LIKE 'Updated Event%'";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $deleted = $stmt->rowCount();
            
            if ($deleted > 0) {
                $results[] = self::logTestResult("Cleanup", "SUCCESS", "Deleted $deleted test events");
            }
            return $results;
        } catch (Exception $e) {
            return [self::logTestResult("Cleanup", "ERROR", "Failed to cleanup test events: " . $e->getMessage())];
        }
    }

    public static function cleanupTestSearchData($pdo)
    {
        try {
            $results = [];
            // Clean up any test data created during search tests
            $sql = "DELETE FROM events WHERE title LIKE 'Test Event for Listing%'";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $deleted = $stmt->rowCount();
            
            if ($deleted > 0) {
                $results[] = self::logTestResult("Cleanup", "SUCCESS", "Deleted $deleted test search events");
            }
            return $results;
        } catch (Exception $e) {
            return [self::logTestResult("Cleanup", "ERROR", "Failed to cleanup test search data: " . $e->getMessage())];
        }
    }

    public static function cleanupAll($pdo)
    {
        $results = [];
        $results = array_merge($results, self::cleanupTestUsers($pdo));
        $results = array_merge($results, self::cleanupTestEvents($pdo));
        $results = array_merge($results, self::cleanupTestSearchData($pdo));
        return $results;
    }
}

// Initialize test environment
TestUtils::init();
