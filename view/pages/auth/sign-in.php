<?php
session_start();

require_once __DIR__ . '/../../../config/google.php';
include_once __DIR__ . '/../../../config/database.php';
include_once __DIR__ . '/../../../controller/auth.php';



if (!empty($_POST) && isset($_POST['email'], $_POST['password'])) {
  $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
  $password = $_POST['password'];

  $authResult = loginUser($cnx, $email, $password);

  if ($authResult === AuthResult::SUCCESS) {
    $stmt = $cnx->prepare("SELECT id, full_name FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
      $_SESSION['user'] = $email;
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['user_full_name'] = $user['full_name'];
      echo json_encode(['status' => 'success', 'message' => 'Login successful!', 'ok' => true]);
    } else {
      echo json_encode(['status' => 'error', 'message' => 'User data retrieval failed', 'ok' => false]);
    }
  } elseif ($authResult === AuthResult::INVALID_CREDENTIALS) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email or password!', 'ok' => false]);
  } else {
    echo json_encode(['status' => 'error', 'message' => 'Login failed!', 'ok' => false]);
  }
  exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Event Login Page</title>
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="../../styles/css/auth.css" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="../../scripts/tailwind.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>
  <div class="auth-container">
    <!-- Left Section - Event Image and Text -->
    <div class="left-section hidden lg:block">
      <img
        src="../../assets/images/auth-img-v2.png"
        alt="Concert event with blue lighting and crowd"
        class="bg-image" />
      <div class="content">
        <h1 class="title">
          Start Your <br />
          Journey <br />with Us
        </h1>
        <div class="footer">
          <img class="logo" src="../../assets/images/logo.png" alt="Logo" />
          <p class="tagline">Lama – Uniting Events, Elevating Experiences!</p>
        </div>
      </div>
    </div>

    <!-- Right Section - Login Form -->
    <div class="right-section w-full lg:w-1/2">
      <div class="form-container">
        <div class="form-header">
          <h2>Welcome Back</h2>
          <p>Sign in to your account</p>
        </div>

        <form id="loginForm" method="POST">
          <div class="form-group">
            <label for="email">Email</label>
            <div class="input-container">
              <i class="fas fa-envelope input-icon"></i>
              <input
                type="email"
                id="email"
                name="email"
                placeholder="domat@example.com"
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

          <!-- Remember me & Forgot password -->
          <div class="remember-forgot">
            <div class="remember">
              <input type="checkbox" id="remember" />
              <label for="remember">Remember me</label>
            </div>
            <a href="#" class="forgot-link">Forgot Password</a>
          </div>

          <!-- Sign in button -->
          <button type="submit" class="signin-button">Sign in</button>

          <div class="divider">
            <span>or</span>
          </div>

          <!-- Google sign in -->
          <a href="<?php echo $client->createAuthUrl(); ?>" class="google-button">
            <span class="google-icon">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 48 48"
                width="24px"
                height="24px">
                <path
                  fill="#FFC107"
                  d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z" />
                <path
                  fill="#FF3D00"
                  d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z" />
                <path
                  fill="#4CAF50"
                  d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z" />
                <path
                  fill="#1976D2"
                  d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z" />
              </svg>
            </span>
            Sign in with Google
          </a>
        </form>

        <p class="signup-text">
          Don't have an account?
          <a href="./sign-up.php" class="signup-link">Sign up</a>
        </p>
      </div>
    </div>
  </div>


  <script>
    document.getElementById("loginForm").addEventListener("submit", function(e) {
      e.preventDefault();

      const formData = new FormData(this);


      fetch("sign-in.php", {
          method: "POST",
          body: formData,
        })
        .then((response) => response.json())
        .then((data) => {
          if (data.status === 'success') {
            // Swal.fire({
            //   icon: "success",
            //   title: "Login Successful",
            //   text: data.message,
            //   confirmButtonText: "OK",
            // }).then(() => {
            window.location.href = "/lama/view/pages/landing-page.php";
            // });
          } else {
            Swal.fire({
              icon: "error",
              title: "Login Failed",
              text: data.message,
              confirmButtonText: "OK",
            });
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          Swal.fire({
            icon: "error",
            title: "Connection Error",
            text: "Failed to connect to the server. Please try again later.",
            confirmButtonText: "OK",
          });
        });
    });
  </script>
  <script src="../../scripts/auth.js"></script>
</body>

</html>