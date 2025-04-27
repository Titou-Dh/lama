<!DOCTYPE html>
<?php
include "../../../config/session.php";
checkSession();
include "../../../config/database.php";
include "../../../controller/event.php";
include "../../../controller/categories.php";

/**
 * Helper function to handle image paths
 * 
 * @param string $imagePath The image path to process
 * @return string The processed image path
 */
function getImagePath($imagePath)
{
    if (empty($imagePath)) {
        return "../../assets/images/default-event.jpg";
    }

    if (strpos($imagePath, '/lama/uploads/') === 0) {
        return $imagePath;
    }

    if (strpos($imagePath, '../') === 0) {
        return "../$imagePath";
    }

    return "../../assets/images/default-event.jpg";
}

$userId = $_SESSION['user_id'];
$categories = getCategories($cnx);

$selectedCategory = $_GET['category'] ?? null;

// Get upcoming events the user has registered for
$upcomingEvents = getEvents(
    $cnx,
    [
        'attendee_id' => $userId,
        'date_from' => date('Y-m-d'),
        'status' => 'published'
    ],
    1,
    3
);

// Get past events the user has attended
$pastEvents = getEvents(
    $cnx,
    [
        'attendee_id' => $userId,
        'date_to' => date('Y-m-d', strtotime('-1 day')),
        'status' => 'published'
    ],
    1,
    3
);

