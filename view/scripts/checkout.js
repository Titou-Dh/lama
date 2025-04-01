// Lamma Event Management - Checkout Flow JavaScript

document.addEventListener("DOMContentLoaded", () => {
    // Elements
    const progressBar = document.getElementById("progressBar")
    const attendeeStep = document.getElementById("attendeeStep")
    const attendeeText = document.getElementById("attendeeText")
    const paymentStep = document.getElementById("paymentStep")
    const paymentText = document.getElementById("paymentText")
    const confirmationStep = document.getElementById("confirmationStep")
    const confirmationText = document.getElementById("confirmationText")
    
    // Forms
    const ticketStepForm = document.getElementById("ticketStep")
    const attendeeForm = document.getElementById("attendeeForm")
    const paymentForm = document.getElementById("paymentForm")
    const confirmationSection = document.getElementById("confirmationStep")
    
    // Buttons
    const continueToAttendee = document.getElementById("continueToAttendee")
    const backToTickets = document.getElementById("backToTickets")
    const continueToPayment = document.getElementById("continueToPayment")
    const backToAttendee = document.getElementById("backToAttendee")
    const completePayment = document.getElementById("completePayment")
    
    // Ticket quantity controls
    const ticketIncreaseButtons = document.querySelectorAll(".ticket-increase")
    const ticketDecreaseButtons = document.querySelectorAll(".ticket-decrease")
    const ticketQuantityInputs = document.querySelectorAll(".ticket-quantity")
    
    // Payment method options
    const paymentOptions = document.querySelectorAll(".payment-option")
    const cardPaymentForm = document.getElementById("cardPaymentForm")
    const paypalForm = document.getElementById("paypalForm")
    const applepayForm = document.getElementById("applepayForm")
    
    // Delivery options
    const deliveryOptions = document.querySelectorAll(".delivery-option")
    
    // Step navigation
    if (continueToAttendee) {
      continueToAttendee.addEventListener("click", () => {
        ticketStepForm.classList.add("hidden")
        attendeeForm.classList.remove("hidden")
        paymentForm.classList.add("hidden")
        confirmationSection.classList.add("hidden")
    
        // Update progress bar and steps
        progressBar.style.width = "50%"
        attendeeStep.classList.remove("bg-gray-200", "text-gray-500")
        attendeeStep.classList.add("bg-blue-500", "text-white")
        attendeeText.classList.remove("text-gray-500")
        attendeeText.classList.add("text-blue-500")
    
        // Update additional attendees based on ticket quantities
        updateAdditionalAttendees()
      })
    }
    
    if (backToTickets) {
      backToTickets.addEventListener("click", () => {
        ticketStepForm.classList.remove("hidden")
        attendeeForm.classList.add("hidden")
    
        // Update progress bar and steps
        progressBar.style.width = "25%"
        attendeeStep.classList.remove("bg-blue-500", "text-white")
        attendeeStep.classList.add("bg-gray-200", "text-gray-500")
        attendeeText.classList.remove("text-blue-500")
        attendeeText.classList.add("text-gray-500")
      })
    }
    
    if (continueToPayment) {
      continueToPayment.addEventListener("click", () => {
        // Simple validation
        const firstName = document.getElementById("firstName")
        const lastName = document.getElementById("lastName")
        const email = document.getElementById("email")
    
        if (!firstName.value.trim() || !lastName.value.trim() || !email.value.trim()) {
          alert("Please fill in all required fields.")
          return
        }
    
        attendeeForm.classList.add("hidden")
        paymentForm.classList.remove("hidden")
    
        // Update progress bar and steps
        progressBar.style.width = "75%"
        paymentStep.classList.remove("bg-gray-200", "text-gray-500")
        paymentStep.classList.add("bg-blue-500", "text-white")
        paymentText.classList.remove("text-gray-500")
        paymentText.classList.add("text-blue-500")
      })
    }
    
    if (backToAttendee) {
      backToAttendee.addEventListener("click", () => {
        attendeeForm.classList.remove("hidden")
        paymentForm.classList.add("hidden")
    
        // Update progress bar and steps
        progressBar.style.width = "50%"
        paymentStep.classList.remove("bg-blue-500", "text-white")
        paymentStep.classList.add("bg-gray-200", "text-gray-500")
        paymentText.classList.remove("text-blue-500")
        paymentText.classList.add("text-gray-500")
      })
    }
    
    if (completePayment) {
      completePayment.addEventListener("click", () => {
        // Simple validation for card payment
        const activePayment = document.querySelector(".payment-option.active").dataset.payment
    
        if (activePayment === "card") {
          const cardNumber = document.getElementById("cardNumber")
          const expiryDate = document.getElementById("expiryDate")
          const cvv = document.getElementById("cvv")
          const cardName = document.getElementById("cardName")
    
          if (!cardNumber.value.trim() || !expiryDate.value.trim() || !cvv.value.trim() || !cardName.value.trim()) {
            alert("Please fill in all payment details.")
            return
          }
        }
    
        paymentForm.classList.add("hidden")
        confirmationSection.classList.remove("hidden")
    
        // Update progress bar and steps
        progressBar.style.width = "100%"
        confirmationStep.classList.remove("bg-gray-200", "text-gray-500")
        confirmationStep.classList.add("bg-blue-500", "text-white")
        confirmationText.classList.remove("text-gray-500")
        confirmationText.classList.add("text-blue-500")
      })
    }
    
    // Ticket quantity controls
    ticketIncreaseButtons.forEach((button) => {
      button.addEventListener("click", function () {
        const ticketType = this.dataset.ticketType
        const input = document.querySelector(`.ticket-quantity[data-ticket-type="${ticketType}"]`)
        const currentValue = Number.parseInt(input.value)
    
        if (currentValue < Number.parseInt(input.max)) {
          input.value = currentValue + 1
          updateTicketSummary()
        }
      })
    })
    
    ticketDecreaseButtons.forEach((button) => {
      button.addEventListener("click", function () {
        const ticketType = this.dataset.ticketType
        const input = document.querySelector(`.ticket-quantity[data-ticket-type="${ticketType}"]`)
        const currentValue = Number.parseInt(input.value)
    
        if (currentValue > Number.parseInt(input.min)) {
          input.value = currentValue - 1
          updateTicketSummary()
        }
      })
    })
    
    ticketQuantityInputs.forEach((input) => {
      input.addEventListener("change", () => {
        updateTicketSummary()
      })
    })
    
    // Payment method selection
    paymentOptions.forEach((option) => {
      option.addEventListener("click", function () {
        // Remove active class from all options
        paymentOptions.forEach((opt) => {
          opt.classList.remove("active")
          const radio = opt.querySelector("div.w-5")
          radio.classList.remove("border-blue-500")
          radio.classList.add("border-gray-300")
          const innerCircle = radio.querySelector("div")
          innerCircle.classList.remove("bg-blue-500")
          innerCircle.classList.add("bg-white")
        })
    
        // Add active class to selected option
        this.classList.add("active")
        const radio = this.querySelector("div.w-5")
        radio.classList.remove("border-gray-300")
        radio.classList.add("border-blue-500")
        const innerCircle = radio.querySelector("div")
        innerCircle.classList.remove("bg-white")
        innerCircle.classList.add("bg-blue-500")
    
        // Show/hide appropriate payment form
        const paymentType = this.dataset.payment
        cardPaymentForm.classList.add("hidden")
        paypalForm.classList.add("hidden")
        applepayForm.classList.add("hidden")
    
        if (paymentType === "card") {
          cardPaymentForm.classList.remove("hidden")
        } else if (paymentType === "paypal") {
          paypalForm.classList.remove("hidden")
        } else if (paymentType === "applepay") {
          applepayForm.classList.remove("hidden")
        }
      })
    })
    
    // Delivery option selection
    deliveryOptions.forEach((option) => {
      option.addEventListener("click", function () {
        // Remove active class from all options
        deliveryOptions.forEach((opt) => {
          opt.classList.remove("active")
          const radio = opt.querySelector("div.w-5")
          radio.classList.remove("border-blue-500")
          radio.classList.add("border-gray-300")
          const innerCircle = radio.querySelector("div")
          innerCircle.classList.remove("bg-blue-500")
          innerCircle.classList.add("bg-white")
        })
    
        // Add active class to selected option
        this.classList.add("active")
        const radio = this.querySelector("div.w-5")
        radio.classList.remove("border-gray-300")
        radio.classList.add("border-blue-500")
        const innerCircle = radio.querySelector("div")
        innerCircle.classList.remove("bg-white")
        innerCircle.classList.add("bg-blue-500")
      })
    })
    
    // Update ticket summary
    function updateTicketSummary() {
      let subtotal = 0
    
      // General tickets
      const generalTickets = document.querySelector('.ticket-quantity[data-ticket-type="general"]')
      const generalCount = Number.parseInt(generalTickets.value)
      const generalPrice = Number.parseFloat(generalTickets.dataset.price)
      const generalTotal = generalCount * generalPrice
      subtotal += generalTotal
    
      const generalTicketLine = document.getElementById("generalTicketLine")
      if (generalCount > 0) {
        generalTicketLine.classList.remove("hidden")
        generalTicketLine.querySelector(".ticket-count").textContent = generalCount
        generalTicketLine.querySelector(".ticket-price").textContent = "$" + generalTotal.toFixed(2)
      } else {
        generalTicketLine.classList.add("hidden")
      }
    
      // VIP tickets
      const vipTickets = document.querySelector('.ticket-quantity[data-ticket-type="vip"]')
      const vipCount = Number.parseInt(vipTickets.value)
      const vipPrice = Number.parseFloat(vipTickets.dataset.price)
      const vipTotal = vipCount * vipPrice
      subtotal += vipTotal
    
      const vipTicketLine = document.getElementById("vipTicketLine")
      if (vipCount > 0) {
        vipTicketLine.classList.remove("hidden")
        vipTicketLine.querySelector(".ticket-count").textContent = vipCount
        vipTicketLine.querySelector(".ticket-price").textContent = "$" + vipTotal.toFixed(2)
      } else {
        vipTicketLine.classList.add("hidden")
      }
    
      // Workshop tickets
      const workshopTickets = document.querySelector('.ticket-quantity[data-ticket-type="workshop"]')
      const workshopCount = Number.parseInt(workshopTickets.value)
      const workshopPrice = Number.parseFloat(workshopTickets.dataset.price)
      const workshopTotal = workshopCount * workshopPrice
      subtotal += workshopTotal
    
      const workshopTicketLine = document.getElementById("workshopTicketLine")
      if (workshopCount > 0) {
        workshopTicketLine.classList.remove("hidden")
        workshopTicketLine.querySelector(".ticket-count").textContent = workshopCount
        workshopTicketLine.querySelector(".ticket-price").textContent = "$" + workshopTotal.toFixed(2)
      } else {
        workshopTicketLine.classList.add("hidden")
      }
    
      // Update totals
      document.getElementById("subtotal").textContent = "$" + subtotal.toFixed(2)
    
      const serviceFee = subtotal * 0.1 // 10% service fee
      document.getElementById("serviceFee").textContent = "$" + serviceFee.toFixed(2)
    
      const tax = subtotal * 0.09 // 9% tax
      document.getElementById("tax").textContent = "$" + tax.toFixed(2)
    
      const total = subtotal + serviceFee + tax
      document.getElementById("totalPrice").textContent = "$" + total.toFixed(2)
    }
    
    // Update additional attendees based on ticket quantities
    function updateAdditionalAttendees() {
      const additionalAttendeesContainer = document.getElementById("additionalAttendees")
      additionalAttendeesContainer.innerHTML = ""
    
      let totalTickets = 0
      ticketQuantityInputs.forEach((input) => {
        totalTickets += Number.parseInt(input.value)
      })
    
      // Subtract 1 for the primary attendee
      const additionalTickets = Math.max(0, totalTickets - 1)
    
      if (additionalTickets > 0) {
        const heading = document.createElement("h3")
        heading.className = "font-bold mt-6 mb-3"
        heading.textContent = "Additional Attendees"
        additionalAttendeesContainer.appendChild(heading)
    
        for (let i = 0; i < additionalTickets; i++) {
          const attendeeHtml = `
            <div class="border rounded-lg p-4 mb-4">
              <h4 class="font-medium mb-3">Attendee ${i + 2}</h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                  <label class="form-label">First Name</label>
                  <input type="text" class="form-control" required>
                </div>
                <div class="form-group">
                  <label class="form-label">Last Name</label>
                  <input type="text" class="form-control" required>
                </div>
              </div>
              <div class="form-group mt-3">
                <label class="form-label">Email Address</label>
                <input type="email" class="form-control" required>
              </div>
            </div>
          `
    
          additionalAttendeesContainer.innerHTML += attendeeHtml
        }
      }
    }
    
    // Initialize
    updateTicketSummary()
    })