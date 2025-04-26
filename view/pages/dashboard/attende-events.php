<!DOCTYPE html>
<?php
include "../../../config/session.php";
checkSession();

?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events</title>
    <link href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,800" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link href="https://demos.creative-tim.com/soft-ui-dashboard/assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="https://demos.creative-tim.com/soft-ui-dashboard/assets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>

    <!-- CSS Files -->
    <link
        id="pagestyle"
        href="../../styles/css/soft-ui-dashboard.css"
        rel="stylesheet" />

    <style>
        .banner {
            background: #4e54c8;
            position: relative;
            overflow: hidden;
            border-radius: 1rem;
        }

        .banner::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('../../assets/images/events-banner.png');
            background-repeat: no-repeat;
            background-position: right center;
            background-size: cover;
        }

        .event-card {
            border-radius: 1rem;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .event-card:hover {
            transform: translateY(-5px);
        }

        .event-image {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }

        .like-button {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: white;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .like-button:hover {
            background: #f3f4f6;
        }

        .category-pills {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
        }

        .category-pill {
            padding: 0.5rem 1rem;
            border-radius: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .category-pill.active {
            background: #4F46E5;
            color: white;
        }
    </style>
</head>

<body class="g-sidenav-show bg-gray-100">
    <?php include '../../partials/dashboar-sidebar.php' ?>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <?php include '../../partials/dashboard-navbar.php' ?>

        <div class="container-fluid py-4">
            <!-- Banner Section -->
            <div class="banner p-5 mb-4">
                <div class="row align-items-center" style="position: relative; z-index: 1;">
                    <div class="col-lg-8">
                        <h1 class="text-white mb-3">
                            Create an event to become an organizer in Lama
                        </h1>
                        <p class="text-white opacity-8 mb-4">Enter in the world of events. Discover now the latest Events or start creating your own!</p>
                        <div class="d-flex gap-3">
                            <button class="btn btn-white">Create now</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category Filter -->
            <div class="category-pills">
                <div class="category-pill active">Technical</div>
                <div class="category-pill">Music</div>
                <div class="category-pill">Cultural</div>
                <div class="category-pill">Sports</div>
            </div>

            <!-- Events Grid -->
            <div class="row">
                <div class="col-12">
                    <h5 class="mb-4">Events Participated</h5>
                </div>
                <?php for ($i = 0; $i < 3; $i++): ?>
                    <div class="col-lg-4 mb-4">
                        <div class="card event-card">
                            <div class="position-relative">
                                <img src="../../assets/images/abstract-image.png" class="event-image" alt="Abstract Colors">
                                <div class="like-button">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title mb-1">Abstract Colors</h5>
                                <p class="text-sm text-muted">By Esthera Jackson</p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div class="avatar-group">
                                        <img src="https://ui-avatars.com/api/?background=random" class="avatar avatar-xs rounded-circle" alt="">
                                        <img src="https://ui-avatars.com/api/?background=random" class="avatar avatar-xs rounded-circle" alt="">
                                        <img src="https://ui-avatars.com/api/?background=random" class="avatar avatar-xs rounded-circle" alt="">
                                    </div>
                                    <button class="btn btn-primary btn-sm">Register</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>

                <div class="col-12 mt-4">
                    <h5 class="mb-4">Trending Events</h5>
                </div>
                <?php for ($i = 0; $i < 3; $i++): ?>
                    <div class="col-lg-4 mb-4">
                        <div class="card event-card">
                            <div class="position-relative">
                                <img src="../../assets/images/abstract-image.png" class="event-image" alt="Abstract Colors">
                                <div class="like-button">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title mb-1">Abstract Colors</h5>
                                <p class="text-sm text-muted">By Esthera Jackson</p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div class="avatar-group">
                                        <img src="https://ui-avatars.com/api/?background=random" class="avatar avatar-xs rounded-circle" alt="">
                                        <img src="https://ui-avatars.com/api/?background=random" class="avatar avatar-xs rounded-circle" alt="">
                                        <img src="https://ui-avatars.com/api/?background=random" class="avatar avatar-xs rounded-circle" alt="">
                                    </div>
                                    <button class="btn btn-primary btn-sm">Register</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </main>

    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="../../scripts/core/popper.min.js"></script>
    <script src="../../scripts/core/bootstrap.min.js"></script>
    <script src="../../scripts/soft-ui-dashboard.js"></script>
</body>

</html>