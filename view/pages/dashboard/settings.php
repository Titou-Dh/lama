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

                <!-- Settings Navigation (Horizontal) -->
                <div class="settings-nav">
                    <div class="nav nav-pills" id="v-pills-tab" role="tablist">
                        <button
                            class="nav-link active"
                            id="v-pills-account-tab"
                            data-bs-toggle="pill"
                            data-bs-target="#v-pills-account"
                            type="button"
                            role="tab"
                            aria-controls="v-pills-account"
                            aria-selected="true">
                            <i class="bi bi-person-circle"></i> Account
                        </button>
                        <button
                            class="nav-link"
                            id="v-pills-notifications-tab"
                            data-bs-toggle="pill"
                            data-bs-target="#v-pills-notifications"
                            type="button"
                            role="tab"
                            aria-controls="v-pills-notifications"
                            aria-selected="false">
                            <i class="bi bi-bell"></i> Notifications
                        </button>
                        <button
                            class="nav-link"
                            id="v-pills-privacy-tab"
                            data-bs-toggle="pill"
                            data-bs-target="#v-pills-privacy"
                            type="button"
                            role="tab"
                            aria-controls="v-pills-privacy"
                            aria-selected="false">
                            <i class="bi bi-shield-lock"></i> Privacy & Security
                        </button>
                        <button
                            class="nav-link"
                            id="v-pills-integrations-tab"
                            data-bs-toggle="pill"
                            data-bs-target="#v-pills-integrations"
                            type="button"
                            role="tab"
                            aria-controls="v-pills-integrations"
                            aria-selected="false">
                            <i class="bi bi-puzzle"></i> Integrations
                        </button>
                    </div>
                </div>

                <!-- Settings Content -->
                <div class="settings-content">
                    <div class="tab-content" id="v-pills-tabContent">
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
                                    <div class="password-strength mt-2 mb-3">
                                        <div class="progress" style="height: 6px">
                                            <div
                                                class="progress-bar bg-success"
                                                role="progressbar"
                                                style="width: 75%"
                                                aria-valuenow="75"
                                                aria-valuemin="0"
                                                aria-valuemax="100"></div>
                                        </div>
                                        <small class="text-success mt-1 d-block">Strong password</small>
                                    </div>
                                    <p class="text-muted small">
                                        Password must be at least 8 characters long and include
                                        uppercase, lowercase, numbers, and special characters.
                                    </p>
                                </form>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button class="btn btn-custom btn-custom-outline">
                                    Cancel
                                </button>
                                <button class="save-btn">Save Changes</button>
                            </div>
                        </div>

                        <!-- Notification Settings -->
                        <div
                            class="tab-pane fade"
                            id="v-pills-notifications"
                            role="tabpanel"
                            aria-labelledby="v-pills-notifications-tab">
                            <div class="settings-section">
                                <h3 class="settings-section-title">Email Notifications</h3>
                                <div class="card settings-card mb-4">
                                    <div class="card-body">
                                        <div
                                            class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <h5 class="mb-1">Event Updates</h5>
                                                <p class="text-muted mb-0">
                                                    Receive notifications about changes to your events
                                                </p>
                                            </div>
                                            <label class="toggle-switch">
                                                <input type="checkbox" checked />
                                                <span class="toggle-slider"></span>
                                            </label>
                                        </div>
                                        <div class="ms-4 mt-3">
                                            <div class="form-check mb-2">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="eventChanges"
                                                    checked />
                                                <label class="form-check-label" for="eventChanges">
                                                    Event changes and updates
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="eventReminders"
                                                    checked />
                                                <label class="form-check-label" for="eventReminders">
                                                    Event reminders and notifications
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="eventCancellations"
                                                    checked />
                                                <label
                                                    class="form-check-label"
                                                    for="eventCancellations">
                                                    Event cancellations
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card settings-card mb-4">
                                    <div class="card-body">
                                        <div
                                            class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <h5 class="mb-1">Attendee Activity</h5>
                                                <p class="text-muted mb-0">
                                                    Notifications about attendee registrations and
                                                    interactions
                                                </p>
                                            </div>
                                            <label class="toggle-switch">
                                                <input type="checkbox" checked />
                                                <span class="toggle-slider"></span>
                                            </label>
                                        </div>
                                        <div class="ms-4 mt-3">
                                            <div class="form-check mb-2">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="newRegistrations"
                                                    checked />
                                                <label class="form-check-label" for="newRegistrations">
                                                    New attendee registrations
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="attendeeCancellations"
                                                    checked />
                                                <label
                                                    class="form-check-label"
                                                    for="attendeeCancellations">
                                                    Attendee cancellations
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="attendeeFeedback" />
                                                <label class="form-check-label" for="attendeeFeedback">
                                                    Attendee feedback and reviews
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card settings-card mb-4">
                                    <div class="card-body">
                                        <div
                                            class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <h5 class="mb-1">Financial Updates</h5>
                                                <p class="text-muted mb-0">
                                                    Notifications about payments and financial activity
                                                </p>
                                            </div>
                                            <label class="toggle-switch">
                                                <input type="checkbox" checked />
                                                <span class="toggle-slider"></span>
                                            </label>
                                        </div>
                                        <div class="ms-4 mt-3">
                                            <div class="form-check mb-2">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="paymentNotifications"
                                                    checked />
                                                <label
                                                    class="form-check-label"
                                                    for="paymentNotifications">
                                                    Payment notifications
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="refundRequests"
                                                    checked />
                                                <label class="form-check-label" for="refundRequests">
                                                    Refund requests
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="financialReports"
                                                    checked />
                                                <label class="form-check-label" for="financialReports">
                                                    Financial reports
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="settings-section">
                                <h3 class="settings-section-title">Push Notifications</h3>
                                <div class="card settings-card mb-4">
                                    <div class="card-body">
                                        <div
                                            class="d-flex justify-content-between align-items-center mb-3">
                                            <div>
                                                <h5 class="mb-1">Mobile Notifications</h5>
                                                <p class="text-muted mb-0">
                                                    Receive push notifications on your mobile device
                                                </p>
                                            </div>
                                            <label class="toggle-switch">
                                                <input type="checkbox" checked />
                                                <span class="toggle-slider"></span>
                                            </label>
                                        </div>
                                        <div class="ms-4 mt-3">
                                            <div class="form-check mb-2">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="pushEventUpdates"
                                                    checked />
                                                <label class="form-check-label" for="pushEventUpdates">
                                                    Event updates and changes
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="pushNewRegistrations"
                                                    checked />
                                                <label
                                                    class="form-check-label"
                                                    for="pushNewRegistrations">
                                                    New attendee registrations
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="pushPaymentNotifications"
                                                    checked />
                                                <label
                                                    class="form-check-label"
                                                    for="pushPaymentNotifications">
                                                    Payment notifications
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="pushMessages" />
                                                <label class="form-check-label" for="pushMessages">
                                                    New messages
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button class="btn btn-custom btn-custom-outline">
                                    Reset to Default
                                </button>
                                <button class="save-btn">Save Changes</button>
                            </div>
                        </div>

                        <!-- Privacy & Security Settings -->
                        <div
                            class="tab-pane fade"
                            id="v-pills-privacy"
                            role="tabpanel"
                            aria-labelledby="v-pills-privacy-tab">
                            <div class="settings-section">
                                <h3 class="settings-section-title">Privacy Settings</h3>
                                <div class="card settings-card mb-4">
                                    <div class="card-body">
                                        <div class="form-group mb-4">
                                            <label class="form-label">Profile Visibility</label>
                                            <select class="form-select">
                                                <option value="public" selected>
                                                    Public - Anyone can view your profile
                                                </option>
                                                <option value="private">
                                                    Private - Only registered users can view your profile
                                                </option>
                                                <option value="hidden">
                                                    Hidden - Only you can view your profile
                                                </option>
                                            </select>
                                        </div>

                                        <div class="form-group mb-4">
                                            <label class="form-label">Contact Information Visibility</label>
                                            <div class="form-check mb-2">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="showEmail"
                                                    checked />
                                                <label class="form-check-label" for="showEmail">
                                                    Show email address on profile
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="showPhone" />
                                                <label class="form-check-label" for="showPhone">
                                                    Show phone number on profile
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="showLocation"
                                                    checked />
                                                <label class="form-check-label" for="showLocation">
                                                    Show location on profile
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="settings-section">
                                <h3 class="settings-section-title">Security Settings</h3>
                                <div class="card settings-card mb-4">
                                    <div class="card-body">
                                        <div
                                            class="d-flex justify-content-between align-items-center mb-4">
                                            <div>
                                                <h5 class="mb-1">Two-Factor Authentication</h5>
                                                <p class="text-muted mb-0">
                                                    Add an extra layer of security to your account
                                                </p>
                                            </div>
                                            <label class="toggle-switch">
                                                <input type="checkbox" />
                                                <span class="toggle-slider"></span>
                                            </label>
                                        </div>

                                        <div class="form-group mb-4">
                                            <label class="form-label">Session Timeout</label>
                                            <select class="form-select">
                                                <option value="30">30 minutes</option>
                                                <option value="60" selected>1 hour</option>
                                                <option value="120">2 hours</option>
                                                <option value="240">4 hours</option>
                                                <option value="480">8 hours</option>
                                            </select>
                                            <small class="text-muted">You'll be automatically logged out after this period of
                                                inactivity</small>
                                        </div>

                                        <div class="mb-4">
                                            <h6 class="mb-2">Active Sessions</h6>
                                            <div
                                                class="card mb-2"
                                                style="border-radius: 8px; border: 1px solid #e0e0e0">
                                                <div class="card-body py-2 px-3">
                                                    <div
                                                        class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <p class="mb-0">
                                                                <i class="bi bi-laptop me-2"></i> MacBook Pro -
                                                                New York
                                                            </p>
                                                            <small class="text-muted">Current session</small>
                                                        </div>
                                                        <span class="badge-success">Active Now</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div
                                                class="card mb-2"
                                                style="border-radius: 8px; border: 1px solid #e0e0e0">
                                                <div class="card-body py-2 px-3">
                                                    <div
                                                        class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <p class="mb-0">
                                                                <i class="bi bi-phone me-2"></i> iPhone 13 - New
                                                                York
                                                            </p>
                                                            <small class="text-muted">Last active: 2 hours ago</small>
                                                        </div>
                                                        <button class="btn btn-sm btn-custom-danger">
                                                            Revoke
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <button
                                                type="button"
                                                class="btn btn-custom btn-custom-danger mt-2">
                                                Revoke All Other Sessions
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="settings-section">
                                <h3 class="settings-section-title">Data Management</h3>
                                <div class="card settings-card">
                                    <div class="card-body">
                                        <div class="mb-4">
                                            <h5 class="mb-2">Download Your Data</h5>
                                            <p class="text-muted mb-3">
                                                Download a copy of your personal data including profile
                                                information, events, and activity history.
                                            </p>
                                            <button
                                                type="button"
                                                class="btn btn-custom btn-custom-primary">
                                                Download My Data
                                            </button>
                                        </div>

                                        <div>
                                            <h5 class="mb-2">Delete Account</h5>
                                            <p class="text-muted mb-3">
                                                Permanently delete your account and all associated data.
                                                This action cannot be undone.
                                            </p>
                                            <button
                                                type="button"
                                                class="btn btn-custom btn-custom-danger">
                                                Delete Account
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button class="btn btn-custom btn-custom-outline">
                                    Cancel
                                </button>
                                <button class="save-btn">Save Changes</button>
                            </div>
                        </div>

                        <!-- Integrations Settings -->
                        <div
                            class="tab-pane fade"
                            id="v-pills-integrations"
                            role="tabpanel"
                            aria-labelledby="v-pills-integrations-tab">
                            <div class="settings-section">
                                <h3 class="settings-section-title">Connected Services</h3>

                                <div class="card settings-card mb-3">
                                    <div class="card-body">
                                        <div
                                            class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div
                                                    class="me-3 d-flex align-items-center justify-content-center"
                                                    style="
                            width: 50px;
                            height: 50px;
                            background-color: #f1f5f9;
                            border-radius: 10px;
                          ">
                                                    <i class="bi bi-google fs-4 text-primary"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">Google Calendar</h6>
                                                    <small class="text-muted">Connected on May 10, 2025</small>
                                                </div>
                                            </div>
                                            <div>
                                                <span class="badge-success me-2">Connected</span>
                                                <button class="btn btn-sm btn-custom-danger">
                                                    Disconnect
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card settings-card mb-3">
                                    <div class="card-body">
                                        <div
                                            class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div
                                                    class="me-3 d-flex align-items-center justify-content-center"
                                                    style="
                            width: 50px;
                            height: 50px;
                            background-color: #f1f5f9;
                            border-radius: 10px;
                          ">
                                                    <i class="bi bi-stripe fs-4 text-primary"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">Stripe</h6>
                                                    <small class="text-muted">Connected on April 22, 2025</small>
                                                </div>
                                            </div>
                                            <div>
                                                <span class="badge-success me-2">Connected</span>
                                                <button class="btn btn-sm btn-custom-danger">
                                                    Disconnect
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card settings-card mb-3">
                                    <div class="card-body">
                                        <div
                                            class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div
                                                    class="me-3 d-flex align-items-center justify-content-center"
                                                    style="
                            width: 50px;
                            height: 50px;
                            background-color: #f1f5f9;
                            border-radius: 10px;
                          ">
                                                    <i class="bi bi-mailchimp fs-4 text-primary"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">Mailchimp</h6>
                                                    <small class="text-muted">Connected on March 15, 2025</small>
                                                </div>
                                            </div>
                                            <div>
                                                <span class="badge-success me-2">Connected</span>
                                                <button class="btn btn-sm btn-custom-danger">
                                                    Disconnect
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="settings-section">
                                <h3 class="settings-section-title">Available Integrations</h3>

                                <div class="row row-cols-1 row-cols-md-2 g-4">
                                    <div class="col">
                                        <div class="card settings-card h-100">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div
                                                        class="me-3 d-flex align-items-center justify-content-center"
                                                        style="
                              width: 50px;
                              height: 50px;
                              background-color: #f1f5f9;
                              border-radius: 10px;
                            ">
                                                        <i class="bi bi-zoom-in fs-4 text-primary"></i>
                                                    </div>
                                                    <h5 class="card-title mb-0">Zoom</h5>
                                                </div>
                                                <p class="card-text text-muted">
                                                    Connect your Zoom account to easily create and manage
                                                    virtual events.
                                                </p>
                                                <button class="btn btn-custom btn-custom-primary">
                                                    Connect
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col">
                                        <div class="card settings-card h-100">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div
                                                        class="me-3 d-flex align-items-center justify-content-center"
                                                        style="
                              width: 50px;
                              height: 50px;
                              background-color: #f1f5f9;
                              border-radius: 10px;
                            ">
                                                        <i class="bi bi-slack fs-4 text-primary"></i>
                                                    </div>
                                                    <h5 class="card-title mb-0">Slack</h5>
                                                </div>
                                                <p class="card-text text-muted">
                                                    Get notifications and updates directly in your Slack
                                                    workspace.
                                                </p>
                                                <button class="btn btn-custom btn-custom-primary">
                                                    Connect
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col">
                                        <div class="card settings-card h-100">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div
                                                        class="me-3 d-flex align-items-center justify-content-center"
                                                        style="
                              width: 50px;
                              height: 50px;
                              background-color: #f1f5f9;
                              border-radius: 10px;
                            ">
                                                        <i class="bi bi-paypal fs-4 text-primary"></i>
                                                    </div>
                                                    <h5 class="card-title mb-0">PayPal</h5>
                                                </div>
                                                <p class="card-text text-muted">
                                                    Accept payments through PayPal for your events.
                                                </p>
                                                <button class="btn btn-custom btn-custom-primary">
                                                    Connect
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col">
                                        <div class="card settings-card h-100">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center mb-3">
                                                    <div
                                                        class="me-3 d-flex align-items-center justify-content-center"
                                                        style="
                              width: 50px;
                              height: 50px;
                              background-color: #f1f5f9;
                              border-radius: 10px;
                            ">
                                                        <i class="bi bi-linkedin fs-4 text-primary"></i>
                                                    </div>
                                                    <h5 class="card-title mb-0">LinkedIn</h5>
                                                </div>
                                                <p class="card-text text-muted">
                                                    Share your events on LinkedIn and connect with
                                                    professional networks.
                                                </p>
                                                <button class="btn btn-custom btn-custom-primary">
                                                    Connect
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
</body>

</html>