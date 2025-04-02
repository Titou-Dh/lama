// Lamma Event Management - Event Details JavaScript

document.addEventListener("DOMContentLoaded", () => {
    // Share functionality
    const shareBtn = document.getElementById("shareBtn")
    const shareModal = document.getElementById("shareModal")
    const closeShareModal = document.getElementById("closeShareModal")
  
    if (shareBtn && shareModal && closeShareModal) {
      shareBtn.addEventListener("click", () => {
        shareModal.classList.remove("hidden")
      })
  
      closeShareModal.addEventListener("click", () => {
        shareModal.classList.add("hidden")
      })
  
      // Close modal when clicking outside
      window.addEventListener("click", (event) => {
        if (event.target === shareModal) {
          shareModal.classList.add("hidden")
        }
      })
    }
  
    // Save/Bookmark event
    const saveBtn = document.getElementById("saveBtn")
    if (saveBtn) {
      saveBtn.addEventListener("click", function () {
        const icon = this.querySelector("i")
        if (icon.classList.contains("far")) {
          // Save event
          icon.classList.remove("far")
          icon.classList.add("fas")
          this.querySelector("span").textContent = "Saved"
        } else {
          // Unsave event
          icon.classList.remove("fas")
          icon.classList.add("far")
          this.querySelector("span").textContent = "Save"
        }
      })
    }
  
    // Read more functionality
    const readMoreBtn = document.getElementById("readMoreBtn")
    if (readMoreBtn) {
      const aboutSection = readMoreBtn.closest("div").querySelector(".prose")
      let isExpanded = false
  
      // Initially limit the height
      aboutSection.style.maxHeight = "200px"
      aboutSection.style.overflow = "hidden"
  
      readMoreBtn.addEventListener("click", () => {
        if (!isExpanded) {
          // Expand
          aboutSection.style.maxHeight = "none"
          readMoreBtn.querySelector("span").textContent = "Read less"
          readMoreBtn.querySelector("i").classList.remove("fa-chevron-down")
          readMoreBtn.querySelector("i").classList.add("fa-chevron-up")
        } else {
          // Collapse
          aboutSection.style.maxHeight = "200px"
          readMoreBtn.querySelector("span").textContent = "Read more"
          readMoreBtn.querySelector("i").classList.remove("fa-chevron-up")
          readMoreBtn.querySelector("i").classList.add("fa-chevron-down")
        }
        isExpanded = !isExpanded
      })
    }
  
    // Copy link functionality
    const copyLinkBtn = document.querySelector(".btn-outline-primary")
    if (copyLinkBtn) {
      copyLinkBtn.addEventListener("click", () => {
        const linkInput = document.querySelector(".form-control")
        linkInput.select()
        document.execCommand("copy")
  
        // Show feedback
        const originalText = copyLinkBtn.textContent
        copyLinkBtn.textContent = "Copied!"
  
        setTimeout(() => {
          copyLinkBtn.textContent = originalText
        }, 2000)
      })
    }
  
    // "Add to Calendar" functionality
    const addToCalendarBtn = document.querySelector(".card-body .btn-outline-primary:nth-child(2)")
    if (addToCalendarBtn) {
      addToCalendarBtn.addEventListener("click", () => {
        // Create calendar data
        const eventDetails = {
          title: "Tech Conference 2025",
          start: "20250515T090000",
          end: "20250517T180000",
          location: "San Francisco Convention Center",
          details: "Join us for the biggest tech conference of the year.",
        }
  
        // In a real implementation, you would create proper calendar links (Google, Outlook, iCal)
        alert("Calendar integration would be implemented here!")
      })
    }
  
    // Initialize ticket buttons
    const ticketButtons = document.querySelectorAll(".btn-primary")
    ticketButtons.forEach((button) => {
      if (button.textContent.includes("Get Tickets")) {
        button.addEventListener("click", () => {
          window.location.href = "checkout.html"
        })
      }
    })
  
    // Register Now button
    const registerNowBtn = document.querySelector(".card .btn-primary")
    if (registerNowBtn) {
      registerNowBtn.addEventListener("click", () => {
        window.location.href = "checkout.html"
      })
    }
  })
  
  