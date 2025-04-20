<!DOCTYPE html>
<?php
include "../../../config/session.php";
checkSession();

?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
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
    <link rel="stylesheet" href="../../styles/css/settings.css" />
    <link rel="icon" type="image/png" href="../../assets/images/logo.png" />

</head>

<body class="g-sidenav-show bg-gray-100">
    <?php include '../../partials/dashboar-sidebar.php' ?>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <?php include '../../partials/dashboard-navbar.php' ?>
        <div class="content-wrapper">
            <div class="container-fluid">
                <!-- Header with user info -->
                <div class="profile-header mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="section-title mb-4">Account Settings</h2>
                            <p class="text-muted">
                                Manage your account settings and preferences
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="d-flex align-items-center justify-content-end">
                                <div class="me-3 text-end">
                                    <h6 class="mb-0">Sarah Johnson</h6>
                                </div>
                                <img
                                    src="https://img.freepik.com/premium-vector/avatar-profile-icon-flat-style-female-user-profile-vector-illustration-isolated-background-women-profile-sign-business-concept_157943-38866.jpg?semt=ais_hybrid"
                                    alt="Profile"
                                    class="rounded-circle"
                                    style="width: 50px; height: 50px; object-fit: cover" />
                            </div>
                        </div>
                    </div>
                </div>
                        <!-- Account Settings -->
                        <div
                            class="tab-pane fade show active"
                            id="v-pills-account"
                            role="tabpanel"
                            aria-labelledby="v-pills-account-tab">
                            <div class="settings-section">
                                <h3 class="settings-section-title">Personal Information</h3>
                                <form>
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label for="firstName" class="form-label">First Name</label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="firstName"
                                                value="Sarah" />
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="lastName" class="form-label">Last Name</label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="lastName"
                                                value="Johnson" />
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label for="email" class="form-label">Email Address</label>
                                            <input
                                                type="email"
                                                class="form-control"
                                                id="email"
                                                value="sarah.johnson@eventpro.com" />
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input
                                                type="tel"
                                                class="form-control"
                                                id="phone"
                                                value="+1 (555) 123-4567" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="jobTitle" class="form-label">Job Title</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="jobTitle"
                                            value="Senior Event Coordinator" />
                                    </div>
                                    <div class="form-group">
                                        <label for="location" class="form-label">Location</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="location"
                                            value="New York, NY" />
                                    </div>
                                    <div class="form-group">
                                        <label for="bio" class="form-label">Bio</label>
                                        <textarea class="form-control" id="bio" rows="4">
Senior Event Coordinator with over 10 years of experience organizing tech conferences, workshops, and corporate events. Passionate about creating memorable experiences and fostering meaningful connections.</textarea>
                                    </div>
                                </form>
                            </div>

                            <div class="settings-section">
                                <h3 class="settings-section-title">Profile Picture</h3>
                                <div class="d-flex align-items-center mb-3">
                                    <img
                                        src="https://img.freepik.com/premium-vector/avatar-profile-icon-flat-style-female-user-profile-vector-illustration-isolated-background-women-profile-sign-business-concept_157943-38866.jpg?semt=ais_hybrid"
                                        alt="Profile Picture"
                                        class="rounded-circle me-4"
                                        style="
                                        width: 100px;
                                        height: 100px;
                                        object-fit: cover;
                                        border: 3px solid #4361ee;
                                        " />
                                <div>
                                        <div class="mb-3">
                                            <input
                                                class="form-control"
                                                type="file"
                                                id="profilePicture" />
                                        </div>
                                        <div>
                                            <button class="btn btn-custom btn-custom-primary me-2">
                                                Upload New Picture
                                            </button>
                                            <button class="btn btn-custom btn-custom-danger">
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-muted small">
                                    Recommended size: 400x400 pixels (max 2MB). JPG, GIF, or PNG.
                                </p>
                            </div>

                            <div class="settings-section">
                                <h3 class="settings-section-title">Expertise & Skills</h3>
                                <div class="form-group">
                                    <label class="form-label">Areas of Expertise</label>
                                    <div class="mb-3">
                                        <div class="d-flex flex-wrap gap-2">
                                            <span class="expertise-badge d-flex align-items-center">
                                                Tech Conferences
                                                <i
                                                    class="bi bi-x-circle ms-2"
                                                    style="cursor: pointer"></i>
                                            </span>
                                            <span class="expertise-badge d-flex align-items-center">
                                                Workshops
                                                <i
                                                    class="bi bi-x-circle ms-2"
                                                    style="cursor: pointer"></i>
                                            </span>
                                            <span class="expertise-badge d-flex align-items-center">
                                                Corporate Events
                                                <i
                                                    class="bi bi-x-circle ms-2"
                                                    style="cursor: pointer"></i>
                                            </span>
                                            <span class="expertise-badge d-flex align-items-center">
                                                Networking
                                                <i
                                                    class="bi bi-x-circle ms-2"
                                                    style="cursor: pointer"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="input-group">
                                        <input
                                            type="text"
                                            class="form-control"
                                            placeholder="Add a new expertise" />
                                        <button
                                            class="btn btn-custom btn-custom-primary"
                                            type="button">
                                            Add
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="settings-section">
                                <h3 class="settings-section-title">Change Password</h3>
                                <form>
                                    <div class="form-group">
                                        <label for="currentPassword" class="form-label">Current Password</label>
                                        <input
                                            type="password"
                                            class="form-control"
                                            id="currentPassword" />
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label for="newPassword" class="form-label">New Password</label>
                                            <input
                                                type="password"
                                                class="form-control"
                                                id="newPassword" />
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label for="confirmPassword" class="form-label">Confirm New Password</label>
                                            <input
                                                type="password"
                                                class="form-control"
                                                id="confirmPassword" />
                                        </div>
                                    </div>

                                </form>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button class="btn btn-custom btn-custom-outline">
                                    Cancel
                                </button>
                                <button class="save-btn">Save Changes</button>
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
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const navItems = document.querySelectorAll(".nav-item");

            navItems.forEach((item) => {
                item.addEventListener("click", function() {
                    navItems.forEach((el) => el.classList.remove("active"));
                    this.classList.add("active");
                });
            });

            // Add animation to settings tabs
            const tabButtons = document.querySelectorAll('[data-bs-toggle="pill"]');
            tabButtons.forEach((button) => {
                button.addEventListener("click", function() {
                    const targetId = this.getAttribute("data-bs-target");
                    const targetPane = document.querySelector(targetId);

                    if (targetPane) {
                        targetPane.style.animation = "none";
                        setTimeout(() => {
                            targetPane.style.animation = "fadeIn 0.5s ease-in-out";
                        }, 10);
                    }
                });
            });

            // Toggle switches functionality
            const toggleSwitches = document.querySelectorAll(
                ".toggle-switch input"
            );
            toggleSwitches.forEach((toggle) => {
                toggle.addEventListener("change", function() {
                    const parentCard = this.closest(".card");
                    const checkboxes = parentCard.querySelectorAll(
                        ".form-check-input:not(.toggle-switch input)"
                    );

                    checkboxes.forEach((checkbox) => {
                        checkbox.disabled = !this.checked;
                    });
                });
            });
        });
    </script>
</body>

</html>