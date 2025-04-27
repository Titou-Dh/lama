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

$selectedCategory =  $_GET['category'] ?? null;

$events = getEvents(
    $cnx,
    [
        'organizer_id' => $userId,
        'category_id' => $selectedCategory
    ]
);
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                        <h1 class="text-white mb-3">Manage Your Events</h1>
                        <p class="text-white opacity-8 mb-4">View, update, or delete your organized events</p>
                        <div class="d-flex gap-3">
                            <a href="create-event.php" class="btn btn-white">Create New Event</a>
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
            </div>

            <div class="row">
                <div class="col-12 mb-4">
                    <h5>Your Organized Events</h5>
                </div>
                <?php if (!empty($events)): ?>
                    <?php foreach ($events as $event): ?>
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                            <div class="card shadow-sm h-100">
                                <img src="<?php echo getImagePath($event['image']); ?>"
                                    class="card-img-top img-fluid" style="height: 200px; object-fit: cover;"
                                    alt="<?php echo htmlspecialchars($event['title']); ?>">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h5>
                                    <p class="card-text text-muted mb-3">
                                        <span class="badge bg-light text-primary">
                                            <?php echo htmlspecialchars($event['category_name']); ?>
                                        </span>
                                    </p>
                                    <p class="card-text small mb-3">
                                        <?php echo substr(htmlspecialchars($event['description']), 0, 100); ?>...
                                    </p>
                                    <div class="mt-auto d-flex flex-wrap justify-content-between align-items-center">
                                        <span class="text-sm text-muted">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            <?php
                                            $eventDate = $event['start_date'] ?? null;
                                            if ($eventDate) {
                                                echo date('M d, Y', strtotime($eventDate));
                                            } else {
                                                echo 'Date not set';
                                            }
                                            ?>
                                        </span>
                                        <div class="btn-group mt-2">
                                            <a href="../event-details.php?id=<?php echo $event['id']; ?>" class="btn btn-sm btn-primary" title="Preview">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="update-event.php?id=<?php echo $event['id']; ?>" class="btn btn-sm btn-warning" title="Update">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <button onclick="deleteEvent(<?php echo $event['id']; ?>)" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info">You have not organized any events yet.</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script src="../../scripts/core/popper.min.js"></script>
    <script src="../../scripts/core/bootstrap.min.js"></script>
    <script src="../../scripts/soft-ui-dashboard.js"></script>
    <script>
        function deleteEvent(eventId) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to recover this event!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, keep it'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/lama/controller/api/delete-event.php?id=${eventId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Event deleted successfully',
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                                window.location.reload();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Failed to delete event',
                                    text: data.error || 'Failed to delete event'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'An error occurred while deleting the event'
                            });
                        });
                }
            });
        }
    </script>
</body>

</html>