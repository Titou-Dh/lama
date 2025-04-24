<?php
include "../../config/database.php";
include "../../controller/event.php";
include "../../controller/user.php";
include "../../config/session.php";

if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
  $eventId = (int) $_GET["id"];
  $event = getEventById($cnx, $eventId);

  if (!$event) {
    header("Location: error.html");
    exit();
  }

  $tickets = getEventTickets($cnx, $eventId);
  $res = $event;
} else {
  header("Location: error.html");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo htmlspecialchars($res['title']); ?> - Lamma</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
  <!-- Custom CSS -->
  <link rel="stylesheet" href="css/styles.css" />
  <!-- AOS Animation Library -->
  <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
  <!-- Configure Tailwind -->
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            blue: {
              light: "#4F46E5",
              DEFAULT: "#3730A3",
              dark: "#312E81",
            },
            purple: {
              light: "#A78BFA",
              DEFAULT: "#8B5CF6",
              dark: "#7C3AED",
            },
            pink: {
              light: "#EC4899",
              DEFAULT: "#DB2777",
              dark: "#BE185D",
            },
          },
          boxShadow: {
            'custom': '0 10px 25px -5px rgba(59, 130, 246, 0.1), 0 8px 10px -6px rgba(59, 130, 246, 0.1)',
          }
        },
      },
    };
  </script>
  <style>
    .gradient-text {
      background: linear-gradient(90deg, #4F46E5, #8B5CF6, #EC4899);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .gradient-bg {
      background: linear-gradient(135deg, #4F46E5, #8B5CF6);
    }

    .ticket-card {
      transition: all 0.3s ease;
    }

    .ticket-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 20px 25px -5px rgba(59, 130, 246, 0.2), 0 10px 10px -5px rgba(59, 130, 246, 0.1);
    }

    .event-image-container {
      position: relative;
      overflow: hidden;
      border-radius: 1rem;
    }

    .event-image-container::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(0deg, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0) 50%);
    }

    .sticky-sidebar {
      position: sticky;
      top: 100px;
    }

    .btn-gradient {
      background: linear-gradient(90deg, #4F46E5, #8B5CF6);
      color: white;
      transition: all 0.3s ease;
    }

    .btn-gradient:hover {
      background: linear-gradient(90deg, #3730A3, #7C3AED);
      transform: translateY(-2px);
      box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3);
    }

    .feature-icon {
      transition: all 0.3s ease;
    }

    .feature-card:hover .feature-icon {
      transform: scale(1.1);
    }

    .countdown-container {
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .share-icon {
      transition: all 0.3s ease;
    }

    .share-icon:hover {
      transform: scale(1.1);
    }

    #mainNav {
      background-color: rgba(37, 99, 235, 0.2);
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
  </style>
  </style>
</head>

<body class="bg-gray-50">

  <?php include '../partials/navbar.php'; ?>

  <!-- Breadcrumb -->
  <div class="bg-gray-50 py-3">
    <div class="container mx-auto px-4">
      <nav class="text-sm" aria-label="Breadcrumb">
        <ol class="flex flex-wrap">
          <li class="flex items-center">
            <a href="index.php" class="text-gray-500 hover:text-blue-500">Home</a>
            <svg class="h-5 w-5 text-gray-400 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </li>
          <li class="flex items-center">
            <a href="events.php" class="text-gray-500 hover:text-blue-500">Events</a>
            <svg class="h-5 w-5 text-gray-400 mx-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </li>
          <li class="text-blue-500"><?php echo htmlspecialchars($res['title']); ?></li>
        </ol>
      </nav>
    </div>
  </div>

  <!-- Main Content -->
  <div class="container mx-auto px-4 py-8">
    <!-- Hero Section -->
    <div class="event-image-container mb-10" data-aos="fade-up">
      <img src="<?php echo htmlspecialchars($res['image']); ?>" alt="<?php echo htmlspecialchars($res['title']); ?>" class="w-full h-[400px] object-cover rounded-xl" />

      <!-- Event Date Overlay -->
      <div class="absolute top-6 left-6 bg-white/90 backdrop-blur-sm rounded-lg p-3 shadow-md">
        <div class="text-center">
          <span class="block text-2xl font-bold text-blue-600"><?php echo date("d", strtotime($res["start_date"])); ?></span>
          <span class="block text-sm font-medium text-gray-600"><?php echo date("M", strtotime($res["start_date"])); ?></span>
          <span class="block text-xs text-gray-500"><?php echo date("Y", strtotime($res["start_date"])); ?></span>
        </div>
      </div>

      <!-- Category Badge -->
      <div class="absolute top-6 right-6">
        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-gradient-to-r from-blue-500 to-purple-500 text-white shadow-md">
          <?php echo htmlspecialchars($res['category_name'] ?? 'Uncategorized'); ?>
        </span>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Main Content Column -->
      <div class="lg:col-span-2 space-y-8">
        <!-- Event Header -->
        <div class="bg-white rounded-xl p-6 shadow-custom" data-aos="fade-up">
          <div class="flex flex-wrap justify-between items-start mb-6">
            <h1 class="text-4xl font-bold gradient-text"><?php echo htmlspecialchars($res['title']); ?></h1>

            <div class="flex space-x-3 mt-2 md:mt-0">
              <button class="flex items-center space-x-2 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-full px-4 py-2 transition-all duration-300" id="shareBtn">
                <i class="fas fa-share-alt"></i>
                <span>Share</span>
              </button>
              <button class="flex items-center space-x-2 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-full px-4 py-2 transition-all duration-300" id="saveBtn">
                <i class="far fa-bookmark"></i>
                <span>Save</span>
              </button>
            </div>
          </div>

          <!-- Event Quick Info -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="flex items-center">
              <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mr-4 feature-icon">
                <i class="fas fa-calendar-day text-blue-500 text-xl"></i>
              </div>
              <div>
                <p class="text-sm text-gray-500">Date & Time</p>
                <p class="font-medium text-gray-800"><?php echo date("d M Y à H:i", strtotime($res["start_date"])); ?></p>
              </div>
            </div>

            <div class="flex items-center">
              <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center mr-4 feature-icon">
                <i class="fas fa-map-marker-alt text-purple-500 text-xl"></i>
              </div>
              <div>
                <p class="text-sm text-gray-500">Location</p>
                <p class="font-medium text-gray-800"><?php echo htmlspecialchars($res["location"]); ?></p>
              </div>
            </div>

            <div class="flex items-center">
              <div class="w-12 h-12 rounded-full bg-pink-100 flex items-center justify-center mr-4 feature-icon">
                <i class="fas fa-user text-pink-500 text-xl"></i>
              </div>
              <div>
                <p class="text-sm text-gray-500">Organizer</p>
                <p class="font-medium text-gray-800"><?php echo htmlspecialchars($res["username"] ?? "not defined"); ?></p>
              </div>
            </div>

            <div class="flex items-center">
              <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center mr-4 feature-icon">
                <i class="fas fa-users text-green-500 text-xl"></i>
              </div>
              <div>
                <p class="text-sm text-gray-500">Capacity</p>
                <p class="font-medium text-gray-800"><?php echo htmlspecialchars($event['capacity']); ?> attendees</p>
              </div>
            </div>
          </div>
        </div>

        <!-- About Section -->
        <div class="bg-white rounded-xl p-6 shadow-custom" data-aos="fade-up" data-aos-delay="100">
          <h2 class="text-2xl font-bold mb-4 flex items-center">
            <span class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
              <i class="fas fa-info-circle text-blue-500"></i>
            </span>
            About This Event
          </h2>

          <div class="prose max-w-none text-gray-600 mb-4 overflow-hidden" id="eventDescription">
            <p><?= nl2br(htmlspecialchars($event['description'])) ?></p>
          </div>

          <button class="text-blue-500 hover:text-blue-700 flex items-center font-medium" id="readMoreBtn">
            <span>Read more</span>
            <i class="fas fa-chevron-down ml-1"></i>
          </button>
        </div>

        <!-- Ticket Section -->
        <div class="bg-white rounded-xl p-6 shadow-custom" data-aos="fade-up" data-aos-delay="200">
          <h2 class="text-2xl font-bold mb-6 flex items-center">
            <span class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center mr-3">
              <i class="fas fa-ticket-alt text-purple-500"></i>
            </span>
            Available Tickets
          </h2>

          <div class="space-y-5">
            <?php foreach ($tickets as $ticket): ?>
              <div class="ticket-card bg-gradient-to-r from-gray-50 to-white rounded-xl p-5 border border-gray-200 shadow-sm relative">
                <?php if (strtolower($ticket['name']) === 'vip access'): ?>
                  <div class="absolute -top-3 -right-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gradient-to-r from-purple-500 to-pink-500 text-white shadow-md">
                      Best Value
                    </span>
                  </div>
                <?php endif; ?>

                <div class="flex flex-wrap justify-between items-center">
                  <div class="mb-3 md:mb-0">
                    <h3 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($ticket['name']); ?></h3>
                    <p class="text-gray-600 mt-1"><?php echo htmlspecialchars($ticket['description']); ?></p>

                    <?php if (!empty($tickets)): ?>
                      <p class="text-sm text-gray-500 mt-2">
                        <i class="far fa-clock mr-1"></i>
                        Sales end on <?php echo date('F j, Y', strtotime($tickets[0]['sales_end'])); ?>
                      </p>
                    <?php endif; ?>
                  </div>

                  <div class="flex flex-col md:items-end space-y-3">
                    <div class="text-2xl font-bold text-blue-600"><?php echo $ticket['price']; ?> dt</div>
                    <a href="checkout.php?ticket_id=<?php echo $ticket['id']; ?>" class="btn btn-gradient rounded-full px-6 py-2 text-white font-medium inline-flex items-center justify-center transition-all duration-300 hover:shadow-lg">
                      <i class="fas fa-ticket-alt mr-2"></i> Get Tickets
                    </a>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <?php if (!empty($tickets)): ?>
            <div class="mt-5 text-sm text-gray-600 bg-blue-50 p-4 rounded-lg border border-blue-100">
              <i class="fas fa-info-circle text-blue-500 mr-2"></i>
              Sales end on <?php echo date('F j, Y \a\t g:i A', strtotime($tickets[0]['sales_end'])); ?> or when sold out.
              <a href="#" class="text-blue-500 hover:underline">See refund policy</a>
            </div>
          <?php endif; ?>
        </div>

        <!-- Organizer Info -->
        <div class="bg-white rounded-xl p-6 shadow-custom" data-aos="fade-up" data-aos-delay="300">
          <h2 class="text-2xl font-bold mb-4 flex items-center">
            <span class="w-8 h-8 rounded-full bg-pink-100 flex items-center justify-center mr-3">
              <i class="fas fa-user-tie text-pink-500"></i>
            </span>
            Organizer
          </h2>

          <div class="flex items-start">
            <div class="flex-shrink-0">
              <img src="<?= $event['profile_image'] ?>" alt="<?= $event['username'] ?>" class="w-20 h-20 rounded-xl object-cover border-4 border-white shadow-md" />
            </div>
            <div class="ml-5">
              <h3 class="text-xl font-bold"><?= htmlspecialchars($event['username']) ?></h3>
              <p class="text-gray-600 mb-3">Event organizer</p>

            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar Column -->
      <div class="lg:col-span-1">
        <div class="sticky-sidebar space-y-6">
          <!-- Countdown Timer -->
          <div class="countdown-container bg-white rounded-xl p-6 shadow-custom" data-aos="fade-left">
            <h3 class="text-lg font-bold mb-3 text-center">Event Starts In</h3>
            <div class="grid grid-cols-4 gap-2 text-center">
              <div class="bg-blue-50 rounded-lg p-2">
                <span class="block text-2xl font-bold text-blue-600" id="days">00</span>
                <span class="text-xs text-gray-500">Days</span>
              </div>
              <div class="bg-blue-50 rounded-lg p-2">
                <span class="block text-2xl font-bold text-blue-600" id="hours">00</span>
                <span class="text-xs text-gray-500">Hours</span>
              </div>
              <div class="bg-blue-50 rounded-lg p-2">
                <span class="block text-2xl font-bold text-blue-600" id="minutes">00</span>
                <span class="text-xs text-gray-500">Mins</span>
              </div>
              <div class="bg-blue-50 rounded-lg p-2">
                <span class="block text-2xl font-bold text-blue-600" id="seconds">00</span>
                <span class="text-xs text-gray-500">Secs</span>
              </div>
            </div>
          </div>

          <!-- Registration Card -->
          <div class="bg-white rounded-xl overflow-hidden shadow-custom" data-aos="fade-left" data-aos-delay="100">
            <div class="gradient-bg p-6 text-white">
              <h3 class="text-xl font-bold mb-2">Register for This Event</h3>
              <p class="text-white/80 text-sm">Secure your spot before tickets sell out!</p>
            </div>

            <div class="p-6 space-y-5">
              <div class="flex justify-between items-center pb-4 border-b border-gray-100">
                <span class="text-gray-600">Starting from</span>
                <span class="text-2xl font-bold text-blue-600">
                  <?php
                  if (!empty($tickets)) {
                    $prices = array_column($tickets, 'price');
                    echo min($prices) . ' dt';
                  } else {
                    echo 'Free';
                  }
                  ?>
                </span>
              </div>

              <a href="checkout.php?event_id=<?php echo $event['id']; ?>">
                <button class="btn-gradient w-full py-3 rounded-xl font-medium flex items-center justify-center">
                  <i class="fas fa-ticket-alt mr-2"></i> Register Now
                </button>
              </a>

            </div>
          </div>

          <!-- Event Details Card -->
          <div class="bg-white rounded-xl p-6 shadow-custom" data-aos="fade-left" data-aos-delay="200">
            <h3 class="text-lg font-bold mb-4 flex items-center">
              <i class="fas fa-info-circle text-blue-500 mr-2"></i>
              Event Details
            </h3>

            <div class="space-y-4">
              <!-- Capacity -->
              <div class="flex items-center feature-card p-3 rounded-lg hover:bg-gray-50 transition-colors duration-300">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3 feature-icon">
                  <i class="fas fa-users text-blue-500"></i>
                </div>
                <div>
                  <p class="text-sm text-gray-500">Capacity</p>
                  <p class="font-medium"><?php echo htmlspecialchars($event['capacity']); ?> attendees</p>
                </div>
              </div>

              <!-- Location -->
              <div class="flex items-center feature-card p-3 rounded-lg hover:bg-gray-50 transition-colors duration-300">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3 feature-icon">
                  <i class="fas fa-map-marker-alt text-green-500"></i>
                </div>
                <div>
                  <p class="text-sm text-gray-500">Location</p>
                  <p class="font-medium"><?php echo htmlspecialchars($event['location']); ?></p>
                </div>
              </div>

              <!-- Category -->
              <div class="flex items-center feature-card p-3 rounded-lg hover:bg-gray-50 transition-colors duration-300">
                <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center mr-3 feature-icon">
                  <i class="fas fa-layer-group text-yellow-500"></i>
                </div>
                <div>
                  <p class="text-sm text-gray-500">Category</p>
                  <p class="font-medium"><?php echo htmlspecialchars($event['category_name'] ?? 'Uncategorized'); ?></p>
                </div>
              </div>

              <!-- Event Type -->
              <div class="flex items-center feature-card p-3 rounded-lg hover:bg-gray-50 transition-colors duration-300">
                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3 feature-icon">
                  <i class="fas fa-globe text-purple-500"></i>
                </div>
                <div>
                  <p class="text-sm text-gray-500">Event Type</p>
                  <p class="font-medium"><?php echo ucfirst($event['event_type'] ?? 'In-person'); ?></p>
                </div>
              </div>
            </div>
          </div>

          <!-- Share Card -->
          <div class="bg-white rounded-xl p-6 shadow-custom" data-aos="fade-left" data-aos-delay="300">
            <h3 class="text-lg font-bold mb-4 flex items-center">
              <i class="fas fa-share-alt text-blue-500 mr-2"></i>
              Share This Event
            </h3>

            <div class="flex justify-between items-center">
              <a href="#" class="share-icon w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center">
                <i class="fab fa-facebook-f"></i>
              </a>
              <a href="#" class="share-icon w-10 h-10 bg-blue-400 text-white rounded-full flex items-center justify-center">
                <i class="fab fa-twitter"></i>
              </a>
              <a href="#" class="share-icon w-10 h-10 bg-green-500 text-white rounded-full flex items-center justify-center">
                <i class="fab fa-whatsapp"></i>
              </a>
              <a href="#" class="share-icon w-10 h-10 bg-blue-700 text-white rounded-full flex items-center justify-center">
                <i class="fab fa-linkedin-in"></i>
              </a>
              <a href="#" class="share-icon w-10 h-10 bg-red-500 text-white rounded-full flex items-center justify-center">
                <i class="fas fa-envelope"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Share Modal -->
  <div class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm" id="shareModal">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4 shadow-xl">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold">Share This Event</h3>
        <button class="text-gray-500 hover:text-gray-700" id="closeShareModal">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <div class="space-y-6">
        <div class="flex justify-center space-x-6">
          <a href="#" class="flex flex-col items-center">
            <div class="w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700 transition-colors duration-300">
              <i class="fab fa-facebook-f text-xl"></i>
            </div>
            <span class="text-sm mt-1">Facebook</span>
          </a>
          <a href="#" class="flex flex-col items-center">
            <div class="w-12 h-12 bg-blue-400 text-white rounded-full flex items-center justify-center hover:bg-blue-500 transition-colors duration-300">
              <i class="fab fa-twitter text-xl"></i>
            </div>
            <span class="text-sm mt-1">Twitter</span>
          </a>
          <a href="#" class="flex flex-col items-center">
            <div class="w-12 h-12 bg-green-500 text-white rounded-full flex items-center justify-center hover:bg-green-600 transition-colors duration-300">
              <i class="fab fa-whatsapp text-xl"></i>
            </div>
            <span class="text-sm mt-1">WhatsApp</span>
          </a>
          <a href="#" class="flex flex-col items-center">
            <div class="w-12 h-12 bg-blue-700 text-white rounded-full flex items-center justify-center hover:bg-blue-800 transition-colors duration-300">
              <i class="fab fa-linkedin-in text-xl"></i>
            </div>
            <span class="text-sm mt-1">LinkedIn</span>
          </a>
        </div>

        <div>
          <label class="text-sm text-gray-600 mb-1 block">Copy Link</label>
          <div class="flex">
            <input type="text" value="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>" class="form-control rounded-r-none" readonly />
            <button class="btn btn-outline-primary rounded-l-none">
              Copy
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="bg-gray-900 text-white py-12 mt-16">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 mb-5 mb-lg-0">
          <div class="d-flex align-items-center mb-4">
            <img src="../assets/images/logo.png" alt="EventLama" style="height: 60px" />
          </div>
          <p class="text-gray-400 mb-4">
            Discover and create events that matter to you.
          </p>
          <div class="social-links">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-linkedin-in"></i></a>
          </div>
        </div>

        <div class="col-lg-2 col-md-4 mb-5 mb-md-0">
          <h5 class="text-xl font-semibold mb-4">Company</h5>
          <ul class="list-unstyled">
            <li class="mb-2">
              <a href="#" class="text-gray-400 hover:text-white">About Us</a>
            </li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-4 mb-5 mb-md-0">
          <h5 class="text-xl font-semibold mb-4">Support</h5>
          <ul class="list-unstyled">
            <li class="mb-2">
              <a href="#" class="text-gray-400 hover:text-white">Help Center</a>
            </li>
            <li class="mb-2">
              <a href="#" class="text-gray-400 hover:text-white">Contact Us</a>
            </li>
            <li class="mb-2">
              <a href="#" class="text-gray-400 hover:text-white">Safety Center</a>
            </li>
            <li>
              <a href="#" class="text-gray-400 hover:text-white">Community Guidelines</a>
            </li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-4 mb-5 mb-md-0">
          <h5 class="text-xl font-semibold mb-4">Legal</h5>
          <ul class="list-unstyled">
            <li class="mb-2">
              <a href="#" class="text-gray-400 hover:text-white">Terms of Service</a>
            </li>
            <li class="mb-2">
              <a href="#" class="text-gray-400 hover:text-white">Privacy Policy</a>
            </li>
            <li class="mb-2">
              <a href="#" class="text-gray-400 hover:text-white">Cookie Policy</a>
            </li>
            <li>
              <a href="#" class="text-gray-400 hover:text-white">Intellectual Property</a>
            </li>
          </ul>
        </div>
      </div>
      <hr class="my-5 border-gray-700" />
      <div class="row align-items-center">
        <div class="col-md-6 text-center text-md-start">
          <p class="text-gray-400 mb-0">
            &copy; 2025 EventLama. All rights reserved.
          </p>
        </div>
      </div>
    </div>
  </footer>

  <!-- Bootstrap JS Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <!-- AOS Animation Library -->
  <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
  <!-- Custom JavaScript -->
  <script>
    // Initialize AOS
    AOS.init({
      duration: 800,
      once: true
    });

    // User menu toggle
    const userMenuButton = document.getElementById('userMenuButton');
    const userMenu = document.getElementById('userMenu');

    if (userMenuButton && userMenu) {
      userMenuButton.addEventListener('click', () => {
        userMenu.classList.toggle('hidden');
      });

      document.addEventListener('click', (e) => {
        if (!userMenuButton.contains(e.target) && !userMenu.contains(e.target)) {
          userMenu.classList.add('hidden');
        }
      });
    }

    // Mobile menu toggle
    const mobileMenuButton = document.getElementById('mobileMenuButton');
    const mobileMenu = document.getElementById('mobileMenu');

    if (mobileMenuButton && mobileMenu) {
      mobileMenuButton.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
      });
    }

    // Share modal functionality
    const shareBtn = document.getElementById('shareBtn');
    const shareModal = document.getElementById('shareModal');
    const closeShareModal = document.getElementById('closeShareModal');

    if (shareBtn && shareModal && closeShareModal) {
      shareBtn.addEventListener('click', () => {
        shareModal.classList.remove('hidden');
      });

      closeShareModal.addEventListener('click', () => {
        shareModal.classList.add('hidden');
      });

      // Close modal when clicking outside
      shareModal.addEventListener('click', (e) => {
        if (e.target === shareModal) {
          shareModal.classList.add('hidden');
        }
      });
    }

    // Save/bookmark functionality
    const saveBtn = document.getElementById('saveBtn');
    if (saveBtn) {
      saveBtn.addEventListener('click', function() {
        const icon = this.querySelector('i');
        if (icon.classList.contains('far')) {
          icon.classList.remove('far');
          icon.classList.add('fas');
          this.querySelector('span').textContent = 'Saved';
        } else {
          icon.classList.remove('fas');
          icon.classList.add('far');
          this.querySelector('span').textContent = 'Save';
        }
      });
    }

    // Read more functionality
    const readMoreBtn = document.getElementById('readMoreBtn');
    if (readMoreBtn) {
      const eventDescription = document.getElementById('eventDescription');
      let isExpanded = false;

      // Initially limit the height
      eventDescription.style.maxHeight = '150px';

      readMoreBtn.addEventListener('click', () => {
        if (!isExpanded) {
          // Expand
          eventDescription.style.maxHeight = 'none';
          readMoreBtn.querySelector('span').textContent = 'Read less';
          readMoreBtn.querySelector('i').classList.remove('fa-chevron-down');
          readMoreBtn.querySelector('i').classList.add('fa-chevron-up');
        } else {
          // Collapse
          eventDescription.style.maxHeight = '150px';
          readMoreBtn.querySelector('span').textContent = 'Read more';
          readMoreBtn.querySelector('i').classList.remove('fa-chevron-up');
          readMoreBtn.querySelector('i').classList.add('fa-chevron-down');
        }
        isExpanded = !isExpanded;
      });
    }

    // Countdown timer
    function updateCountdown() {
      const eventDate = new Date('<?php echo $res["start_date"]; ?>').getTime();
      const now = new Date().getTime();
      const distance = eventDate - now;

      // Time calculations
      const days = Math.floor(distance / (1000 * 60 * 60 * 24));
      const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      const seconds = Math.floor((distance % (1000 * 60)) / 1000);

      // Display the result
      document.getElementById('days').textContent = days.toString().padStart(2, '0');
      document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
      document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
      document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');

      // If the countdown is over
      if (distance < 0) {
        clearInterval(countdownTimer);
        document.getElementById('days').textContent = '00';
        document.getElementById('hours').textContent = '00';
        document.getElementById('minutes').textContent = '00';
        document.getElementById('seconds').textContent = '00';
      }
    }

    // Update countdown every second
    updateCountdown();
    const countdownTimer = setInterval(updateCountdown, 1000);
  </script>
</body>

</html>