<?php
require_once '../../config/database.php';
require_once '../../controller/event.php';
require_once '../../config/session.php';

$query = $_GET['query'] ?? '';
$when = $_GET['when'] ?? '';
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 6 * $page;

$filters = [];

if ($category_id > 0) {
  $filters['category_id'] = $category_id;
}

if (!empty($when)) {
  $today = date('Y-m-d');
  $tomorrow = date('Y-m-d', strtotime('+1 day'));
  $weekend = date('Y-m-d', strtotime('next saturday'));
  $nextWeek = date('Y-m-d', strtotime('+7 days'));

  switch ($when) {
    case 'Today':
      $filters['date_from'] = $today;
      $filters['date_to'] = $today;
      break;
    case 'Tomorrow':
      $filters['date_from'] = $tomorrow;
      $filters['date_to'] = $tomorrow;
      break;
    case 'This weekend':
      $filters['date_from'] = $weekend;
      $filters['date_to'] = date('Y-m-d', strtotime($weekend . ' +1 day'));
      break;
    case 'Next week':
      $filters['date_from'] = $nextWeek;
      $filters['date_to'] = date('Y-m-d', strtotime($nextWeek . ' +6 days'));
      break;
  }
}

$results = [];
if (!empty($query)) {
  $results = searchEvents($cnx, $query, 1, $limit);
} else {
  $results = getEvents($cnx, $filters, 1, $limit);
}

