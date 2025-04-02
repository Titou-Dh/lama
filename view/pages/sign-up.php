<?php

include_once '../../controller/auth.php';
include_once '../../config/database.php';

if (!empty($_POST) && isset($_POST['name'], $_POST['email'], $_POST['password'])) {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $password = $_POST['password'];

  $result = registerUser($cnx, $name, $email, $password);

  if ($result === AuthResult::SUCCESS) {
    echo json_encode(['status' => 'success', 'message' => 'Registration successful!']);
  } elseif ($result === AuthResult::USER_EXISTS) {
    echo json_encode(['status' => 'error', 'message' => 'User already exists!']);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Registration failed!']);
  }
  exit;
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Event Sign Up Page</title>
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="../styles/css/auth.css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="../scripts/tailwind.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
  <div class="auth-container">
    <!-- Left Section - Event Image and Text -->
    <div class="left-section hidden lg:block">
      <img
        src="../assets/images/auth-img-v2.png"
        alt="Concert event with blue lighting and crowd"
        class="bg-image" />
      <div class="content">
        <h1 class="title">
          Join the<br />
          Adventure
          <br />
          with Us
        </h1>
        <div class="footer">
          <img class="logo" src="../assets/images/logo.png" alt="Logo" />
          <p class="tagline">Lama – Uniting Events, Elevating Experiences!</p>
        </div>
      </div>
    </div>

    <!-- Right Section - Sign Up Form -->
    <div class="right-section w-full lg:w-1/2">
      <div class="form-container">
        <div class="form-header">
          <h2>Create Account</h2>
          <p>Sign up to get started</p>
        </div>

        <form id="signupForm" method="POST">
          <div class="form-group">
            <label for="fullname">Full Name</label>
            <div class="input-container">
              <i class="fas fa-user input-icon"></i>
              <input
                type="text"
                id="fullname"
                name="name"
                placeholder="John Doe"
                required />
            </div>
          </div>

          <div class="form-group">
            <label for="email">Email</label>
            <div class="input-container">
              <i class="fas fa-envelope input-icon"></i>
              <input
                type="email"
                id="email"
                name="email"
                placeholder="johndoe@example.com"
                required />
            </div>
          </div>

          <div class="form-group">
            <label for="password">Password</label>
            <div class="input-container">
              <i class="fas fa-lock input-icon"></i>
              <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter your password"
                required />
              <button
                type="button"
                class="password-toggle"
                id="passwordToggle">
                <i class="fas fa-eye"></i>
              </button>
            </div>
          </div>

          <div class="form-group">
            <label for="confirmPassword">Confirm Password</label>
            <div class="input-container">
              <i class="fas fa-lock input-icon"></i>
              <input
                type="password"
                id="confirmPassword"
                placeholder="Confirm your password"
                required />
              <button
                type="button"
                class="password-toggle"
                id="confirmPasswordToggle">
                <i class="fas fa-eye"></i>
              </button>
            </div>
          </div>

          <button type="submit" class="signin-button" id="signupButton">Sign Up</button>


          <p class="signup-text">
            Already have an account?
            <a href="sign-in.php" class="signup-link">Sign in</a>
          </p>
      </div>
    </div>
  </div>


  <script>
    document.getElementById("signupForm").addEventListener("submit", function(e) {
      e.preventDefault();

      const formData = new FormData(this);

      fetch("sign-up.php", {
          method: "POST",
          body: formData,
        })
        .then((response) => response.json())
        .then((data) => {
          if (data.status === "success") {
            Swal.fire({
              icon: "success",
              title: "Registration Successful",
              text: "You have successfully registered!",
              confirmButtonText: "OK",
            }).then(() => {
              window.location.href = "sign-in.php";
            });
          } else if (data.status === "error") {
            Swal.fire({
              icon: "error",
              title: data.message.includes("User") ?
                "User Already Exists" : "Registration Failed",
              text: data.message,
              confirmButtonText: "OK",
            });
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          Swal.fire({
            icon: "error",
            title: "Registration Failed",
            text: "Please try again later.",
            confirmButtonText: "OK",
          });
        });
    });
  </script>

  <script src="../scripts/auth.js"></script>
</body>

</html>