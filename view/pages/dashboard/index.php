<!DOCTYPE html>
<?php
include "../../../config/session.php";
checkSession();

?>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
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

</head>

<body class="g-sidenav-show bg-gray-100">
  <?php include '../../partials/dashboar-sidebar.php' ?>
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <?php include '../../partials/dashboard-navbar.php' ?>
    <?php
    if (isset($_SESSION['user'])) {
      echo '<div class="container-fluid py-4">';
      echo '<div class="card">';
      echo '<div class="card-header pb-0">';
      echo '<h6>User Information</h6>';
      echo '</div>';
      echo '<div class="card-body">';
      echo '<div class="row">';
      echo '<div class="col-md-6">';
      echo '<p><strong>Email:</strong> ' . htmlspecialchars($_SESSION['user']) . '</p>';
      echo '<p><strong>User ID:</strong> ' . htmlspecialchars($_SESSION['user_id']) . '</p>';
      echo '<p><strong>Full Name:</strong> ' . htmlspecialchars($_SESSION['user_full_name']) . '</p>';
      echo '</div>';
      echo '</div>';
      echo '</div>';
      echo '</div>';
      echo '</div>';
    } else {
      echo '<div class="container-fluid py-4">';
      echo '<div class="alert alert-warning">No active session found</div>';
      echo '</div>';
    }
    ?>
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