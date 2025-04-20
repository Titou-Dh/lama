<?php

include "../../config/database.php";
include "../../controller/event.php";

if (isset($_GET["id"])) {
  $ID = $_GET["id"];
  $res = getEventById($cnx, $ID);
  if (!$res) {
    header("Location: error.html");
    exit();
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Event Details - Lamma</title>
  <!-- Bootstrap CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
    rel="stylesheet" />
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Font Awesome -->
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
  <!-- Custom CSS -->
  <link rel="stylesheet" href="css/styles.css" />
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
          },
        },
      },
    };
  </script>
</head>

<body>
  <!-- Header/Navigation -->
  <header
    class="sticky top-0 z-50 w-full border-b bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
    <div class="container mx-auto px-4">
      <nav class="flex items-center justify-between py-4">
        <a href="index.html" class="flex items-center space-x-2">
          <img
            src="../assets/images/logo.png"
            alt="Lamma Logo"
            class="h-8 w-8" />
        </a>
        <div class="hidden md:flex items-center space-x-6">
          <a
            href="dashboard.html"
            class="flex items-center text-sm font-medium text-gray-500 hover:text-blue-500">
            <i class="fas fa-home mr-2"></i>
            <span>Dashboard</span>
          </a>
          <a
            href="events.html"
            class="flex items-center text-sm font-medium text-blue-500">
            <i class="fas fa-calendar mr-2"></i>
            <span>Events</span>
          </a>
          <a
            href="messages.html"
            class="flex items-center text-sm font-medium text-gray-500 hover:text-blue-500">
            <i class="fas fa-comment mr-2"></i>
            <span>Messages</span>
          </a>
          <a
            href="profile.html"
            class="flex items-center text-sm font-medium text-gray-500 hover:text-blue-500">
            <i class="fas fa-user mr-2"></i>
            <span>Profile</span>
          </a>
          <a
            href="create-event.html"
            class="flex items-center text-sm font-medium text-gray-500 hover:text-blue-500">
            <i class="fas fa-plus-circle mr-2"></i>
            <span>Create Event</span>
          </a>
        </div>
        <div class="flex items-center space-x-4">
          <div class="relative">
            <button
              class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center"
              id="userMenuButton">
              <img
                src="../assets/images/aziz.jpg"
                alt="User"
                class="h-8 w-8 rounded-full" />
            </button>
            <div
              class="absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 hidden"
              id="userMenu">
              <div class="py-1">
                <div class="px-4 py-2 text-sm text-gray-700">
                  <p class="font-medium">Username</p>
                  <p class="text-xs text-gray-500">user@example.com</p>
                </div>
                <hr />
                <a
                  href="profile.html"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                <a
                  href="settings.html"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Settings</a>
                <hr />
                <a
                  href="#"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Log out</a>
              </div>
            </div>
          </div>
          <button class="md:hidden" id="mobileMenuButton">
            <i class="fas fa-bars text-gray-500"></i>
          </button>
        </div>
      </nav>
      <!-- Mobile menu -->
      <div class="md:hidden hidden" id="mobileMenu">
        <div class="px-2 pt-2 pb-3 space-y-1">
          <a
            href="dashboard.html"
            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">
            <i class="fas fa-home mr-2"></i>
            Dashboard
          </a>
          <a
            href="events.html"
            class="block px-3 py-2 rounded-md text-base font-medium text-blue-500 hover:bg-gray-100">
            <i class="fas fa-calendar mr-2"></i>
            Events
          </a>
          <a
            href="messages.html"
            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">
            <i class="fas fa-comment mr-2"></i>
            Messages
          </a>
          <a
            href="profile.html"
            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">
            <i class="fas fa-user mr-2"></i>
            Profile
          </a>
          <a
            href="create-event.html"
            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-100">
            <i class="fas fa-plus-circle mr-2"></i>
            Create Event
          </a>
        </div>
      </div>
    </div>
  </header>

  <!-- Breadcrumb -->
  <div class="bg-gray-50 py-3">
    <div class="container mx-auto px-4">
      <nav class="text-sm" aria-label="Breadcrumb">
        <ol class="flex flex-wrap">
          <li class="flex items-center">
            <a href="index.html" class="text-gray-500 hover:text-blue-500">Home</a>
            <svg
              class="h-5 w-5 text-gray-400 mx-1"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 5l7 7-7 7"></path>
            </svg>
          </li>
          <li class="flex items-center">
            <a href="events.html" class="text-gray-500 hover:text-blue-500">Events</a>
            <svg
              class="h-5 w-5 text-gray-400 mx-1"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24">
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 5l7 7-7 7"></path>
            </svg>
          </li>
          <li class="text-blue-500">Tech Conference 2025</li>
        </ol>
      </nav>
    </div>
  </div>

  <div class="container mx-auto px-4 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <div class="lg:col-span-2 space-y-8">
        <!-- Event Header -->
        <div>
          <div
            class="relative w-full h-64 md:h-96 rounded-lg overflow-hidden">
            <img
              src="../assets/images/tech.jpg"
              alt="Tech Conference"
              class="w-full h-full object-cover" />
            <div
              class="absolute top-4 right-4 bg-blue-500 text-white text-xs font-bold px-3 py-1 rounded-full">
              Technology
            </div>
          </div>

          <div class="mt-6">
            <div class="flex flex-wrap justify-between items-start">
              <h1 class="text-3xl font-bold mb-2"><?php echo $res['title']; ?></h1>
              <div class="flex space-x-2 mb-4">
                <button
                  class="flex items-center space-x-1 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1 text-sm"
                  id="shareBtn">
                  <i class="fas fa-share-alt"></i>
                  <span>Share</span>
                </button>
                <button
                  class="flex items-center space-x-1 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-full px-3 py-1 text-sm"
                  id="saveBtn">
                  <i class="far fa-bookmark"></i>
                  <span>Save</span>
                </button>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
              <div class="flex items-center text-gray-600">
                <i
                  class="fas fa-calendar-day text-blue-500 mr-2 w-5 text-center"></i>
                <div>
                  <p class="font-medium">Date & Time</p>
                  <p>
                    <?php
                    echo $res["start_date"];
                    ?>
                  </p>
                </div>
              </div>
              <div class="flex items-center text-gray-600">
                <i
                  class="fas fa-map-marker-alt text-blue-500 mr-2 w-5 text-center"></i>
                <div>
                  <p class="font-medium">Location</p>
                  <p>
                    <?php
                    echo $res["location"];
                    ?>
                  </p>
                </div>
              </div>
              <div class="flex items-center text-gray-600">
                <i class="fas fa-user text-blue-500 mr-2 w-5 text-center"></i>
                <div>
                  <p class="font-medium">Organizer</p>
                  <a href="organizer-profile.html" class="hover:text-blue-500">Tech Events Inc.</a>
                </div>
              </div>
              <div class="flex items-center text-gray-600">
                <i
                  class="fas fa-users text-blue-500 mr-2 w-5 text-center"></i>
                <div>
                  <p class="font-medium">Attendees</p>
                  <p>245 going • 89 interested</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Ticket Section -->
        <div
          class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg p-6 border border-blue-100">
          <h2 class="text-xl font-bold mb-4">Tickets</h2>

          <div class="space-y-4">
            <!-- Ticket Option 1 -->
            <div
              class="bg-white rounded-lg p-4 shadow-sm flex flex-wrap justify-between items-center">
              <div class="mb-2 md:mb-0">
                <h3 class="font-bold">General Admission</h3>
                <p class="text-sm text-gray-500">
                  Access to all conference areas and sessions
                </p>
              </div>
              <div
                class="flex flex-col md:flex-row md:items-center space-y-2 md:space-y-0 md:space-x-4">
                <div class="text-xl font-bold text-blue-500">99 dt</div>
                <a href="checkout.html" class="btn btn-primary">
                  <i class="fas fa-ticket-alt mr-2"></i> Get Tickets
                </a>
              </div>
            </div>

            <!-- Ticket Option 2 -->
            <div
              class="bg-white rounded-lg p-4 shadow-sm flex flex-wrap justify-between items-center relative">
              <div
                class="absolute -top-2 -right-2 bg-purple-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                Best Value
              </div>
              <div class="mb-2 md:mb-0">
                <h3 class="font-bold">VIP Access</h3>
                <p class="text-sm text-gray-500">
                  Premium seating, exclusive networking event, and swag bag
                </p>
              </div>
              <div
                class="flex flex-col md:flex-row md:items-center space-y-2 md:space-y-0 md:space-x-4">
                <div class="text-xl font-bold text-blue-500">150 dt</div>
                <a href="checkout.html" class="btn btn-primary">
                  <i class="fas fa-ticket-alt mr-2"></i> Get Tickets
                </a>
              </div>
            </div>

            <!-- Ticket Option 3 -->
            <div
              class="bg-white rounded-lg p-4 shadow-sm flex flex-wrap justify-between items-center">
              <div class="mb-2 md:mb-0">
                <h3 class="font-bold">Workshop Pass</h3>
                <p class="text-sm text-gray-500">
                  Access to all workshops and hands-on sessions
                </p>
              </div>
              <div
                class="flex flex-col md:flex-row md:items-center space-y-2 md:space-y-0 md:space-x-4">
                <div class="text-xl font-bold text-blue-500">45 dt</div>
                <a href="checkout.html" class="btn btn-primary">
                  <i class="fas fa-ticket-alt mr-2"></i> Get Tickets
                </a>
              </div>
            </div>
          </div>

          <div class="mt-4 text-sm text-gray-600">
            <i class="fas fa-info-circle text-blue-500 mr-1"></i>
            Sales end on May 1, 2025 at 11:59 PM or when sold out.
            <a href="#" class="text-blue-500 hover:underline">See refund policy</a>
          </div>
        </div>

        <!-- About Section -->
        <div class="space-y-6">
          <div>
            <h2 class="text-xl font-bold mb-4">About This Event</h2>
            <div class="prose max-w-none text-gray-600">
              <p>
                Join us for the biggest tech conference of the year featuring
                keynotes from industry leaders, workshops, and networking
                opportunities. This three-day event will cover the latest
                trends in AI, blockchain, cloud computing, and more.
              </p>

              <p>
                There will be opportunities to connect with potential
                employers, investors, and collaborators. Whether you're a
                seasoned professional or just starting your tech journey, Tech
                Conference 2025 has something valuable for everyone.
              </p>

              <h3 class="text-lg font-bold mt-4 mb-2">What to expect:</h3>
              <ul class="list-disc pl-5 space-y-1">
                <li>Keynote speeches from industry experts</li>
                <li>Panel discussions on trending topics</li>
                <li>Hands-on workshops</li>
                <li>Networking sessions</li>
                <li>Product demos and exhibitions</li>
                <li>Career opportunities</li>
              </ul>

              <h3 class="text-lg font-bold mt-4 mb-2">Key Speakers:</h3>
              <ul class="list-disc pl-5 space-y-1">
                <li>Jane Smith - CEO of Tech Innovations</li>
                <li>John Doe - Lead AI Researcher at Future Labs</li>
                <li>Sarah Johnson - CTO of Global Solutions</li>
                <li>Mike Wilson - Blockchain Expert</li>
                <li>And many more industry leaders!</li>
              </ul>
            </div>

            <div class="mt-6">
              <button
                class="text-blue-500 hover:text-blue-700 flex items-center font-medium"
                id="readMoreBtn">
                <span>Read more</span>
                <i class="fas fa-chevron-down ml-1"></i>
              </button>
            </div>
          </div>

          <!-- Schedule -->
          <div>
            <h2 class="text-xl font-bold mb-4">Schedule</h2>

            <div class="space-y-6">
              <!-- Day 1 -->
              <div>
                <h3 class="text-lg font-bold mb-2">Day 1 - May 15</h3>
                <div class="space-y-4">
                  <div class="bg-white rounded-lg p-4 shadow-sm">
                    <div class="flex flex-wrap justify-between mb-2">
                      <span class="font-medium">Opening Keynote</span>
                      <span class="text-gray-600">9:00 AM - 10:30 AM</span>
                    </div>
                    <p class="text-sm text-gray-600">
                      Jane Smith, CEO of Tech Innovations
                    </p>
                  </div>

                  <div class="bg-white rounded-lg p-4 shadow-sm">
                    <div class="flex flex-wrap justify-between mb-2">
                      <span class="font-medium">The Future of AI</span>
                      <span class="text-gray-600">11:00 AM - 12:30 PM</span>
                    </div>
                    <p class="text-sm text-gray-600">Panel Discussion</p>
                  </div>

                  <div class="bg-white rounded-lg p-4 shadow-sm">
                    <div class="flex flex-wrap justify-between mb-2">
                      <span class="font-medium">Networking Lunch</span>
                      <span class="text-gray-600">12:30 PM - 2:00 PM</span>
                    </div>
                    <p class="text-sm text-gray-600">Main Hall</p>
                  </div>

                  <div class="text-center">
                    <button
                      class="text-blue-500 hover:text-blue-700 text-sm font-medium">
                      View full Day 1 schedule
                    </button>
                  </div>
                </div>
              </div>

              <!-- Day 2 -->
              <div>
                <h3 class="text-lg font-bold mb-2">Day 2 - May 16</h3>
                <div class="space-y-4">
                  <div class="bg-white rounded-lg p-4 shadow-sm">
                    <div class="flex flex-wrap justify-between mb-2">
                      <span class="font-medium">Blockchain Revolution</span>
                      <span class="text-gray-600">9:00 AM - 10:30 AM</span>
                    </div>
                    <p class="text-sm text-gray-600">
                      Mike Wilson, Blockchain Expert
                    </p>
                  </div>

                  <div class="bg-white rounded-lg p-4 shadow-sm">
                    <div class="flex flex-wrap justify-between mb-2">
                      <span class="font-medium">Cloud Computing Workshop</span>
                      <span class="text-gray-600">11:00 AM - 1:00 PM</span>
                    </div>
                    <p class="text-sm text-gray-600">Hands-on Session</p>
                  </div>

                  <div class="text-center">
                    <button
                      class="text-blue-500 hover:text-blue-700 text-sm font-medium">
                      View full Day 2 schedule
                    </button>
                  </div>
                </div>
              </div>

              <!-- Day 3 -->
              <div>
                <h3 class="text-lg font-bold mb-2">Day 3 - May 17</h3>
                <div class="space-y-4">
                  <div class="bg-white rounded-lg p-4 shadow-sm">
                    <div class="flex flex-wrap justify-between mb-2">
                      <span class="font-medium">Future of Work</span>
                      <span class="text-gray-600">9:00 AM - 10:30 AM</span>
                    </div>
                    <p class="text-sm text-gray-600">
                      Sarah Johnson, CTO of Global Solutions
                    </p>
                  </div>

                  <div class="bg-white rounded-lg p-4 shadow-sm">
                    <div class="flex flex-wrap justify-between mb-2">
                      <span class="font-medium">Closing Ceremony</span>
                      <span class="text-gray-600">4:00 PM - 6:00 PM</span>
                    </div>
                    <p class="text-sm text-gray-600">
                      Awards and Final Keynote
                    </p>
                  </div>

                  <div class="text-center">
                    <button
                      class="text-blue-500 hover:text-blue-700 text-sm font-medium">
                      View full Day 3 schedule
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Location -->
          <div>
            <h2 class="text-xl font-bold mb-4">Location</h2>
            <p class="text-gray-600 mb-4">
              747 Howard St, San Francisco, CA 94103
            </p>
            <div
              class="w-full h-64 bg-gray-200 rounded-lg relative overflow-hidden">
              <div
                class="absolute inset-0 flex items-center justify-center text-gray-500">
                <div class="text-center">
                  <i class="fas fa-map-marker-alt text-3xl mb-2"></i>
                  <p>Map would be displayed here</p>
                </div>
              </div>
            </div>
            <div class="mt-4">
              <a
                href="https://maps.google.com"
                target="_blank"
                class="text-blue-500 hover:text-blue-700 flex items-center">
                <i class="fas fa-directions mr-2"></i>
                <span>Get Directions</span>
              </a>
            </div>
          </div>

          <!-- Tags -->
          <div>
            <h2 class="text-xl font-bold mb-4">Tags</h2>
            <div class="flex flex-wrap gap-2">
              <a
                href="#"
                class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1 rounded-full text-sm">Technology</a>
              <a
                href="#"
                class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1 rounded-full text-sm">Conference</a>
              <a
                href="#"
                class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1 rounded-full text-sm">AI</a>
              <a
                href="#"
                class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1 rounded-full text-sm">Blockchain</a>
              <a
                href="#"
                class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1 rounded-full text-sm">Cloud Computing</a>
              <a
                href="#"
                class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1 rounded-full text-sm">Networking</a>
            </div>
          </div>

          <!-- Organizer Info -->
          <div>
            <h2 class="text-xl font-bold mb-4">Organizer</h2>
            <div class="flex items-start">
              <div class="flex-shrink-0">
                <img
                  src="../assets/images/ieee-essths.jpg"
                  alt="Tech Events Inc."
                  class="w-16 h-16 rounded-lg object-cover" />
              </div>
              <div class="ml-4">
                <h3 class="font-bold">Tech Events Inc.</h3>
                <p class="text-gray-600 mb-2">
                  Premier technology event organizer since 2010
                </p>
                <div class="flex space-x-4 mb-3">
                  <a href="#" class="text-blue-500 hover:text-blue-700">
                    <i class="fas fa-globe mr-1"></i>
                    <span>Website</span>
                  </a>
                  <a href="#" class="text-blue-500 hover:text-blue-700">
                    <i class="fas fa-envelope mr-1"></i>
                    <span>Contact</span>
                  </a>
                </div>
                <button
                  class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1 rounded-full">
                  <i class="fas fa-plus mr-1"></i>
                  <span>Follow</span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Share Modal -->
        <div
          class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
          id="shareModal">
          <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-xl font-bold">Share This Event</h3>
              <button
                class="text-gray-500 hover:text-gray-700"
                id="closeShareModal">
                <i class="fas fa-times"></i>
              </button>
            </div>

            <div class="space-y-4">
              <div class="flex justify-center space-x-6">
                <a href="#" class="flex flex-col items-center">
                  <div
                    class="w-12 h-12 bg-blue-600 text-white rounded-full flex items-center justify-center">
                    <i class="fab fa-facebook-f text-xl"></i>
                  </div>
                  <span class="text-sm mt-1">Facebook</span>
                </a>
                <a href="#" class="flex flex-col items-center">
                  <div
                    class="w-12 h-12 bg-blue-400 text-white rounded-full flex items-center justify-center">
                    <i class="fab fa-twitter text-xl"></i>
                  </div>
                  <span class="text-sm mt-1">Twitter</span>
                </a>
                <a href="#" class="flex flex-col items-center">
                  <div
                    class="w-12 h-12 bg-green-500 text-white rounded-full flex items-center justify-center">
                    <i class="fab fa-whatsapp text-xl"></i>
                  </div>
                  <span class="text-sm mt-1">WhatsApp</span>
                </a>
                <a href="#" class="flex flex-col items-center">
                  <div
                    class="w-12 h-12 bg-blue-700 text-white rounded-full flex items-center justify-center">
                    <i class="fab fa-linkedin-in text-xl"></i>
                  </div>
                  <span class="text-sm mt-1">LinkedIn</span>
                </a>
              </div>

              <div>
                <label class="text-sm text-gray-600 mb-1 block">Copy Link</label>
                <div class="flex">
                  <input
                    type="text"
                    value="https://lamma.com/events/tech-conference-2025"
                    class="form-control rounded-r-none"
                    readonly />
                  <button class="btn btn-outline-primary rounded-l-none">
                    Copy
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="lg:col-span-1">
        <!-- Action Buttons -->
        <div class="card shadow-sm mb-6">
          <div class="card-body">
            <div class="space-y-4">
              <a href="checkout.html">
                <button class="btn btn-primary w-full py-3">
                  <i class="fas fa-ticket-alt mr-2"></i> Register Now
                </button>
              </a>

              <button
                class="btn btn-outline-primary w-full flex items-center justify-center">
                <i class="far fa-calendar-plus mr-2"></i> Add to Calendar
              </button>
              <button
                class="btn btn-outline-secondary w-full flex items-center justify-center">
                <i class="far fa-question-circle mr-2"></i> Ask Organizer
              </button>
            </div>
          </div>
        </div>

        <!-- Event Stats -->
        <div class="card shadow-sm mb-6">
          <div class="card-header bg-white">
            <h3 class="font-bold">Event Details</h3>
          </div>
          <div class="card-body">
            <div class="space-y-4">
              <div class="flex items-center">
                <div
                  class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                  <i class="fas fa-users text-blue-500"></i>
                </div>
                <div>
                  <p class="text-sm text-gray-500">Capacity</p>
                  <p class="font-medium">500 attendees</p>
                </div>
              </div>
              <div class="flex items-center">
                <div
                  class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                  <i class="fas fa-clock text-purple-500"></i>
                </div>
                <div>
                  <p class="text-sm text-gray-500">Duration</p>
                  <p class="font-medium">3 days</p>
                </div>
              </div>
              <div class="flex items-center">
                <div
                  class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                  <i class="fas fa-globe text-green-500"></i>
                </div>
                <div>
                  <p class="text-sm text-gray-500">Language</p>
                  <p class="font-medium">English</p>
                </div>
              </div>
              <div class="flex items-center">
                <div
                  class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
                  <i class="fas fa-layer-group text-yellow-500"></i>
                </div>
                <div>
                  <p class="text-sm text-gray-500">Category</p>
                  <p class="font-medium">Technology, Conference</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Contact Information -->
        <div class="card shadow-sm mb-6">
          <div class="card-header bg-white">
            <h3 class="font-bold">Contact Information</h3>
          </div>
          <div class="card-body">
            <div class="space-y-4">
              <div class="flex items-start">
                <i class="fas fa-phone text-blue-500 mr-3 mt-1"></i>
                <div>
                  <p class="text-sm text-gray-500">Phone</p>
                  <p>+1 (555) 123-4567</p>
                </div>
              </div>
              <div class="flex items-start">
                <i class="fas fa-envelope text-blue-500 mr-3 mt-1"></i>
                <div>
                  <p class="text-sm text-gray-500">Email</p>
                  <p>info@techevents.example.com</p>
                </div>
              </div>
              <div class="flex items-start">
                <i class="fas fa-globe text-blue-500 mr-3 mt-1"></i>
                <div>
                  <p class="text-sm text-gray-500">Website</p>
                  <a href="#" class="text-blue-500 hover:underline">techconference2025.example.com</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="bg-white border-t py-8 mt-12">
    <div class="container mx-auto px-4">
      <div class="flex flex-col md:flex-row justify-between items-center">
        <div class="mb-4 md:mb-0">
          <a href="index.html" class="flex items-center space-x-2">
            <img
              src="../assets/images/logo.png"
              alt="Lamma Logo"
              class="h-8 w-8" />
          </a>
          <p class="text-gray-600 mt-2">Live in the Moment</p>
        </div>
        <div class="flex space-x-6">
          <a href="#" class="text-gray-600 hover:text-blue-500">
            <i class="fab fa-facebook-f"></i>
          </a>
          <a href="#" class="text-gray-600 hover:text-blue-500">
            <i class="fab fa-twitter"></i>
          </a>
          <a href="#" class="text-gray-600 hover:text-blue-500">
            <i class="fab fa-instagram"></i>
          </a>
          <a href="#" class="text-gray-600 hover:text-blue-500">
            <i class="fab fa-linkedin-in"></i>
          </a>
        </div>
      </div>
      <div class="mt-8 pt-8 border-t text-center">
        <p class="text-gray-600">© 2025 Lamma. All rights reserved.</p>
      </div>
    </div>
  </footer>

  <!-- Bootstrap JS Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Custom JavaScript -->
  <script src="js/script.js"></script>
  <!-- Event Details JavaScript -->
  <script src="js/event-details.js"></script>
</body>

</html>