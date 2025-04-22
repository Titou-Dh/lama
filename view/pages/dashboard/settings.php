<!DOCTYPE html>
<?php
include "../../../config/session.php";
checkSession();

// Include database connection and user controller
include_once "../../../config/database.php"; // This will give us $cnx
include_once "../../../controller/user.php";

// Get user ID from session
$userId = $_SESSION['user_id'];

// Get user data
$userData = getUserById($cnx, $userId);

// Process form submissions
$passwordMessage = '';
$profileMessage = '';
$deleteMessage = '';

// Handle password change
if (isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $passwordMessage = '<div class="alert alert-danger">All password fields are required</div>';
    } elseif ($newPassword !== $confirmPassword) {
        $passwordMessage = '<div class="alert alert-danger">New passwords do not match</div>';
    } else {
        $result = changeUserPassword($cnx, $userId, $currentPassword, $newPassword);
        if ($result['success']) {
            $passwordMessage = '<div class="alert alert-success">' . $result['message'] . '</div>';
        } else {
            $passwordMessage = '<div class="alert alert-danger">' . $result['message'] . '</div>';
        }
    }
}

// Handle profile update
if (isset($_POST['update_profile'])) {
    // Check if username is already taken
    $newUsername = $_POST['username'] ?? '';
    $usernameAvailable = true;
    
    if ($newUsername !== $userData['username']) {
        // Check if username exists in database
        try {
            $stmt = $cnx->prepare("SELECT id FROM users WHERE username = :username AND id != :user_id");
            $stmt->bindParam(':username', $newUsername);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $usernameAvailable = false;
                $profileMessage = '<div class="alert alert-danger">Username is already taken. Please choose another one.</div>';
            }
        } catch (PDOException $e) {
            error_log("Username check error: " . $e->getMessage());
            $profileMessage = '<div class="alert alert-danger">An error occurred. Please try again.</div>';
            $usernameAvailable = false;
        }
    }
    
    if ($usernameAvailable) {
        $userData = [
            'username' => $newUsername,
            'full_name' => $_POST['full_name'] ?? '',
            'email' => $_POST['email'] ?? '',
            // Removed is_organizer toggle
        ];
        
        // Handle profile image upload
        $fileData = null;
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $fileData = $_FILES['profile_image'];
        }
        
        $result = updateUserProfile($cnx, $userId, $userData, $fileData);
        if ($result) {
            $profileMessage = '<div class="alert alert-success">Profile updated successfully</div>';
            // Refresh user data
            $userData = getUserById($cnx, $userId);
        } else {
            $profileMessage = '<div class="alert alert-danger">Failed to update profile</div>';
        }
    }
}

// Handle account deletion
if (isset($_POST['delete_account'])) {
    $password = $_POST['delete_password'] ?? '';
    
    if (empty($password)) {
        $deleteMessage = '<div class="alert alert-danger">Password is required to delete your account</div>';
    } else {
        $result = deleteUserAccount($cnx, $userId, $password);
        if ($result) {
            // Destroy session and redirect to login
            session_destroy();
            header('Location: ../../../login.php?message=account_deleted');
            exit;
        } else {
            $deleteMessage = '<div class="alert alert-danger">Incorrect password or unable to delete account</div>';
        }
    }
}

