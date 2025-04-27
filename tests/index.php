<?php
require_once __DIR__ . '/auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LAMA Test Suite - Dashboard</title>
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
                            <div class="text-muted mt-1">Welcome, <?= htmlspecialchars($_SESSION['tester_username']) ?></div>
                        </div>
                        <div class="col-auto ms-auto d-print-none">
                            <div class="btn-list">
                                <a href="logout.php" class="btn btn-outline-danger">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-logout" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2"></path>
                                        <path d="M9 12h12l-3 -3"></path>
                                        <path d="M18 15l3 -3"></path>
                                    </svg>
                                    Logout
                                </a>
                            </div>
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
                                    <div class="list-group list-group-flush">
                                        <a href="functional/authentication.php" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h5 class="mb-1">Authentication Tests</h5>
                                                <span class="badge bg-primary text-white">Login/Register</span>
                                            </div>
                                            <p class="mb-1">Test user authentication and registration functionality</p>
                                        </a>
                                        <a href="functional/event-management.php" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h5 class="mb-1">Event Management Tests</h5>
                                                <span class="badge bg-primary text-white">CRUD</span>
                                            </div>
                                            <p class="mb-1">Test event creation, reading, updating, and deletion</p>
                                        </a>
                                        <a href="functional/search.php" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h5 class="mb-1">Search Tests</h5>
                                                <span class="badge bg-primary text-white">Search</span>
                                            </div>
                                            <p class="mb-1">Test event search and filtering functionality</p>
                                        </a>
                                    </div>
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
                                    <div class="list-group list-group-flush">
                                        <a href="security/sql-injection.php" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h5 class="mb-1">SQL Injection Tests</h5>
                                                <span class="badge bg-danger text-white">Security</span>
                                            </div>
                                            <p class="mb-1">Test protection against SQL injection attacks</p>
                                        </a>
                                        <a href="security/xss.php" class="list-group-item list-group-item-action">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h5 class="mb-1">XSS Tests</h5>
                                                <span class="badge bg-danger text-white">Security</span>
                                            </div>
                                            <p class="mb-1">Test protection against Cross-Site Scripting attacks</p>
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
</body>

</html>