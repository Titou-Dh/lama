<!DOCTYPE html>
<?php
include "../../../config/session.php";
checkSession();

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
                                src="https://img.freepik.com/premium-vector/avatar-profile-icon-flat-style-female-user-profile-vector-illustration-isolated-background-women-profile-sign-business-concept_157943-38866.jpg?semt=ais_hybrid"
                                alt="Organizer avatar"
                                class="profile-avatar" />
                        </div>
                        <div class="col-md-8">
                            <h2 class="mb-1">Sarah Johnson</h2>
                            <p class="text-muted mb-2">
                                <i class="bi bi-briefcase me-2"></i>Senior Event Coordinator
                            </p>
                            <p class="text-muted mb-3">
                                <i class="bi bi-geo-alt me-2"></i>New York, NY
                            </p>
                            <div class="mb-3">
                                <span class="expertise-badge">Tech Conferences</span>
                                <span class="expertise-badge">Workshops</span>
                                <span class="expertise-badge">Corporate Events</span>
                                <span class="expertise-badge">Networking</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="star-rating me-2">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-half"></i>
                                </div>
                                <span class="text-muted">4.8 (126 reviews)</span>
                            </div>
                        </div>
                    </div>

                    <div class="profile-stats">
                        <div class="profile-stat-item">
                            <div class="number">87</div>
                            <div class="label">Events Organized</div>
                        </div>
                        <div class="profile-stat-item">
                            <div class="number">12,450</div>
                            <div class="label">Total Attendees</div>
                        </div>
                        <div class="profile-stat-item">
                            <div class="number">1</div>
                            <div class="label">Upcoming Events</div>
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
                            <div class="info-item">
                                <div class="info-label">Full Name</div>

                        </div>

                        <!-- Expertise -->
                        <div class="info-card">
                            <h3 class="section-title">Areas of Expertise</h3>
                            <div>
                                <span class="expertise-badge">Tech Conferences</span>
                                <span class="expertise-badge">Workshops</span>
                                <span class="expertise-badge">Corporate Events</span>
                                <span class="expertise-badge">Networking</span>
                                <span class="expertise-badge">Product Launches</span>
                                <span class="expertise-badge">Team Building</span>
                                <span class="expertise-badge">Virtual Events</span>
                                <span class="expertise-badge">Hybrid Events</span>
                            </div>
                        </div>

                        <!-- Reviews -->
                        <div class="info-card">
                            <div
                                class="d-flex justify-content-between align-items-center mb-4">
                                <h3 class="section-title mb-0">Recent Reviews</h3>
                                <a href="#" class="text-muted small">View All</a>
                            </div>

                            <div class="review-card">
                                <div class="reviewer-info">
                                    <img
                                        src="https://images.unsplash.com/photo-1633332755192-727a05c4013d?q=80&w=1780&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                        alt="Reviewer"
                                        class="reviewer-avatar" />
                                    <div>
                                        <div class="reviewer-name">Michael Chen</div>
                                        <div class="review-date">2 days ago</div>
                                    </div>
                                </div>
                                <div class="star-rating mb-2">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                </div>
                                <p class="review-text">
                                    Sarah organized our tech conference flawlessly. Her attention
                                    to detail and ability to handle last-minute changes was
                                    impressive. Would definitely work with her again!
                                </p>
                            </div>
                            <div class="review-card">
                                <div class="reviewer-info">
                                    <img
                                        src="https://plus.unsplash.com/premium_photo-1670884441012-c5cf195c062a?q=80&w=1887&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                                        alt="Reviewer"
                                        class="reviewer-avatar" />
                                    <div>
                                        <div class="reviewer-name">Jessica Williams</div>
                                        <div class="review-date">1 week ago</div>
                                    </div>
                                </div>
                                <div class="star-rating mb-2">
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star-fill"></i>
                                    <i class="bi bi-star"></i>
                                </div>
                                <p class="review-text">
                                    Great experience working with Sarah on our company's annual
                                    retreat. She managed to keep everything within budget while
                                    still delivering a memorable experience.
                                </p>
                            </div>
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
                                <li class="nav-item" role="presentation">
                                    <button
                                        class="nav-link"
                                        id="drafts-tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#drafts"
                                        type="button"
                                        role="tab">
                                        Drafts
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
                                        <div class="col">
                                            <div class="card h-100 border-0 shadow-sm">
                                                <img
                                                    src="https://www.de-ctr.org/wp-content/uploads/2019/08/tech-talk-1024x512.jpg"
                                                    class="card-img-top"
                                                    alt="Upcoming Event" />
                                                <div class="card-body">
                                                    <h5 class="card-title">Tech Talk: Future of AI</h5>
                                                    <p class="card-text text-muted small mb-3">
                                                        Guest lecture by leading AI researcher
                                                    </p>
                                                    <div
                                                        class="d-flex align-items-center text-muted small mb-2">
                                                        <i class="bi bi-calendar me-2"></i>
                                                        <span>Tomorrow, 4:00 PM</span>
                                                    </div>
                                                    <div
                                                        class="d-flex align-items-center text-muted small mb-2">
                                                        <i class="bi bi-geo-alt me-2"></i>
                                                        <span>Lecture Hall B</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
                                        <div class="col">
                                            <div class="card h-100 border-0 shadow-sm">
                                                <img
                                                    src="https://www.bostontechmom.com/wp-content/uploads/2022/11/hackathon-tech-banner.jpg"
                                                    class="card-img-top"
                                                    alt="Past Event" />
                                                <div class="card-body">
                                                    <h5 class="card-title">Hackathon 2024</h5>
                                                    <p class="card-text text-muted small mb-3">
                                                        A 48-hour coding challenge.
                                                    </p>
                                                    <div
                                                        class="d-flex align-items-center text-muted small mb-2">
                                                        <i class="bi bi-calendar me-2"></i>
                                                        <span>March 15, 2024</span>
                                                    </div>
                                                    <div
                                                        class="d-flex align-items-center text-muted small mb-2">
                                                        <i class="bi bi-geo-alt me-2"></i>
                                                        <span>Innovation Center</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Drafts Tab -->
                                <div
                                    class="tab-pane fade"
                                    id="drafts"
                                    role="tabpanel"
                                    aria-labelledby="drafts-tab">
                                    <h3 class="section-title">Your Drafts</h3>
                                    <div class="row row-cols-1 row-cols-md-2 g-4">
                                        <div class="col">
                                            <div class="card h-100 border-0 shadow-sm">
                                                <img
                                                    src="https://thefusioneer.com/wp-content/uploads/2023/11/5-AI-Advancements-to-Expect-in-the-Next-10-Years-scaled.jpeg"
                                                    class="card-img-top"
                                                    alt="Draft Event" />
                                                <div class="card-body">
                                                    <h5 class="card-title">AI Workshop</h5>
                                                    <p class="card-text text-muted small mb-3">
                                                        Hands-on session on AI tools.
                                                    </p>
                                                    <div
                                                        class="d-flex align-items-center text-muted small mb-2">
                                                        <i class="bi bi-calendar me-2"></i>
                                                        <span>April 5, 2025</span>
                                                    </div>
                                                    <div
                                                        class="d-flex align-items-center text-muted small mb-2">
                                                        <i class="bi bi-geo-alt me-2"></i>
                                                        <span>Lab 3</span>
                                                    </div>
                                                </div>
                                                <div class="card-footer bg-white border-0 pt-0">
                                                    <button class="btn btn-sm btn-outline-primary w-100">
                                                        Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
    <!-- <script src="../../scripts/soft-ui-dashboard.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const tabButtons = document.querySelectorAll("#profileTabs .nav-link");
            tabButtons.forEach((button) => {
                button.addEventListener("click", function() {
                    sidebarNavItems.forEach((el) => el.classList.remove("active"));
                    activeSidebarItem.classList.add("active");
                });
            });

            const editButton = document.getElementById("edit-info-btn");
            const inputs = document.querySelectorAll(".info-value");

            editButton.addEventListener("click", () => {
                const isEditing = editButton.textContent === "Save";
                inputs.forEach((input) => (input.disabled = isEditing));
                editButton.textContent = isEditing ? "Edit" : "Save";
            });
        });
    </script>
</body>

</html>