// Password visibility toggle function
function setupPasswordToggle(inputId, toggleId) {
  const input = document.getElementById(inputId);
  const toggle = document.getElementById(toggleId);
  if (!input || !toggle) return;

  const toggleIcon = toggle.querySelector("i");

  toggle.addEventListener("click", function () {
    if (input.type === "password") {
      input.type = "text";
      toggleIcon.classList.remove("fa-eye");
      toggleIcon.classList.add("fa-eye-slash");
    } else {
      input.type = "password";
      toggleIcon.classList.remove("fa-eye-slash");
      toggleIcon.classList.add("fa-eye");
    }
  });
}

// Setup password toggles
setupPasswordToggle("password", "passwordToggle");
setupPasswordToggle("confirmPassword", "confirmPasswordToggle");

// // Signup form submission
// const signupForm = document.getElementById('signupForm');
// if (signupForm) {
//     signupForm.addEventListener('submit', function (e) {
//         e.preventDefault();

//         const fullname = document.getElementById('fullname').value;
//         const email = document.getElementById('email').value;
//         const password = document.getElementById('password').value;
//         const confirmPassword = document.getElementById('confirmPassword').value;

//         if (password !== confirmPassword) {
//             alert('Passwords do not match!');
//             return;
//         }

//         console.log('Sign up form submitted:', {
//             fullname,
//             email,
//             password,
//             confirmPassword
//         });
//         // Here you would typically send the data to your server
//         alert('Sign up form submitted!');
//     });
// }

// // Login form submission
// const loginForm = document.getElementById('loginForm');
// if (loginForm) {
//     loginForm.addEventListener('submit', function (e) {
//         e.preventDefault();

//         const email = document.getElementById('email').value;
//         const password = document.getElementById('password').value;
//         const remember = document.getElementById('remember')?.checked || false;

//         console.log('Login form submitted:', { email, password, remember });
//         // Here you would typically send the data to your server
//         alert('Login form submitted!');
//     });
// }
