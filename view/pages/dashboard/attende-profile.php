<!DOCTYPE html>
<?php
include "../../../config/session.php";
checkSession();

include_once "../../../config/database.php";
include_once "../../../controller/user.php";
include_once "../../../controller/event.php";


$userId = $_SESSION['user_id'];

$userData = getUserById($cnx, $userId);

// Get events attended by the user
$upcomingEvents = getEvents($cnx, [
    'attendee_id' => $userId,
    'date_from' => date('Y-m-d'),
    'status' => 'published'
]);

$pastEvents = getEvents($cnx, [
    'attendee_id' => $userId,
    'date_to' => date('Y-m-d', strtotime('-1 day')),
    'status' => 'published'
]);

$defaultAvatar = "https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_1280.png";
$profileImage = !empty($userData['profile_image']) ? $userData['profile_image'] : $defaultAvatar;
?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
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
    <link rel="stylesheet" href="../../styles/css/usersprofiles.css" />
    <link rel="icon" type="image/png" href="../../assets/images/logo.png" />
</head>

<body class="g-sidenav-show bg-gray-100">
    <?php include '../../partials/dashboar-sidebar.php' ?>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <?php include '../../partials/dashboard-navbar.php' ?>
        <div class="content-wrapper">
            <div class="container-fluid">
                <!-- Profile Header -->
                <div class="profile-header">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center text-md-start mb-4 mb-md-0">
                            <img
                                src="<?php echo $profileImage; ?>"
                                alt="User avatar"
                                class="profile-avatar" />
                        </div>
                        <div class="col-md-8">
                            <h2 class="mb-1"><?php echo htmlspecialchars($userData['full_name']); ?></h2>
                            <p class="text-muted mb-2">
                                <i class="fas fa-user me-2"></i>@<?php echo htmlspecialchars($userData['username']); ?>
                            </p>
                            <p class="text-muted mb-2">
                                <i class="fas fa-briefcase me-2"></i><?php echo $userData['is_organizer'] ? 'Event Organizer' : 'Attendee'; ?>
                            </p>
                        </div>
                    </div>
                    <div class="profile-stats">
                    <div class="profile-stat-item">
                        <div class="number"><?php echo count($pastEvents) + count($upcomingEvents); ?></div>
                        <div class="label">Events Attended</div>
                    </div>
                    <div class="profile-stat-item">
                        <div class="number"><?php echo count($upcomingEvents); ?></div>
                        <div class="label">Upcoming Events</div>
                    </div>
                </div>
                </div>

                
            </div>

            <!-- Profile Content -->
            <div class="row">
                <!-- Left Column -->
                <div class="col-lg-4">
                    <!-- Professional Information -->
                    <div class="info-card">
                        <h3 class="section-title">Professional Information</h3>
                        <form id="profile-form" method="post" action="../../../controllers/update-profile.php">
                            <div class="info-item">
                                <div class="info-label">Full Name</div>
                                <p><?php echo htmlspecialchars($userData['full_name']); ?></p>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Username</div>
                                <p><?php echo htmlspecialchars($userData['username']); ?></p>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Email</div>
                                <p><?php echo htmlspecialchars($userData['email']); ?></p>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Member Since</div>
                                <p><?php echo date('F j, Y', strtotime($userData['created_at'])); ?></p>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Account Type</div>
                                <p><?php echo $userData['is_organizer'] ? 'Organizer' : 'Attendee'; ?></p>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Right Column -->
                <div class="col-lg-8">
                    <!-- Profile Tabs -->
                    <div class="profile-tabs">
                        <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button
                                    class="nav-link active"
                                    id="upcoming-tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#upcoming"
                                    type="button"
                                    role="tab">
                                    Upcoming Events
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button
                                    class="nav-link"
                                    id="past-tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#past"
                                    type="button"
                                    role="tab">
                                    Past Events
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content" id="profileTabsContent">
                            <!-- Upcoming Events Tab -->
                            <div
                                class="tab-pane fade show active"
                                id="upcoming"
                                role="tabpanel"
                                aria-labelledby="upcoming-tab">
                                <h3 class="section-title">Your Upcoming Events</h3>
                                <div class="row row-cols-1 row-cols-md-2 g-4">
                                    <?php if (count($upcomingEvents) > 0): ?>
                                        <?php foreach ($upcomingEvents as $event): ?>
                                            <div class="col">
                                                <div class="card h-100 border-0 shadow-sm">
                                                    <img
                                                        src="<?php echo !empty($event['image']) ? $event['image'] : 'https://www.de-ctr.org/wp-content/uploads/2019/08/tech-talk-1024x512.jpg'; ?>"
                                                        class="card-img-top"
                                                        alt="<?php echo htmlspecialchars($event['title']); ?>" />
                                                    <div class="card-body">
                                                        <h5 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h5>
                                                        <p class="card-text text-muted small mb-3">
                                                            <?php echo htmlspecialchars(substr($event['description'], 0, 100)) . '...'; ?>
                                                        </p>
                                                        <div
                                                            class="d-flex align-items-center text-muted small mb-2">
                                                            <i class="fas fa-calendar me-2"></i>
                                                            <span><?php echo date('F j, Y, g:i A', strtotime($event['start_date'])); ?></span>
                                                        </div>
                                                        <div
                                                            class="d-flex align-items-center text-muted small mb-2">
                                                            <i class="fas fa-map-marker-alt me-2"></i>
                                                            <span><?php echo htmlspecialchars($event['location']); ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="card-footer bg-white border-0 pt-0">
                                                        <a href="../../event-details.php?id=<?php echo $event['id']; ?>" class="btn btn-sm btn-outline-primary w-100">
                                                            View
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="col-12">
                                            <p class="text-center">You don't have any upcoming events.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <!-- Past Events Tab -->
                            <div
                                class="tab-pane fade"
                                id="past"
                                role="tabpanel"
                                aria-labelledby="past-tab">
                                <h3 class="section-title">Your Past Events</h3>
                                <div class="row row-cols-1 row-cols-md-2 g-4">
                                    <?php if (count($pastEvents) > 0): ?>
                                        <?php foreach ($pastEvents as $event): ?>
                                            <div class="col">
                                                <div class="card h-100 border-0 shadow-sm">
                                                    <img
                                                        src="<?php echo !empty($event['image']) ? $event['image'] : 'https://www.bostontechmom.com/wp-content/uploads/2022/11/hackathon-tech-banner.jpg'; ?>"
                                                        class="card-img-top"
                                                        alt="<?php echo htmlspecialchars($event['title']); ?>" />
                                                    <div class="card-body">
                                                        <h5 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h5>
                                                        <p class="card-text text-muted small mb-3">
                                                            <?php echo htmlspecialchars(substr($event['description'], 0, 100)) . '...'; ?>
                                                        </p>
                                                        <div
                                                            class="d-flex align-items-center text-muted small mb-2">
                                                            <i class="fas fa-calendar me-2"></i>
                                                            <span><?php echo date('F j, Y', strtotime($event['start_date'])); ?></span>
                                                        </div>
                                                        <div
                                                            class="d-flex align-items-center text-muted small mb-2">
                                                            <i class="fas fa-map-marker-alt me-2"></i>
                                                            <span><?php echo htmlspecialchars($event['location']); ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="card-footer bg-white border-0 pt-0">
                                                        <a href="../../event-details.php?id=<?php echo $event['id']; ?>" class="btn btn-sm btn-outline-primary w-100">
                                                            View
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="col-12">
                                            <p class="text-center">You don't have any past events.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="../../scripts/core/popper.min.js"></script>
    <script src="../../scripts/core/bootstrap.min.js"></script>
    <script src="../../scripts/soft-ui-dashboard.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>