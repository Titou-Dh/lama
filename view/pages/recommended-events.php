<?php
session_start();

include_once __DIR__ . '/../../config/database.php';
include_once __DIR__ . '/../../controller/preferences.php';
include_once __DIR__ . '/../../controller/categories.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/sign-in.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'newest'; // Default sort is newest first

$user_preferences = getUserPreferences($cnx, $user_id);

$recommended_events = getRecommendedEvents($cnx, $user_id, $limit, $offset, $category_filter, $sort_by);

$total_events = countRecommendedEvents($cnx, $user_id, $category_filter);
$total_pages = ceil($total_events / $limit);

$categories = getCategories($cnx);

$base_url = '?';
if ($category_filter) $base_url .= "category={$category_filter}&";
if ($sort_by) $base_url .= "sort={$sort_by}&";
if ($limit != 6) $base_url .= "limit={$limit}&";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Your Recommended Events - EventLama</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="../styles/css/landing2.css" />
    <script src="../scripts/tailwind.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        #mainNav {
            background-color: rgba(37, 99, 235, 0.2);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <?php include '../partials/navbar.php'; ?>

    <main class="py-8 bg-gray-50">
        <div class="container">
            <div class="mb-8 text-center">
                <h1 class="text-3xl font-bold text-gradient mb-2">Your Recommended Events</h1>
                <p class="text-gray-600">Personalized event suggestions based on your preferences</p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-5 mb-8">
                <h2 class="text-xl font-semibold mb-4">Your Preferences</h2>
                <div class="flex flex-wrap gap-2 mb-4">
                    <?php if (!empty($user_preferences)): ?>
                        <?php foreach ($categories as $category): ?>
                            <?php if (in_array($category['id'], $user_preferences)): ?>
                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-gray-500">No preferences selected yet.</p>
                    <?php endif; ?>
                </div>
                <a href="landing-page.php#preferencesSection" class="text-blue-600 text-sm hover:underline">
                    <i class="fas fa-edit me-1"></i> Edit your preferences
                </a>
            </div>

            <!-- Filter Bar -->
            <div class="bg-white rounded-lg shadow-md p-4 mb-4">
                <form method="GET" class="row g-3 align-items-end" id="filterForm">
                    <div class="col-md-4">
                        <label for="categoryFilter" class="form-label text-sm text-gray-600">Category</label>
                        <select class="form-select" id="categoryFilter" name="category" onchange="this.form.submit()">
                            <option value="0" <?php echo $category_filter == 0 ? 'selected' : ''; ?>>All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" <?php echo $category_filter == $category['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="sortFilter" class="form-label text-sm text-gray-600">Sort By</label>
                        <select class="form-select" id="sortFilter" name="sort" onchange="this.form.submit()">
                            <option value="newest" <?php echo $sort_by == 'newest' ? 'selected' : ''; ?>>Newest First</option>
                            <option value="oldest" <?php echo $sort_by == 'oldest' ? 'selected' : ''; ?>>Oldest First</option>
                            <option value="title_asc" <?php echo $sort_by == 'title_asc' ? 'selected' : ''; ?>>Title (A-Z)</option>
                            <option value="title_desc" <?php echo $sort_by == 'title_desc' ? 'selected' : ''; ?>>Title (Z-A)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="limitFilter" class="form-label text-sm text-gray-600">Events Per Page</label>
                        <select class="form-select" id="limitFilter" name="limit" onchange="this.form.submit()">
                            <option value="6" <?php echo $limit == 6 ? 'selected' : ''; ?>>6</option>
                            <option value="12" <?php echo $limit == 12 ? 'selected' : ''; ?>>12</option>
                            <option value="24" <?php echo $limit == 24 ? 'selected' : ''; ?>>24</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <a href="recommended-events.php" class="btn btn-outline-secondary w-100" title="Clear Filters">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                    <input type="hidden" name="page" value="1">
                </form>
            </div>

            <?php if (!empty($recommended_events)): ?>
                <div class="row g-4">
                    <?php foreach ($recommended_events as $event): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="event-card h-100">
                                <div class="event-image" style="background-image: url('<?php echo $event['image'] ?? '../assets/images/default.jpg'; ?>')">
                                    <div class="event-date">
                                        <?php echo date('M d, Y', strtotime($event['start_date'])); ?>
                                    </div>
                                    <div class="event-category">
                                        <?php echo htmlspecialchars($event['category_name'] ?? 'Uncategorized'); ?>
                                    </div>
                                </div>
                                <div class="event-content">
                                    <h3 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                                    <div class="event-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>
                                            <?php echo $event['event_type'] === 'online'
                                                ? 'Online Event'
                                                : htmlspecialchars($event['location']);
                                            ?>
                                        </span>
                                    </div>
                                    <p class="event-description">
                                        <?php echo htmlspecialchars(
                                            strlen($event['description']) > 100
                                                ? substr($event['description'], 0, 100) . '...'
                                                : $event['description']
                                        ); ?>
                                    </p>
                                    <a href="event-details.php?id=<?php echo $event['id']; ?>" class="btn btn-gradient rounded-pill px-4">Get Tickets</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div> <?php if ($total_pages > 1): ?>
                    <nav class="mt-8">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo $base_url; ?>page=<?php echo $page - 1; ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?php echo $base_url; ?>page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo $base_url; ?>page=<?php echo $page + 1; ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>

            <?php else: ?>
                <div class="text-center py-8 bg-white rounded-lg shadow-md">
                    <div class="mb-4">
                        <i class="fas fa-calendar-alt text-4xl text-gray-300"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">No Recommended Events Yet</h3>
                    <p class="text-gray-500 mb-4">
                        We couldn't find any events matching your preferences at the moment.
                        <br>Try updating your preferences or check back later.
                    </p>
                    <div class="flex justify-center gap-3">
                        <a href="landing-page.php#preferencesSection" class="btn btn-outline-primary rounded-pill">Update Preferences</a>
                        <a href="searchpage.php" class="btn btn-gradient rounded-pill">Browse All Events</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include "../partials/footer.php"; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>