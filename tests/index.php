<?php
// Test Dashboard
$base_url = "/lama/tests";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LAMA - Test Suite</title>
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
                                LAMA Test Suite
                            </h2>
                            <div class="text-muted mt-1">Complete testing dashboard for Event Management System</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="page-body">
                <div class="container-xl">
                    <div class="row row-cards">
                        <!-- Functional Tests -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Functional Tests</h3>
                                </div>
                                <div class="card-body">
                                    <p>Tests for core functionality of the application</p>
                                </div>
                                <div class="list-group list-group-flush">
                                    <a href="<?= $base_url ?>/functional/authentication.php" class="list-group-item list-group-item-action">Authentication Tests</a>
                                    <a href="<?= $base_url ?>/functional/event-management.php" class="list-group-item list-group-item-action">Event Management Tests</a>
                                    <a href="<?= $base_url ?>/functional/search.php" class="list-group-item list-group-item-action">Search Functionality Tests</a>
                                </div>
                            </div>
                        </div>

                        <!-- Security Tests -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Security Tests</h3>
                                </div>
                                <div class="card-body">
                                    <p>Tests to ensure application security and prevent vulnerabilities</p>
                                </div>
                                <div class="list-group list-group-flush">
                                    <a href="<?= $base_url ?>/security/sql-injection.php" class="list-group-item list-group-item-action">SQL Injection Tests</a>
                                    <a href="<?= $base_url ?>/security/xss.php" class="list-group-item list-group-item-action">Cross-Site Scripting (XSS) Tests</a>
                                </div>
                            </div>
                        </div>
                        <!-- Test Results -->
                        <div class="col-12 mt-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Test Results</h3>
                                </div>
                                <div class="card-body">
                                    <a href="<?= $base_url ?>/test-results.php" class="btn btn-primary">
                                        View All Test Results
                                    </a>
                                    <a href="<?= $base_url ?>/run-all-tests.php" class="btn btn-success ms-2">
                                        Run All Tests
                                    </a>
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