// Get featured events
$featuredEvents = getFeaturedEvents($cnx, 3);

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
            height: 100%;
        }

        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
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
            flex-wrap: wrap;
        }

        .category-pill {
            padding: 0.5rem 1rem;
            border-radius: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f3f4f6;
            color: #4F46E5;
            text-decoration: none;
        }

        .category-pill:hover {
            background: #e5e7eb;
        }

        .category-pill.active {
            background: #4F46E5;
            color: white;
        }

        .section-title {
            position: relative;
            margin-bottom: 1.5rem;
            font-weight: 700;
        }

        .section-title::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -8px;
            height: 3px;
            width: 50px;
            background: linear-gradient(90deg, #4F46E5, #7c74f1);
            border-radius: 2px;
        }

        .empty-state {
            background-color: #f8f9fa;
            border-radius: 0.5rem;
            padding: 2rem;
            text-align: center;
            margin: 1rem 0;
        }

        .event-date-badge {
            position: absolute;
            top: 1rem;
            left: 1rem;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 0.5rem;
            padding: 0.5rem;
            font-weight: bold;
            color: #4F46E5;
        }

        .status-badge {
            position: absolute;
            bottom: 1rem;
            left: 1rem;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-badge.upcoming {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-badge.past {
            background-color: #f3f4f6;
            color: #6b7280;
        }

        .status-badge.featured {
            background-color: #fef3c7;
            color: #92400e;
        }
    </style>
</head>

<body class="g-sidenav-show bg-gray-100">
    <?php include '../../partials/dashboar-sidebar.php' ?>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <?php include '../../partials/dashboard-navbar.php' ?>

        <div class="container-fluid py-4"> <!-- Banner Section -->
            <div class="banner p-5 mb-4">
                <div class="row align-items-center" style="position: relative; z-index: 1;">
                    <div class="col-lg-8">
                        <h1 class="text-white mb-3">
                            Create an event to become an organizer in Lama
                        </h1>
                        <p class="text-white opacity-8 mb-4">Enter the world of events. Discover the latest events or start creating your own!</p>
                        <div class="d-flex gap-3">
                            <a href="create-event.php" class="btn btn-white">Create now</a>
                            <a href="../recommended-events.php" class="btn btn-outline-light">Explore events</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category Filter -->
            <div class="category-pills">
                <a href="?category=" class="category-pill <?php echo empty($selectedCategory) ? 'active' : ''; ?>">All Events</a>
                <?php foreach ($categories as $category): ?>
                    <a href="?category=<?php echo $category['id']; ?>"
                        class="category-pill <?php echo $selectedCategory == $category['id'] ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </a>
                <?php endforeach; ?>
            </div> <!-- Upcoming Events Section -->
            <div class="row">
                <div class="col-12">
                    <h5 class="section-title">Upcoming Events You're Attending</h5>
                </div>

                <?php if (!empty($upcomingEvents)): ?>
                    <?php foreach ($upcomingEvents as $event): ?>
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                            <div class="card event-card shadow-sm h-100">
                                <div class="position-relative">
                                    <img src="<?php echo getImagePath($event['image']); ?>"
                                        class="event-image"
                                        alt="<?php echo htmlspecialchars($event['title']); ?>">

                                    <div class="event-date-badge">
                                        <i class="far fa-calendar-alt"></i>
                                        <?php echo date('d M', strtotime($event['start_date'])); ?>
                                    </div>

                                    <div class="like-button">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" />
                                        </svg>
                                    </div>

                                    <span class="status-badge upcoming">Upcoming</span>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title mb-1"><?php echo htmlspecialchars($event['title']); ?></h5>
                                    <p class="text-sm text-muted mb-2">By <?php echo htmlspecialchars($event['organizer_name']); ?></p>
                                    <p class="card-text small mb-3">
                                        <?php echo substr(htmlspecialchars($event['description']), 0, 80); ?>...
                                    </p>
                                    <div class="mt-auto d-flex justify-content-between align-items-center">
                                        <span class="badge bg-light text-primary">
                                            <?php echo htmlspecialchars($event['category_name']); ?>
                                        </span>
                                        <a href="../event-details.php?id=<?php echo $event['id']; ?>" class="btn btn-sm btn-primary">View Details</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="empty-state">
                            <div class="mb-3">
                                <i class="far fa-calendar fa-3x text-muted"></i>
                            </div>
                            <h5>No upcoming events</h5>
                            <p class="text-muted">You haven't registered for any upcoming events yet.</p>
                            <a href="../recommended-events.php" class="btn btn-primary">Discover Events</a>
                        </div>
                    </div> <?php endif; ?>

                <?php if (!empty($upcomingEvents)): ?>
                    <div class="col-12 text-center mt-3">
                        <a href="../recommended-events.php?filter=upcoming" class="btn btn-outline-primary">See All Upcoming Events</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Past Events Section -->
            <div class="row mt-5">
                <div class="col-12">
                    <h5 class="section-title">Past Events You've Attended</h5>
                </div>

                <?php if (!empty($pastEvents)): ?>
                    <?php foreach ($pastEvents as $event): ?>
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                            <div class="card event-card shadow-sm h-100">
                                <div class="position-relative">
                                    <img src="<?php echo getImagePath($event['image']); ?>"
                                        class="event-image"
                                        alt="<?php echo htmlspecialchars($event['title']); ?>"
                                        style="filter: grayscale(20%);">

                                    <div class="event-date-badge">
                                        <i class="far fa-calendar-check"></i>
                                        <?php echo date('d M', strtotime($event['start_date'])); ?>
                                    </div>

                                    <span class="status-badge past">Past</span>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title mb-1"><?php echo htmlspecialchars($event['title']); ?></h5>
                                    <p class="text-sm text-muted mb-2">By <?php echo htmlspecialchars($event['organizer_name']); ?></p>
                                    <p class="card-text small mb-3">
                                        <?php echo substr(htmlspecialchars($event['description']), 0, 80); ?>...
                                    </p>
                                    <div class="mt-auto d-flex justify-content-between align-items-center">
                                        <span class="badge bg-light text-primary">
                                            <?php echo htmlspecialchars($event['category_name']); ?>
                                        </span>
                                        <div class="btn-group">
                                            <a href="../event-details.php?id=<?php echo $event['id']; ?>" class="btn btn-sm btn-outline-primary">View</a>
                                            <a href="#" class="btn btn-sm btn-outline-secondary">Review</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="empty-state">
                            <div class="mb-3">
                                <i class="far fa-calendar-times fa-3x text-muted"></i>
                            </div>
                            <h5>No past events</h5>
                            <p class="text-muted">You haven't attended any events yet.</p>
                        </div>
                    </div> <?php endif; ?>

                <?php if (!empty($pastEvents)): ?>
                    <div class="col-12 text-center mt-3">
                        <a href="../recommended-events.php?filter=past" class="btn btn-outline-primary">See All Past Events</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Featured Events Section -->
            <div class="row mt-5">
                <div class="col-12">
                    <h5 class="section-title">Featured Events</h5>
                </div>

                <?php if (!empty($featuredEvents)): ?>
                    <?php foreach ($featuredEvents as $event): ?>
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                            <div class="card event-card shadow-sm h-100">
                                <div class="position-relative">
                                    <img src="<?php echo getImagePath($event['image']); ?>"
                                        class="event-image"
                                        alt="<?php echo htmlspecialchars($event['title']); ?>">

                                    <div class="event-date-badge">
                                        <i class="far fa-calendar-alt"></i>
                                        <?php echo date('d M', strtotime($event['start_date'])); ?>
                                    </div>

                                    <div class="like-button">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" />
                                        </svg>
                                    </div>

                                    <span class="status-badge featured">Featured</span>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title mb-1"><?php echo htmlspecialchars($event['title']); ?></h5>
                                    <p class="text-sm text-muted mb-2">By <?php echo htmlspecialchars($event['organizer_name']); ?></p>
                                    <p class="card-text small mb-3">
                                        <?php echo substr(htmlspecialchars($event['description']), 0, 80); ?>...
                                    </p>
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span class="badge bg-light text-primary">
                                                <?php echo htmlspecialchars($event['category_name']); ?>
                                            </span>
                                            <small class="text-muted">
                                                <i class="fas fa-map-marker-alt me-1"></i>
                                                <?php echo htmlspecialchars($event['location'] ?? 'Online'); ?>
                                            </small>
                                        </div>
                                        <a href="../event-details.php?id=<?php echo $event['id']; ?>" class="btn btn-primary btn-sm w-100">View Details</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="empty-state">
                            <div class="mb-3">
                                <i class="far fa-star fa-3x text-muted"></i>
                            </div>
                            <h5>No featured events</h5>
                            <p class="text-muted">There are no featured events at the moment.</p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($featuredEvents)): ?>
                    <div class="col-12 text-center mt-3">
                        <a href="../recommended-events.php" class="btn btn-outline-primary">Explore More Events</a>
                    </div>
                <?php endif; ?>
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