$totalEvents = count($results);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Search Events - LAMA Events Platform</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    /* Footer styles */
    footer {
      width: 100%;
      background-color: #111827;
      /* bg-gray-900 */
      color: white;
    }

    .social-links a {
      display: inline-block;
      margin-right: 1rem;
      color: #9ca3af;
      transition: color 0.3s ease;
    }

    .social-links a:hover {
      color: white;
    }

    .navbar-scrolled {
      backdrop-filter: blur(23px) saturate(171%);
      -webkit-backdrop-filter: blur(23px) saturate(171%);
      background-color: rgba(110, 87, 145, 0.18);
    }

    .btn-gradient {
      background: linear-gradient(to right, #3b82f6, #8b5cf6);
      color: white;
      transition: all 0.3s ease;
    }

    .btn-gradient:hover {
      background: linear-gradient(to right, #2563eb, #7c3aed);
      box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }
  </style>

  <script src="../scripts/tailwind.js"></script>
  <script>
    function debounce(func, wait) {
      let timeout;
      return function(...args) {
        const context = this;
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(context, args), wait);
      };
    }

    document.addEventListener('DOMContentLoaded', function() {
      const searchInputs = document.querySelectorAll('.search-input');
      const searchForm = document.getElementById('searchForm');

      searchInputs.forEach(input => {
        input.addEventListener('input', debounce(function() {
          searchForm.submit();
        }, 700));
        if (input.tagName === 'SELECT') {
          input.addEventListener('change', function() {
            searchForm.submit();
          });
        }
      });
    });
  </script>
</head>

<body class="relative">
  <div class="bg-blue-500/50">
    <?php include_once '../partials/navbar.php'; ?>
  </div>
  <div class="bg-gray-50 md:px-20">
    <!-- Hero Section -->
    <div class="relative w-full h-[400px] overflow-hidden rounded-b-lg">
      <div class="absolute inset-0 bg-black/30 z-10"></div>
      <img
        src="../assets/images/search-img.png"
        alt="Event crowd"
        class="w-full h-full object-cover" />
      <div
        class="absolute inset-0 flex flex-col items-center justify-center z-20 text-white">
        <h1 class="text-3xl md:text-4xl font-bold text-center">
          BECAUSE LIFE IS BETTER
        </h1>
        <h1 class="text-3xl md:text-4xl font-bold text-center">WHEN SHARED </h1>
      </div>
    </div>
    <div class="relative -mt-10 mx-auto w-11/12 md:w-4/5 lg:w-3/4 z-30">
      <div class="bg-indigo-900 rounded-lg p-4 shadow-lg">
        <form id="searchForm" method="GET" action="">
          <div class="flex flex-col md:flex-row items-end gap-2">
            <div class="w-full md:w-1/2 mb-2 md:mb-0">
              <p class="text-white text-xs mb-1">Looking for</p>
              <input
                type="text"
                name="query"
                value="<?php echo htmlspecialchars($query); ?>"
                placeholder="Search for events, venues, or experiences"
                class="w-full p-2 rounded text-sm search-input" />
            </div><?php
                  try {
                    // Get categories from categories table instead of events table
                    $categoryStmt = $cnx->query("SELECT id, name FROM categories ORDER BY name");
                    $categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);
                  } catch (PDOException $e) {
                    error_log("Database error fetching categories: " . $e->getMessage());
                    $categories = [];
                  }
                  ?>
            <div class="w-full md:w-1/3 mb-2 md:mb-0">
              <p class="text-white text-xs mb-1">Category</p>
              <select name="category_id" class="w-full p-2 rounded text-sm search-input">
                <option value="0">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                  <option value="<?php echo $cat['id']; ?>" <?php echo ($category_id == $cat['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat['name']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="w-full md:w-1/2 mb-2 md:mb-0">
              <p class="text-white text-xs mb-1">When</p>
              <select name="when" class="w-full p-2 rounded text-sm search-input">
                <option value="">Choose date and time</option>
                <option value="Today" <?php echo ($when == 'Today') ? 'selected' : ''; ?>>Today</option>
                <option value="Tomorrow" <?php echo ($when == 'Tomorrow') ? 'selected' : ''; ?>>Tomorrow</option>
                <option value="This weekend" <?php echo ($when == 'This weekend') ? 'selected' : ''; ?>>This weekend</option>
                <option value="Next week" <?php echo ($when == 'Next week') ? 'selected' : ''; ?>>Next week</option>
              </select>
            </div>
            <div class="w-full md:w-auto">
              <button
                type="submit"
                class="w-full md:w-auto bg-blue-500 hover:bg-blue-600 text-white p-2 rounded">
                <i class="fas fa-search"></i>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="container mx-auto px-4 py-8">
      <div class="flex flex-col md:flex-row justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">
          Upcoming <span class="text-blue-500">Events</span>
        </h2>
        <div class="flex flex-wrap gap-2 mt-4 md:mt-0">
          <div class="relative">
            <select
              class="bg-white border border-gray-300 rounded-md px-3 py-1 text-sm appearance-none pr-8">
              <option>Newest first</option>
              <option>Oldest first</option>
              <option>Price: low to high</option>
              <option>Price: high to low</option>
            </select>
            <div
              class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
              <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
            </div>
          </div>
          <div class="relative">
            <select
              class="bg-white border border-gray-300 rounded-md px-3 py-1 text-sm appearance-none pr-8">
              <option>Event type</option>
              <option>Conference</option>
              <option>Workshop</option>
              <option>Concert</option>
            </select>
            <div
              class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
              <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
            </div>
          </div>
          <div class="relative">
            <select id="categoryFilter" onchange="filterByCategory(this.value)"
              class="bg-white border border-gray-300 rounded-md px-3 py-1 text-sm appearance-none pr-8">
              <option value="0">Any category</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['id']; ?>" <?php echo ($category_id == $cat['id']) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($cat['name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
            <div
              class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
              <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
            </div>
          </div>
        </div>
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (empty($results)): ?>
          <div class="col-span-3 text-center py-10">
            <h3 class="text-xl text-gray-500">No events found matching your search criteria</h3>
            <?php if (!empty($query)): ?>
              <p class="mt-2 text-gray-400">Try different keywords or browse all events</p>
            <?php endif; ?>
          </div>
        <?php else: ?>
          <?php foreach ($results as $event): ?>
            <div class="bg-white rounded-lg overflow-hidden shadow-md">
              <div class="relative">
                <img
                  src="<?php echo !empty($event['image']) ? $event['image'] : '/placeholder.svg?height=200&width=400'; ?>"
                  alt="<?php echo htmlspecialchars($event['title']); ?>"
                  class="w-full h-48 object-cover" />
                <?php
                $createdAt = isset($event['created_at']) ? strtotime($event['created_at']) : 0;
                $isNew = $createdAt && (time() - $createdAt < 7 * 24 * 3600);
                if ($isNew):
                ?>
                  <div class="absolute top-2 left-2 bg-white text-xs font-semibold px-2 py-1 rounded">
                    NEW
                  </div>
                <?php endif; ?>
              </div>
              <div class="p-4">
                <h3 class="font-bold text-lg mb-1">
                  <a href="../pages/event-details.php?id=<?php echo $event['id']; ?>" class="hover:text-blue-500 transition-colors">
                    <?php echo htmlspecialchars($event['title']); ?>
                  </a>
                </h3>
                <p class="text-xs text-purple-600 mb-2">
                  <?php
                  $startDate = new DateTime($event['start_date']);
                  $doorTime = !empty($event['door_time']) ? $event['door_time'] : '';
                  echo $startDate->format('l, F j') . ($doorTime ? ', ' . $doorTime : '');
                  ?>
                </p>
                <div class="flex items-center text-xs text-gray-500 mb-2">
                  <span class="uppercase font-semibold">
                    <?php echo $event['event_type'] === 'online' ? 'ONLINE EVENT' : 'IN-PERSON EVENT'; ?>
                  </span>
                  <span class="mx-2">•</span>
                  <span><?php echo htmlspecialchars($event['location']); ?></span>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
      <div class="flex justify-center mt-8">
        <?php if (count($results) >= $limit): ?>
          <a
            href="?query=<?php echo urlencode($query); ?>&category_id=<?php echo $category_id; ?>&when=<?php echo urlencode($when); ?>&page=<?php echo $page + 1; ?>"
            class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-6 rounded-md">
            LOAD MORE
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php include_once '../partials/footer.php'; ?>

  <script src="../scripts/landing.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const searchInput = document.querySelector('input[name="query"]');
      if (searchInput) {
        searchInput.focus();
      }
    });

    function filterByCategory(categoryId) {
      // Get current URL and parameters
      const urlParams = new URLSearchParams(window.location.search);

      // Update or set the category_id parameter
      urlParams.set('category_id', categoryId);

      // Reset to page 1 when changing filters
      urlParams.set('page', '1');

      // Redirect with the new parameters
      window.location.href = '?' + urlParams.toString();
    }
  </script>

</body>

</html>