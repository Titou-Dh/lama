<?php
session_start();

require_once __DIR__ . '/../../config/google.php';
include_once __DIR__ . '/../../config/database.php';
include_once __DIR__ . '/../../controller/auth.php';

$error_signin = '';

if (isset($_GET['code'])) {
  try {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (isset($token['access_token'])) {
      $client->setAccessToken($token['access_token']);

      // Get profile info
      $google_oauth = new Google\Service\Oauth2($client);
      $google_account_info = $google_oauth->userinfo->get();

      $userinfo = [
        'email' => $google_account_info['email'],
        'full_name' => $google_account_info['name'],
      ];

      if ($userinfo) {
        $res = getUser($cnx, $userinfo['email']);
        if ($res) {
          $_SESSION['user'] = $userinfo['email'];
          $_SESSION['user_id'] = $res['id'];
          $_SESSION['user_full_name'] = $userinfo['full_name'];
        } else {
          $error_signin = "You need to sign up with your email first!";
        }
      } else {
        $error_signin = "Failed to authenticate with Google. Please try again.";
      }
    } else {
      error_log("Google OAuth Error: No access token received");
    }
  } catch (Exception $e) {
    error_log("Google OAuth Error: " . $e->getMessage());
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>EventLama - Discover Amazing Events</title>

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
    rel="stylesheet" />

  <!-- Swiper CSS for carousels -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
  <link rel="stylesheet" href="../styles/css/landing2.css" />
  <script src="../scripts/tailwind.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
  <?php
  include  '../partials/navbar.php';
  ?>

  <main>
    <!-- Hero Carousel -->
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-indicators">
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true"
          aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
      </div>
      <div class="carousel-inner">
        <div class="carousel-item active">
          <img src="../assets/images/music.jpg" class="d-block w-100" alt="Music Festival" />
          <div class="carousel-caption d-none d-md-block">
            <h2 class="text-3xl font-bold mb-2">Discover Amazing Events</h2>
            <p class="mb-4">Find the perfect events happening around you</p>
            <button class="btn btn-gradient rounded-full px-5 py-2">
              Explore Events
            </button>
          </div>
        </div>
        <div class="carousel-item">
          <img src="../assets/images/abed.jpg" class="d-block w-100" alt="Tech Conference" />
          <div class="carousel-caption d-none d-md-block">
            <h2 class="text-3xl font-bold mb-2">
              Connect With Like-Minded People
            </h2>
            <p class="mb-4">
              Attend events that match your interests and passions
            </p>
            <button class="btn btn-gradient rounded-full px-5 py-2">
              Find Your Community
            </button>
          </div>
        </div>
        <div class="carousel-item">
          <img src="../assets/images/tsyp.jpg" class="d-block w-100" alt="Business Networking" />
          <div class="carousel-caption d-none d-md-block">
            <h2 class="text-3xl font-bold mb-2">Create Your Own Events</h2>
            <p class="mb-4">
              Host and manage events with our powerful platform
            </p>
            <button class="btn btn-gradient rounded-full px-5 py-2">
              Start Creating
            </button>
          </div>
        </div>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>

    <!-- Categories Section -->
    <section class="pt-12 bg-gray-50 text-center">
      <div class="">
        <h2 class="text-3xl font-bold mb-8 text-center">
          Browse by Category
        </h2>

        <div class="categories-container flex space-x-4 pb-4 justify-center items-center">
          <!-- Music Category -->
          <div class="category-card flex-shrink-0 text-center">
            <div
              class="w-24 h-24 md:w-32 md:h-32 rounded-full bg-blue-100 flex items-center justify-center mx-auto mb-3 shadow-md">
              <i class="fas fa-music text-4xl text-gradient"></i>
            </div>
            <h3 class="font-medium">Music</h3>
          </div>

          <!-- Business Category -->
          <div class="category-card flex-shrink-0 text-center">
            <div
              class="w-24 h-24 md:w-32 md:h-32 rounded-full bg-purple-100 flex items-center justify-center mx-auto mb-3 shadow-md">
              <i class="fas fa-briefcase text-4xl text-gradient"></i>
            </div>
            <h3 class="font-medium">Business</h3>
          </div>

          <!-- Nightlife Category -->
          <div class="category-card flex-shrink-0 text-center">
            <div
              class="w-24 h-24 md:w-32 md:h-32 rounded-full bg-blue-100 flex items-center justify-center mx-auto mb-3 shadow-md">
              <i class="fas fa-cocktail text-4xl text-gradient"></i>
            </div>
            <h3 class="font-medium">Nightlife</h3>
          </div>

          <!-- Tech Events Category -->
          <div class="category-card flex-shrink-0 text-center">
            <div
              class="w-24 h-24 md:w-32 md:h-32 rounded-full bg-purple-100 flex items-center justify-center mx-auto mb-3 shadow-md">
              <i class="fas fa-laptop-code text-4xl text-gradient"></i>
            </div>
            <h3 class="font-medium">Tech Events</h3>
          </div>

          <!-- Charity Category -->
          <div class="category-card flex-shrink-0 text-center">
            <div
              class="w-24 h-24 md:w-32 md:h-32 rounded-full bg-blue-100 flex items-center justify-center mx-auto mb-3 shadow-md">
              <i class="fas fa-hand-holding-heart text-4xl text-gradient"></i>
            </div>
            <h3 class="font-medium">Charity</h3>
          </div>

          <!-- Food Category -->
          <div class="category-card flex-shrink-0 text-center">
            <div
              class="w-24 h-24 md:w-32 md:h-32 rounded-full bg-purple-100 flex items-center justify-center mx-auto mb-3 shadow-md">
              <i class="fas fa-utensils text-4xl text-gradient"></i>
            </div>
            <h3 class="font-medium">Food</h3>
          </div>

          <!-- Sports Category -->
          <div class="category-card flex-shrink-0 text-center">
            <div
              class="w-24 h-24 md:w-32 md:h-32 rounded-full bg-blue-100 flex items-center justify-center mx-auto mb-3 shadow-md">
              <i class="fas fa-running text-4xl text-gradient"></i>
            </div>
            <h3 class="font-medium">Sports</h3>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- Featured Events Section -->
  <section class="py-5 bg-gradient-light">
    <div class="container">
      <h2 class="text-3xl font-bold mb-4 text-gradient text-center">
        Featured Events
      </h2>
      <p class="text-gray-500 text-center mb-5">
        Discover the most popular events happening around you
      </p>

      <!-- Swiper Carousel -->
      <div class="swiper featured-events-swiper">
        <div class="swiper-wrapper">
          <!-- Event Card 1 -->
          <div class="swiper-slide">
            <div class="event-card">
              <div class="event-image" style="background-image: url('../assets/images/music.jpg')">
                <div class="event-date">Jun 15, 2025</div>
                <div class="event-category">Music</div>
              </div>
              <div class="event-content">
                <h3 class="event-title">Summer Music Festival 2025</h3>
                <div class="event-location">
                  <i class="fas fa-map-marker-alt"></i>
                  <span>sousse</span>
                </div>
                <p class="event-description">
                  Join us for the biggest music festival of the summer
                  featuring top artists. Experience amazing performances,
                  food, and activities.
                </p>
                <a href="#" class="btn btn-gradient rounded-pill px-4">Get Tickets</a>
              </div>
            </div>
          </div>

          <!-- Event Card 2 -->
          <div class="swiper-slide">
            <div class="event-card">
              <div class="event-image" style="background-image: url('../assets/images/ras.jpg')">
                <div class="event-date">Jul 22, 2025</div>
                <div class="event-category">Technology</div>
              </div>
              <div class="event-content">
                <h3 class="event-title">Future Tech Conference 2025</h3>
                <div class="event-location">
                  <i class="fas fa-map-marker-alt"></i>
                  <span>hammam sousse,essths</span>
                </div>
                <p class="event-description">
                  Explore the latest innovations in technology with industry
                  leaders. Network with professionals and gain insights into
                  future trends.
                </p>
                <a href="#" class="btn btn-gradient rounded-pill px-4">Get Tickets</a>
              </div>
            </div>
          </div>

          <!-- Event Card 3 -->
          <div class="swiper-slide">
            <div class="event-card">
              <div class="event-image" style="background-image: url('../assets/images/robot.jpg')">
                <div class="event-date">Aug 5, 2025</div>
                <div class="event-category">Tech</div>
              </div>
              <div class="event-content">
                <h3 class="event-title">robotics competetion</h3>
                <div class="event-location">
                  <i class="fas fa-map-marker-alt"></i>
                  <span>tunis,esprit</span>
                </div>
                <p class="event-description">
                  enjoy making your own robot and competing with it.
                </p>
                <a href="#" class="btn btn-gradient rounded-pill px-4">Get Tickets</a>
              </div>
            </div>
          </div>

          <!-- Event Card 4 -->
          <div class="swiper-slide">
            <div class="event-card">
              <div class="event-image" style="background-image: url('../assets/images/stamp.jpg')">
                <div class="event-date">Sep 18, 2025</div>
                <div class="event-category">Business</div>
              </div>
              <div class="event-content">
                <h3 class="event-title">Global Business Summit</h3>
                <div class="event-location">
                  <i class="fas fa-map-marker-alt"></i>
                  <span>Grand Hotel, London</span>
                </div>
                <p class="event-description">
                  Connect with business leaders and entrepreneurs from around
                  the world. Learn strategies for success in the global
                  market.
                </p>
                <a href="#" class="btn btn-gradient rounded-pill px-4">Get Tickets</a>
              </div>
            </div>
          </div>
        </div>

        <!-- Swiper Navigation and Pagination -->
        <div class="swiper-button-next"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-pagination"></div>
      </div>
    </div>
  </section>

  <!-- Upcoming Events Section -->
  <section class="py-5">
    <div class="container">
      <h2 class="text-3xl font-bold mb-4 text-gradient text-center">
        Upcoming Events
      </h2>
      <p class="text-gray-500 text-center mb-5">
        Don't miss out on these exciting events happening soon
      </p>

      <div class="row g-4">
        <!-- Event Card 1 -->
        <div class="col-lg-4 col-md-6">
          <div class="event-card">
            <div class="event-image" style="background-image: url('../assets/images/art.jpg')">
              <div class="event-date">Jun 10, 2025</div>
              <div class="event-category">Arts</div>
            </div>
            <div class="event-content">
              <h3 class="event-title">Modern Art Exhibition</h3>
              <div class="event-location">
                <i class="fas fa-map-marker-alt"></i>
                <span>nabeul, centre</span>
              </div>
              <p class="event-description">
                Explore contemporary art from emerging artists around the
                world. Experience innovative installations and
                thought-provoking pieces.
              </p>
              <a href="#" class="btn btn-gradient rounded-pill px-4">Get Tickets</a>
            </div>
          </div>
        </div>

        <!-- Event Card 2 -->
        <div class="col-lg-4 col-md-6">
          <div class="event-card">
            <div class="event-image" style="background-image: url('../assets/images/marathon.jpg')">
              <div class="event-date">Jun 20, 2025</div>
              <div class="event-category">Sports</div>
            </div>
            <div class="event-content">
              <h3 class="event-title">Marathon Championship</h3>
              <div class="event-location">
                <i class="fas fa-map-marker-alt"></i>
                <span>Downtown, Boston</span>
              </div>
              <p class="event-description">
                Join thousands of runners in this annual marathon event.
                Challenge yourself and compete for amazing prizes.
              </p>
              <a href="#" class="btn btn-gradient rounded-pill px-4">Get Tickets</a>
            </div>
          </div>
        </div>

        <!-- Event Card 3 -->
        <div class="col-lg-4 col-md-6">
          <div class="event-card">
            <div class="event-image" style="background-image: url('../assets/images/yoga.jpg')">
              <div class="event-date">Jun 25, 2025</div>
              <div class="event-category">Health</div>
            </div>
            <div class="event-content">
              <h3 class="event-title">Wellness & Mindfulness Retreat</h3>
              <div class="event-location">
                <i class="fas fa-map-marker-alt"></i>
                <span>Serenity Resort, Malibu</span>
              </div>
              <p class="event-description">
                Escape the hustle and bustle of daily life with this
                rejuvenating wellness retreat. Learn meditation techniques and
                healthy living practices.
              </p>
              <a href="#" class="btn btn-gradient rounded-pill px-4">Get Tickets</a>
            </div>
          </div>
        </div>

        <!-- Event Card 4 -->
        <div class="col-lg-4 col-md-6">
          <div class="event-card">
            <div class="event-image" style="background-image: url('../assets/images/clean.jpg')">
              <div class="event-date">Jul 2, 2025</div>
              <div class="event-category">Community</div>
            </div>
            <div class="event-content">
              <h3 class="event-title">Community Cleanup Day</h3>
              <div class="event-location">
                <i class="fas fa-map-marker-alt"></i>
                <span>Riverside Park, Portland</span>
              </div>
              <p class="event-description">
                Join your community in making a difference. Help clean up
                local parks and waterways while connecting with like-minded
                individuals.
              </p>
              <a href="#" class="btn btn-gradient rounded-pill px-4">Get Tickets</a>
            </div>
          </div>
        </div>

        <!-- Event Card 5 -->
        <div class="col-lg-4 col-md-6">
          <div class="event-card">
            <div class="event-image" style="background-image: url('../assets/images/card6.jpg')">
              <div class="event-date">Jul 10, 2025</div>
              <div class="event-category">Music</div>
            </div>
            <div class="event-content">
              <h3 class="event-title">Classical Music Night</h3>
              <div class="event-location">
                <i class="fas fa-map-marker-alt"></i>
                <span>tunis,lac</span>
              </div>
              <p class="event-description">
                Experience the magic of classical music performed by a
                world-renowned orchestra. An unforgettable evening of timeless
                masterpieces.
              </p>
              <a href="#" class="btn btn-gradient rounded-pill px-4">Get Tickets</a>
            </div>
          </div>
        </div>

        <!-- Event Card 6 -->
        <div class="col-lg-4 col-md-6">
          <div class="event-card">
            <div class="event-image" style="background-image: url('../assets/images/writing.jpg')">
              <div class="event-date">Jul 15, 2025</div>
              <div class="event-category">Education</div>
            </div>
            <div class="event-content">
              <h3 class="event-title">Creative Writing Workshop</h3>
              <div class="event-location">
                <i class="fas fa-map-marker-alt"></i>
                <span>Public Library, Seattle</span>
              </div>
              <p class="event-description">
                Develop your writing skills with guidance from published
                authors. Perfect for beginners and experienced writers looking
                to refine their craft.
              </p>
              <a href="#" class="btn btn-gradient rounded-pill px-4">Get Tickets</a>
            </div>
          </div>
        </div>
      </div>

      <div class="text-center mt-5">
        <a href="#" class="btn btn-outline-gradient rounded-pill px-5 py-2">View All Events</a>
      </div>
    </div>
  </section>

  <!-- Personalization Section -->
  <section class="personalization-section">
    <div class="personalization-bg"></div>
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6 mb-5 mb-lg-0">
          <h2 class="text-3xl font-bold mb-4 text-gradient">
            let's make it personal!
          </h2>
          <p class="text-gray-700 mb-4">
            Tell us what you're interested in, and we'll recommend events
            tailored just for you. Select your preferences below:
          </p>

          <div class="preference-tags mb-4">
            <span class="preference-tag">Music</span>
            <span class="preference-tag active">Art</span>
            <span class="preference-tag">Sports</span>
            <span class="preference-tag active">Technology</span>
            <span class="preference-tag">Food & Drink</span>
            <span class="preference-tag">Business</span>
            <span class="preference-tag">Health</span>
            <span class="preference-tag">Education</span>
            <span class="preference-tag">Community</span>
            <span class="preference-tag">Charity</span>
            <span class="preference-tag">Family</span>
            <span class="preference-tag">Fashion</span>
          </div>

          <button class="btn btn-gradient rounded-pill px-5 py-2">
            save Preferences
          </button>
        </div>
        <div class="col-lg-6">
          <div class="card border-0 shadow-lg">
            <div class="card-body p-4">
              <h3 class="card-title text-xl font-bold mb-4">
                Your Recommended Events
              </h3>

              <div class="recommended-event d-flex mb-4">
                <div class="flex-shrink-0 me-3" style="
                      width: 80px;
                      height: 80px;
                      background-image: url('../assets/images/art.jpg');
                      background-size: cover;
                      border-radius: 0.5rem;
                    "></div>
                <div>
                  <h4 class="text-lg font-semibold">
                    Contemporary Art Exhibition
                  </h4>
                  <p class="text-sm text-gray-500 mb-1">
                    <i class="far fa-calendar-alt me-1"></i> Jun 12, 2025
                  </p>
                  <p class="text-sm text-gray-500">
                    <i class="fas fa-map-marker-alt me-1"></i> Modern Gallery,
                    New York
                  </p>
                </div>
              </div>

              <div class="recommended-event d-flex mb-4">
                <div class="flex-shrink-0 me-3" style="
                      width: 80px;
                      height: 80px;
                      background-image: url('../assets/images/tech.jpg');
                      background-size: cover;
                      border-radius: 0.5rem;
                    "></div>
                <div>
                  <h4 class="text-lg font-semibold">
                    AI & Machine Learning Workshop
                  </h4>
                  <p class="text-sm text-gray-500 mb-1">
                    <i class="far fa-calendar-alt me-1"></i> Jun 18, 2025
                  </p>
                  <p class="text-sm text-gray-500">
                    <i class="fas fa-map-marker-alt me-1"></i> Tech Hub, San
                    Francisco
                  </p>
                </div>
              </div>

              <div class="recommended-event d-flex">
                <div class="flex-shrink-0 me-3" style="
                      width: 80px;
                      height: 80px;
                      background-image: url('../assets/images/photography.jpg');
                      background-size: cover;
                      border-radius: 0.5rem;
                    "></div>
                <div>
                  <h4 class="text-lg font-semibold">
                    Photography Masterclass
                  </h4>
                  <p class="text-sm text-gray-500 mb-1">
                    <i class="far fa-calendar-alt me-1"></i> Jun 25, 2025
                  </p>
                  <p class="text-sm text-gray-500">
                    <i class="fas fa-map-marker-alt me-1"></i> Creative
                    Studio, Chicago
                  </p>
                </div>
              </div>

              <div class="text-center mt-4">
                <a href="#" class="btn btn-outline-primary rounded-pill px-4">View All Recommendations</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <?php include "../partials/footer.php"; ?>

  <?php
  $error_signin && '
              <script>
              Swal.fire({
              icon: "error",
              title: "Login Failed",
              text: data.message,
              confirmButtonText: "OK",
            });
              </script>
    ';
  ?>

  <!-- Script for navbar scroll effect -->

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- flow bite JS -->
  <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

  <!-- Swiper JS -->
  <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

  <!-- Custom JavaScript -->
  <script>
    // Initialize Swiper
    const swiper = new Swiper(".featured-events-swiper", {
      slidesPerView: 1,
      spaceBetween: 20,
      loop: true,
      pagination: {
        el: ".swiper-pagination",
        clickable: true,
      },
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },
      breakpoints: {
        640: {
          slidesPerView: 1,
          spaceBetween: 20,
        },
        768: {
          slidesPerView: 2,
          spaceBetween: 30,
        },
        1024: {
          slidesPerView: 3,
          spaceBetween: 30,
        },
      },
    });

    // Preference tags
    const preferenceTags = document.querySelectorAll(".preference-tag");
    preferenceTags.forEach((tag) => {
      tag.addEventListener("click", function() {
        this.classList.toggle("active");
      });
    });
  </script>
  <script src="../scripts/landing.js"></script>
</body>

</html>