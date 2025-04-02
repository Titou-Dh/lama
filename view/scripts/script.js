// Lamma Event Management App JavaScript

// Toggle user menu
document.addEventListener("DOMContentLoaded", () => {
    const userMenuButton = document.getElementById("userMenuButton")
    const userMenu = document.getElementById("userMenu")
    
    if (userMenuButton && userMenu) {
      userMenuButton.addEventListener("click", () => {
        userMenu.classList.toggle("hidden")
      })
    
      // Close menu when clicking outside
      document.addEventListener("click", (event) => {
        if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
          userMenu.classList.add("hidden")
        }
      })
    }
    
    // Mobile menu toggle
    const mobileMenuButton = document.getElementById("mobileMenuButton")
    const mobileMenu = document.getElementById("mobileMenu")
    
    if (mobileMenuButton && mobileMenu) {
      mobileMenuButton.addEventListener("click", () => {
        mobileMenu.classList.toggle("hidden")
      })
    }
    
    // Initialize Bootstrap carousels with custom options
    const carousels = document.querySelectorAll(".carousel")
    carousels.forEach((carousel) => {
      const bsCarousel = new bootstrap.Carousel(carousel, {
        interval: 5000,
        wrap: true,
        touch: true,
      })
    })
    
    // Add animation to cards on scroll
    const animateOnScroll = () => {
      const cards = document.querySelectorAll(".card")
      cards.forEach((card) => {
        const cardPosition = card.getBoundingClientRect().top
        const screenPosition = window.innerHeight / 1.3
    
        if (cardPosition < screenPosition) {
          card.classList.add("animate__animated", "animate__fadeInUp")
        }
      })
    }
    
    // Call on scroll
    window.addEventListener("scroll", animateOnScroll)
    
    // Call once on load
    animateOnScroll()
    })
    
    // Form validation for create event page
    const createEventForm = document.getElementById("createEventForm")
    if (createEventForm) {
    createEventForm.addEventListener("submit", (event) => {
      event.preventDefault()
    
      let isValid = true
      const title = document.getElementById("eventTitle")
      const venue = document.getElementById("eventVenue")
      const date = document.getElementById("eventDate")
    
      // Simple validation
      if (!title.value.trim()) {
        showError(title, "Event title is required")
        isValid = false
      } else {
        clearError(title)
      }
    
      if (!venue.value.trim()) {
        showError(venue, "Venue is required")
        isValid = false
      } else {
        clearError(venue)
      }
    
      if (!date.value) {
        showError(date, "Date is required")
        isValid = false
      } else {
        clearError(date)
      }
    
      if (isValid) {
        // Show success message
        const successMessage = document.createElement("div")
        successMessage.className = "alert alert-success mt-3"
        successMessage.textContent = "Event created successfully!"
        createEventForm.appendChild(successMessage)
    
        // Reset form
        setTimeout(() => {
          createEventForm.reset()
          successMessage.remove()
        }, 3000)
      }
    })
    
    function showError(input, message) {
      const formGroup = input.parentElement
      const errorElement = formGroup.querySelector(".error-message") || document.createElement("div")
      errorElement.className = "error-message text-danger mt-1 text-sm"
      errorElement.textContent = message
    
      if (!formGroup.querySelector(".error-message")) {
        formGroup.appendChild(errorElement)
      }
    
      input.classList.add("border-danger")
    }
    
    function clearError(input) {
      const formGroup = input.parentElement
      const errorElement = formGroup.querySelector(".error-message")
    
      if (errorElement) {
        errorElement.remove()
      }
    
      input.classList.remove("border-danger")
    }
    }