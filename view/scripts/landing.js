function initNavbarBlurEffect() {
  const navbar = document.getElementById("mainNav");

  if (!navbar) {
    console.error("Navbar element not found");
    return;
  }

  function handleScroll() {
    if (window.scrollY > 20) {
      navbar.classList.add("navbar-scrolled ");
      navbar.classList.add("fixed-top");
    } else {
      navbar.classList.add("navbar-scrolled");
      navbar.classList.add("fixed-top");
    }
  }

  window.addEventListener("scroll", handleScroll);

  handleScroll();
}

document.addEventListener("DOMContentLoaded", initNavbarBlurEffect);