// Default avatar URL
$defaultAvatar = "https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_1280.png";
$profileImage = !empty($userData['profile_image']) ? $userData['profile_image'] : $defaultAvatar;
?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings</title>
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
        <div class="container-fluid py-4">
            <!-- Profile Header -->
            <div class="profile-header">
                <img src="<?php echo $profileImage; ?>" alt="Profile Avatar" class="profile-avatar">
                <div class="profile-info">
                    <h2><?php echo htmlspecialchars($userData['full_name']); ?></h2>
                    <p>@<?php echo htmlspecialchars($userData['username']); ?></p>
                    <p><?php echo htmlspecialchars($userData['email']); ?></p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-header pb-0">
                            <h6>Account Settings</h6>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab">
                                        Profile Information
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab">
                                        Password
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="danger-tab" data-bs-toggle="tab" data-bs-target="#danger" type="button" role="tab">
                                        Delete Account
                                    </button>
                                </li>
                            </ul>
                            
                            <div class="tab-content mt-4" id="settingsTabsContent">
                                <!-- Profile Information Tab -->
                                <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                    <div class="settings-card">
                                        <h3 class="settings-title">Profile Information</h3>
                                        <?php echo $profileMessage; ?>
                                        <form method="post" enctype="multipart/form-data">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="username" class="form-control-label">Username</label>
                                                        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($userData['username']); ?>">
                                                        <small class="text-muted">Choose a unique username</small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="email" class="form-control-label">Email address</label>
                                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="full_name" class="form-control-label">Full Name</label>
                                                        <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($userData['full_name']); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="created_at" class="form-control-label">Member Since</label>
                                                        <input type="text" class="form-control" id="created_at" value="<?php echo date('F j, Y', strtotime($userData['created_at'])); ?>" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="profile_image" class="form-control-label">Profile Image</label>
                                                        <input type="file" class="form-control" id="profile_image" name="profile_image">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-control-label">Current Profile Image</label>
                                                        <div>
                                                            <img src="<?php echo $profileImage; ?>" alt="Profile Image" class="img-fluid rounded" style="max-height: 100px;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Removed organizer toggle -->
                                            <div class="d-flex justify-content-end mt-4">
                                                <button type="submit" name="update_profile" class="btn btn-primary">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                
                                <!-- Password Tab -->
                                <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">
                                    <div class="settings-card">
                                        <h3 class="settings-title">Change Password</h3>
                                        <?php echo $passwordMessage; ?>
                                        <form method="post">
                                            <div class="form-group">
                                                <label for="current_password" class="form-control-label">Current Password</label>
                                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="new_password" class="form-control-label">New Password</label>
                                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="confirm_password" class="form-control-label">Confirm New Password</label>
                                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                            </div>
                                            <div class="d-flex justify-content-end">
                                                <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                
                                <!-- Danger Zone Tab -->
                                <div class="tab-pane fade" id="danger" role="tabpanel" aria-labelledby="danger-tab">
                                    <div class="settings-card danger-zone">
                                        <h3 class="settings-title text-danger">Delete Account</h3>
                                        <p class="text-muted">Once you delete your account, there is no going back. Please be certain.</p>
                                        <?php echo $deleteMessage; ?>
                                        <form method="post" id="deleteAccountForm">
                                            <div class="form-group">
                                                <label for="delete_password" class="form-control-label">Enter your password to confirm</label>
                                                <input type="password" class="form-control" id="delete_password" name="delete_password" required>
                                            </div>
                                            <div class="d-flex justify-content-end">
                                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal">Delete Account</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmModalLabel">Confirm Account Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you absolutely sure you want to delete your account? This action cannot be undone.</p>
                    <p>All your data, including events, orders, and personal information will be permanently removed.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Yes, Delete My Account</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../../scripts/core/popper.min.js"></script>
    <script src="../../scripts/core/bootstrap.min.js"></script>
    <script src="../../scripts/soft-ui-dashboard.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Handle delete confirmation
            document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
                document.getElementById('deleteAccountForm').innerHTML += '<input type="hidden" name="delete_account" value="1">';
                document.getElementById('deleteAccountForm').submit();
            });
            
            // Show active tab based on URL hash
            const hash = window.location.hash;
            if (hash) {
                const tab = document.querySelector(`[data-bs-target="${hash}"]`);
                if (tab) {
                    const tabInstance = new bootstrap.Tab(tab);
                    tabInstance.show();
                }
            }
            
            // Update URL hash when tab changes
            const tabs = document.querySelectorAll('[data-bs-toggle="tab"]');
            tabs.forEach(tab => {
                tab.addEventListener('shown.bs.tab', function(e) {
                    const target = e.target.getAttribute('data-bs-target');
                    window.location.hash = target;
                });
            });
            
            // Username validation
            const usernameInput = document.getElementById('username');
            if (usernameInput) {
                usernameInput.addEventListener('input', function() {
                    // Remove spaces and special characters
                    this.value = this.value.replace(/[^a-zA-Z0-9_]/g, '');
                });
            }
        });
    </script>
</body>
</html>
