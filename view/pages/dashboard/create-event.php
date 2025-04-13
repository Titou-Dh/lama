<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Event - EventLama</title>

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../styles/css/createevent2.css">
  <script src="../../scripts/tailwind.js"></script>

</head>

<body>



  <main>
    <!-- Content -->
    <div class="container py-4">
      <a href="/lama/view/pages/dashboard" class="px-2 py-2 border border-primary w-fit d-flex align-items-center gap-2 rounded-md btn btn-outline-primary mb-5">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left-icon lucide-arrow-left">
          <path d="m12 19-7-7 7-7" />
          <path d="M19 12H5" />
        </svg>
        Go back to Dashboard
      </a>
      <div class="row">
        <div class="col-12">
          <h1 class="text-3xl font-bold mb-4 text-gradient">Create Your Event</h1>

          <!-- Form Progress -->
          <div class="form-progress bg-gradient-light">
            <div class="progress-step active">
              <div class="progress-number">1</div>
              <div class="progress-label">Basic Info</div>
            </div>
            <div class="progress-step">
              <div class="progress-number">2</div>
              <div class="progress-label">Details</div>
            </div>
            <div class="progress-step">
              <div class="progress-number">3</div>
              <div class="progress-label">Tickets</div>
            </div>
            <div class="progress-step">
              <div class="progress-number">4</div>
              <div class="progress-label">Publish</div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-8">
          <!-- Event Images Section -->
          <div class="form-section">
            <h3 class="text-gradient">Event Images</h3>
            <p class="text-gray-500 mb-4">Upload eye-catching images to attract attendees. You can upload up to 10 images.</p>

            <div class="image-slider mb-4">
              <div class="slider-images" id="sliderImages">
                <div class="slider-image" style="background-image: url('https://via.placeholder.com/800x400/3b82f6/ffffff?text=Upload+Event+Image')"></div>
              </div>
              <div class="slider-controls" id="sliderControls">
                <div class="slider-dot active"></div>
              </div>
            </div>

            <div class="image-upload-container" id="imageUpload">
              <i class="fas fa-cloud-upload-alt text-4xl text-primary-purple mb-2"></i>
              <p class="mb-1">Drag and drop your images here</p>
              <p class="text-sm text-gray-500 mb-3">or</p>
              <button class="btn btn-gradient rounded-pill px-4">Browse Files</button>
              <input type="file" id="imageInput" class="d-none" accept="image/*" multiple>
            </div>
          </div>

          <!-- Basic Info Section -->
          <div class="form-section">
            <h3 class="text-gradient">Basic Information</h3>

            <div class="mb-4">
              <label for="eventTitle" class="form-label font-medium">Event Title <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="eventTitle" placeholder="Keep it short and sweet">
              <div class="form-text">Attendees will see this as the event name. Example: "Summer Music Festival 2025"</div>
            </div>

            <div class="mb-4">
              <label for="eventDescription" class="form-label font-medium">Event Description <span class="text-danger">*</span></label>
              <textarea class="form-control" id="eventDescription" rows="4" placeholder="Tell people what your event is about"></textarea>
              <div class="form-text">Provide a compelling description that will make people want to attend.</div>
            </div>

            <div class="row mb-4">
              <div class="col-md-6">
                <label class="form-label font-medium">Event Date <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text bg-primary-light text-primary-dark"><i class="far fa-calendar-alt"></i></span>
                  <input type="date" class="form-control" id="eventDate">
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label font-medium">Event Time <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text bg-secondary-light text-secondary-purple"><i class="far fa-clock"></i></span>
                  <input type="time" class="form-control" id="eventTime">
                </div>
              </div>
            </div>

            <div class="mb-4 p-3 rounded bg-gradient-light">
              <label class="form-label font-medium">Event Type <span class="text-danger">*</span></label>
              <div class="d-flex">
                <div class="form-check me-4">
                  <input class="form-check-input" type="radio" name="eventType" id="inPersonEvent" checked>
                  <label class="form-check-label" for="inPersonEvent">
                    In-person
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="eventType" id="onlineEvent">
                  <label class="form-check-label" for="onlineEvent">
                    Online
                  </label>
                </div>
              </div>
            </div>

            <div class="mb-4" id="locationSection">
              <label for="eventLocation" class="form-label font-medium">Location <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text bg-primary-light text-primary-dark"><i class="fas fa-map-marker-alt"></i></span>
                <input type="text" class="form-control" id="eventLocation" placeholder="Enter venue name or address">
              </div>
            </div>

            <div class="mb-4 d-none" id="onlineSection">
              <label for="eventLink" class="form-label font-medium">Online Event Link <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text bg-secondary-light text-secondary-purple"><i class="fas fa-link"></i></span>
                <input type="text" class="form-control" id="eventLink" placeholder="https://zoom.us/j/123456789">
              </div>
              <div class="form-text">You can add a Zoom, Google Meet, or any other video conferencing link.</div>
            </div>
          </div>

          <!-- Event Details Section -->
          <div class="form-section">
            <h3 class="text-gradient">Event Details</h3>

            <div class="mb-4">
              <label for="eventCategory" class="form-label font-medium">Category <span class="text-danger">*</span></label>
              <select class="form-select" id="eventCategory">
                <option selected disabled>Select a category</option>
                <option>Music</option>
                <option>Business</option>
                <option>Food & Drink</option>
                <option>Community</option>
                <option>Arts</option>
                <option>Sports & Fitness</option>
                <option>Health</option>
                <option>Science & Technology</option>
                <option>Travel & Outdoor</option>
                <option>Charity & Causes</option>
                <option>Spirituality</option>
                <option>Family & Education</option>
                <option>Holiday</option>
                <option>Fashion</option>
                <option>Home & Lifestyle</option>
                <option>Auto, Boat & Air</option>
                <option>Hobbies</option>
                <option>Other</option>
              </select>
            </div>

            <div class="mb-4 p-3 rounded bg-gradient-light">
              <label class="form-label font-medium">Age Restrictions</label>
              <select class="form-select" id="ageRestriction">
                <option selected>All Ages</option>
                <option>18+</option>
                <option>21+</option>
                <option>Custom</option>
              </select>
            </div>

            <div class="mb-4">
              <label for="doorTime" class="form-label font-medium">Door Time</label>
              <div class="input-group">
                <span class="input-group-text bg-primary-light text-primary-dark"><i class="far fa-clock"></i></span>
                <input type="time" class="form-control" id="doorTime">
              </div>
              <div class="form-text">When should attendees arrive? (e.g., 30 minutes before start time)</div>
            </div>

            <div class="mb-4">
              <label for="capacity" class="form-label font-medium">Capacity</label>
              <input type="number" class="form-control" id="capacity" placeholder="Maximum number of attendees">
              <div class="form-text">Leave blank for unlimited capacity.</div>
            </div>
          </div>

          <!-- FAQ Section -->
          <div class="form-section">
            <h3 class="text-gradient">Frequently Asked Questions</h3>
            <p class="text-gray-500 mb-4">Add FAQs to provide additional information to attendees.</p>

            <div id="faqContainer">
              <div class="faq-item mb-3 p-3 border rounded">
                <div class="mb-3">
                  <label class="form-label font-medium">Question</label>
                  <input type="text" class="form-control" placeholder="E.g., Is there parking available?">
                </div>
                <div>
                  <label class="form-label font-medium">Answer</label>
                  <textarea class="form-control" rows="2" placeholder="Provide a clear answer"></textarea>
                </div>
              </div>
            </div>

            <button class="btn btn-outline-primary mt-2" id="addFaqBtn">
              <i class="fas fa-plus me-2"></i> Add Another FAQ
            </button>
          </div>

          <!-- Tickets Section -->
          <div class="form-section">
            <h3 class="text-gradient">Tickets</h3>
            <p class="text-gray-500 mb-4">Set up tickets for your event. You can create multiple ticket types.</p>

            <div id="ticketContainer">
              <div class="ticket-item mb-3 p-3 border rounded">
                <div class="mb-3">
                  <label class="form-label font-medium">Ticket Name</label>
                  <input type="text" class="form-control" placeholder="E.g., General Admission, VIP, Early Bird">
                </div>
                <div class="row mb-3">
                  <div class="col-md-6">
                    <label class="form-label font-medium">Price</label>
                    <div class="input-group">
                      <span class="input-group-text bg-primary-light text-primary-dark">$</span>
                      <input type="number" class="form-control" placeholder="0.00">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label font-medium">Quantity</label>
                    <input type="number" class="form-control" placeholder="Number of tickets available">
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <label class="form-label font-medium">Sale Start</label>
                    <input type="date" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label font-medium">Sale End</label>
                    <input type="date" class="form-control">
                  </div>
                </div>
              </div>
            </div>

            <button class="btn btn-outline-primary mt-2" id="addTicketBtn">
              <i class="fas fa-plus me-2"></i> Add Another Ticket
            </button>
          </div>

          <!-- Promo Codes Section -->
          <div class="form-section">
            <h3 class="text-gradient">Promo Codes</h3>
            <p class="text-gray-500 mb-4">Create promotional codes to offer discounts on tickets.</p>

            <div id="promoContainer">
              <div class="promo-item mb-3 p-3 border rounded">
                <div class="mb-3">
                  <label class="form-label font-medium">Code</label>
                  <input type="text" class="form-control" placeholder="E.g., SUMMER25, EARLYBIRD">
                </div>
                <div class="row mb-3">
                  <div class="col-md-6">
                    <label class="form-label font-medium">Discount Type</label>
                    <select class="form-select">
                      <option selected>Percentage</option>
                      <option>Fixed Amount</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label font-medium">Discount Value</label>
                    <div class="input-group">
                      <input type="number" class="form-control" placeholder="10">
                      <span class="input-group-text bg-primary-light text-primary-dark">%</span>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <label class="form-label font-medium">Valid From</label>
                    <input type="date" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label font-medium">Valid Until</label>
                    <input type="date" class="form-control">
                  </div>
                </div>
              </div>
            </div>

            <button class="btn btn-outline-primary mt-2" id="addPromoBtn">
              <i class="fas fa-plus me-2"></i> Add Another Promo Code
            </button>
          </div>

          <!-- Publish Button -->
          <div class="d-flex justify-content-between mt-4 mb-5 p-4 rounded bg-gradient-light">
            <button class="btn btn-outline-secondary rounded-pill px-4">Save Draft</button>
            <button class="btn btn-burgundy rounded-pill px-5 py-2">Publish Event</button>
          </div>
        </div>

        <!-- Preview Column -->
        <div class="col-lg-4">
          <div class="sticky-top" style="top: 100px;">
            <div class="preview-container">
              <h3 class="p-3 border-bottom bg-gradient-light">Event Preview</h3>
              <div class="preview-header" id="previewHeader" style="background-image: url('https://via.placeholder.com/800x400/3b82f6/ffffff?text=Event+Image')">
                <div class="preview-header-overlay">
                  <h2 class="text-xl font-bold" id="previewTitle">Your Event Title</h2>
                  <p class="text-sm" id="previewDate">Date and Time</p>
                </div>
              </div>
              <div class="preview-content">
                <div class="preview-section">
                  <h4><i class="fas fa-map-marker-alt me-2 text-primary-blue"></i> Location</h4>
                  <p id="previewLocation">Venue or Online</p>
                </div>
                <div class="preview-section">
                  <h4><i class="fas fa-info-circle me-2 text-primary-blue"></i> About</h4>
                  <p id="previewDescription" class="text-sm text-gray-600">Event description will appear here...</p>
                </div>
                <div class="preview-section">
                  <h4><i class="fas fa-ticket-alt me-2 text-primary-blue"></i> Tickets</h4>
                  <div id="previewTickets" class="text-sm text-gray-600">
                    <p>No tickets added yet</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="mt-4 text-center p-3 rounded bg-gradient-light">
              <p class="text-sm text-gray-500 mb-2">This is how your event will appear to attendees</p>
              <button class="btn btn-outline-primary btn-sm rounded-pill px-3" id="viewFullPreviewBtn">
                <i class="fas fa-external-link-alt me-1"></i> View Full Preview
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Full Preview Modal -->
  <div class="modal fade" id="fullPreviewModal" tabindex="-1" aria-labelledby="fullPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-fullscreen-lg-down">
      <div class="modal-content">
        <div class="modal-header bg-gradient-primary text-white">
          <h5 class="modal-title" id="fullPreviewModalLabel">Event Preview</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-0">
          <div class="full-preview-container">
            <!-- Event Header -->
            <div class="preview-header-full" id="fullPreviewHeader" style="background-image: url('https://via.placeholder.com/1200x600/3b82f6/ffffff?text=Event+Image')">
              <div class="preview-header-overlay-full">
                <div class="container py-5">
                  <h1 class="display-5 fw-bold text-white" id="fullPreviewTitle">Your Event Title</h1>
                  <p class="lead text-white" id="fullPreviewDate">Date and Time</p>
                  <button class="btn btn-light rounded-pill px-4 mt-3">Get Tickets</button>
                </div>
              </div>
            </div>

            <!-- Event Details -->
            <div class="container py-4">
              <div class="row">
                <div class="col-lg-8">
                  <!-- About Section -->
                  <div class="card mb-4">
                    <div class="card-body">
                      <h3 class="card-title"><i class="fas fa-info-circle me-2 text-primary-blue"></i> About This Event</h3>
                      <p class="card-text" id="fullPreviewDescription">Event description will appear here...</p>
                    </div>
                  </div>

                  <!-- FAQ Section -->
                  <div class="card mb-4">
                    <div class="card-body">
                      <h3 class="card-title"><i class="fas fa-question-circle me-2 text-primary-blue"></i> Frequently Asked Questions</h3>
                      <div id="fullPreviewFAQs">
                        <p class="text-muted">No FAQs added yet</p>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-lg-4">
                  <!-- Location Section -->
                  <div class="card mb-4">
                    <div class="card-body">
                      <h3 class="card-title"><i class="fas fa-map-marker-alt me-2 text-primary-blue"></i> Location</h3>
                      <p class="card-text" id="fullPreviewLocation">Venue or Online</p>
                      <div id="map" class="mt-3 bg-light" style="height: 200px; border-radius: 0.5rem;">
                        <div class="d-flex justify-content-center align-items-center h-100 text-muted">
                          <p><i class="fas fa-map me-2"></i> Map preview</p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Tickets Section -->
                  <div class="card mb-4">
                    <div class="card-body">
                      <h3 class="card-title"><i class="fas fa-ticket-alt me-2 text-primary-blue"></i> Tickets</h3>
                      <div id="fullPreviewTickets">
                        <p class="text-muted">No tickets added yet</p>
                      </div>
                    </div>
                  </div>

                  <!-- Organizer Section -->
                  <div class="card">
                    <div class="card-body">
                      <h3 class="card-title"><i class="fas fa-user me-2 text-primary-blue"></i> Organizer</h3>
                      <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-gradient-primary d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                          <i class="fas fa-user text-white"></i>
                        </div>
                        <div>
                          <h5 class="mb-0">John Doe</h5>
                          <p class="text-muted mb-0">Event Organizer</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>



  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Custom JavaScript -->
  <script>
    // Navbar scroll effect
    window.addEventListener('scroll', function() {
      const navbar = document.getElementById('mainNav');
      if (window.scrollY > 50) {
        navbar.classList.add('navbar-scrolled');
      } else {
        navbar.classList.remove('navbar-scrolled');
      }
    });

    // Toggle between in-person and online event
    document.getElementById('inPersonEvent').addEventListener('change', function() {
      document.getElementById('locationSection').classList.remove('d-none');
      document.getElementById('onlineSection').classList.add('d-none');
    });

    document.getElementById('onlineEvent').addEventListener('change', function() {
      document.getElementById('locationSection').classList.add('d-none');
      document.getElementById('onlineSection').classList.remove('d-none');
    });

    // Add FAQ button
    document.getElementById('addFaqBtn').addEventListener('click', function() {
      const faqContainer = document.getElementById('faqContainer');
      const faqItem = document.createElement('div');
      faqItem.className = 'faq-item mb-3 p-3 border rounded';
      faqItem.innerHTML = `
        <div class="d-flex justify-content-between mb-2">
          <h5 class="mb-0">New FAQ</h5>
          <button type="button" class="btn-close remove-faq"></button>
        </div>
        <div class="mb-3">
          <label class="form-label font-medium">Question</label>
          <input type="text" class="form-control" placeholder="E.g., Is there parking available?">
        </div>
        <div>
          <label class="form-label font-medium">Answer</label>
          <textarea class="form-control" rows="2" placeholder="Provide a clear answer"></textarea>
        </div>
      `;
      faqContainer.appendChild(faqItem);

      // Add remove functionality
      faqItem.querySelector('.remove-faq').addEventListener('click', function() {
        faqContainer.removeChild(faqItem);
      });
    });

    // Add Ticket button
    document.getElementById('addTicketBtn').addEventListener('click', function() {
      const ticketContainer = document.getElementById('ticketContainer');
      const ticketItem = document.createElement('div');
      ticketItem.className = 'ticket-item mb-3 p-3 border rounded';
      ticketItem.innerHTML = `
        <div class="d-flex justify-content-between mb-2">
          <h5 class="mb-0">New Ticket</h5>
          <button type="button" class="btn-close remove-ticket"></button>
        </div>
        <div class="mb-3">
          <label class="form-label font-medium">Ticket Name</label>
          <input type="text" class="form-control" placeholder="E.g., General Admission, VIP, Early Bird">
        </div>
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label font-medium">Price</label>
            <div class="input-group">
              <span class="input-group-text bg-primary-light text-primary-dark">$</span>
              <input type="number" class="form-control" placeholder="0.00">
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label font-medium">Quantity</label>
            <input type="number" class="form-control" placeholder="Number of tickets available">
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <label class="form-label font-medium">Sale Start</label>
            <input type="date" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label font-medium">Sale End</label>
            <input type="date" class="form-control">
          </div>
        </div>
      `;
      ticketContainer.appendChild(ticketItem);

      // Add remove functionality
      ticketItem.querySelector('.remove-ticket').addEventListener('click', function() {
        ticketContainer.removeChild(ticketItem);
      });
    });

    // Add Promo Code button
    document.getElementById('addPromoBtn').addEventListener('click', function() {
      const promoContainer = document.getElementById('promoContainer');
      const promoItem = document.createElement('div');
      promoItem.className = 'promo-item mb-3 p-3 border rounded';
      promoItem.innerHTML = `
        <div class="d-flex justify-content-between mb-2">
          <h5 class="mb-0">New Promo Code</h5>
          <button type="button" class="btn-close remove-promo"></button>
        </div>
        <div class="mb-3">
          <label class="form-label font-medium">Code</label>
          <input type="text" class="form-control" placeholder="E.g., SUMMER25, EARLYBIRD">
        </div>
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label font-medium">Discount Type</label>
            <select class="form-select">
              <option selected>Percentage</option>
              <option>Fixed Amount</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label font-medium">Discount Value</label>
            <div class="input-group">
              <input type="number" class="form-control" placeholder="10">
              <span class="input-group-text bg-primary-light text-primary-dark">%</span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <label class="form-label font-medium">Valid From</label>
            <input type="date" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label font-medium">Valid Until</label>
            <input type="date" class="form-control">
          </div>
        </div>
      `;
      promoContainer.appendChild(promoItem);

      // Add remove functionality
      promoItem.querySelector('.remove-promo').addEventListener('click', function() {
        promoContainer.removeChild(promoItem);
      });
    });

    // Image upload functionality
    document.getElementById('imageUpload').addEventListener('click', function() {
      document.getElementById('imageInput').click();
    });

    document.getElementById('imageInput').addEventListener('change', function(e) {
      const files = e.target.files;
      if (files.length > 0) {
        const sliderImages = document.getElementById('sliderImages');
        const sliderControls = document.getElementById('sliderControls');

        // Clear existing images
        sliderImages.innerHTML = '';
        sliderControls.innerHTML = '';

        // Add new images
        for (let i = 0; i < files.length; i++) {
          const file = files[i];
          const reader = new FileReader();

          reader.onload = function(e) {
            // Create slider image
            const sliderImage = document.createElement('div');
            sliderImage.className = 'slider-image';
            sliderImage.style.backgroundImage = `url('${e.target.result}')`;
            sliderImages.appendChild(sliderImage);

            // Create slider dot
            const sliderDot = document.createElement('div');
            sliderDot.className = 'slider-dot';
            if (i === 0) sliderDot.classList.add('active');
            sliderControls.appendChild(sliderDot);

            // Update preview
            if (i === 0) {
              document.getElementById('previewHeader').style.backgroundImage = `url('${e.target.result}')`;
              document.getElementById('fullPreviewHeader').style.backgroundImage = `url('${e.target.result}')`;
            }
          };

          reader.readAsDataURL(file);
        }
      }
    });

    // Real-time preview updates
    document.getElementById('eventTitle').addEventListener('input', function(e) {
      document.getElementById('previewTitle').textContent = e.target.value || 'Your Event Title';
      document.getElementById('fullPreviewTitle').textContent = e.target.value || 'Your Event Title';
    });

    document.getElementById('eventDate').addEventListener('change', function(e) {
      updatePreviewDate();
    });

    document.getElementById('eventTime').addEventListener('change', function(e) {
      updatePreviewDate();
    });

    document.getElementById('eventLocation').addEventListener('input', function(e) {
      document.getElementById('previewLocation').textContent = e.target.value || 'Venue or Online';
      document.getElementById('fullPreviewLocation').textContent = e.target.value || 'Venue or Online';
    });

    document.getElementById('eventLink').addEventListener('input', function(e) {
      if (document.getElementById('onlineEvent').checked) {
        document.getElementById('previewLocation').textContent = 'Online Event';
        document.getElementById('fullPreviewLocation').textContent = 'Online Event';
      }
    });

    document.getElementById('eventDescription').addEventListener('input', function(e) {
      document.getElementById('previewDescription').textContent = e.target.value || 'Event description will appear here...';
      document.getElementById('fullPreviewDescription').textContent = e.target.value || 'Event description will appear here...';
    });

    function updatePreviewDate() {
      const date = document.getElementById('eventDate').value;
      const time = document.getElementById('eventTime').value;

      let previewDateText = 'Date and Time';

      if (date) {
        const dateObj = new Date(date);
        const options = {
          weekday: 'long',
          year: 'numeric',
          month: 'long',
          day: 'numeric'
        };
        previewDateText = dateObj.toLocaleDateString('en-US', options);

        if (time) {
          previewDateText += ' at ' + formatTime(time);
        }
      }

      document.getElementById('previewDate').textContent = previewDateText;
      document.getElementById('fullPreviewDate').textContent = previewDateText;
    }

    function formatTime(timeString) {
      const [hours, minutes] = timeString.split(':');
      const hour = parseInt(hours);
      const ampm = hour >= 12 ? 'PM' : 'AM';
      const hour12 = hour % 12 || 12;
      return `${hour12}:${minutes} ${ampm}`;
    }

    // Initialize the full preview modal
    document.addEventListener('DOMContentLoaded', function() {
      const fullPreviewModal = new bootstrap.Modal(document.getElementById('fullPreviewModal'));

      // View Full Preview button click handler
      document.getElementById('viewFullPreviewBtn').addEventListener('click', function() {
        // Update the full preview with current form values
        updateFullPreview();
        // Show the modal
        fullPreviewModal.show();
      });

      function updateFullPreview() {
        // Update FAQs
        updateFullPreviewFAQs();

        // Update tickets
        updateFullPreviewTickets();
      }

      function updateFullPreviewFAQs() {
        const faqContainer = document.getElementById('fullPreviewFAQs');
        faqContainer.innerHTML = '';

        const faqItems = document.querySelectorAll('.faq-item');
        if (faqItems.length === 0) {
          faqContainer.innerHTML = '<p class="text-muted">No FAQs added yet</p>';
          return;
        }

        const accordion = document.createElement('div');
        accordion.className = 'accordion';
        accordion.id = 'faqAccordion';

        faqItems.forEach((item, index) => {
          const question = item.querySelector('input').value;
          const answer = item.querySelector('textarea').value;

          if (question && answer) {
            const accordionItem = document.createElement('div');
            accordionItem.className = 'accordion-item';
            accordionItem.innerHTML = `
              <h2 class="accordion-header" id="heading${index}">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${index}" aria-expanded="false" aria-controls="collapse${index}">
                  ${question}
                </button>
              </h2>
              <div id="collapse${index}" class="accordion-collapse collapse" aria-labelledby="heading${index}" data-bs-parent="#faqAccordion">
                <div class="accordion-body">
                  ${answer}
                </div>
              </div>
            `;
            accordion.appendChild(accordionItem);
          }
        });

        if (accordion.children.length > 0) {
          faqContainer.appendChild(accordion);
        } else {
          faqContainer.innerHTML = '<p class="text-muted">No FAQs added yet</p>';
        }
      }

      function updateFullPreviewTickets() {
        const ticketsContainer = document.getElementById('fullPreviewTickets');
        ticketsContainer.innerHTML = '';

        const ticketItems = document.querySelectorAll('.ticket-item');
        if (ticketItems.length === 0) {
          ticketsContainer.innerHTML = '<p class="text-muted">No tickets added yet</p>';
          return;
        }

        const ticketsList = document.createElement('div');
        ticketsList.className = 'list-group';

        ticketItems.forEach((item) => {
          const name = item.querySelector('input').value;
          const priceInput = item.querySelector('input[placeholder="0.00"]');
          const price = priceInput ? priceInput.value : '';

          if (name) {
            const ticketItem = document.createElement('div');
            ticketItem.className = 'list-group-item d-flex justify-content-between align-items-center';
            ticketItem.innerHTML = `
              <div>
                <h5 class="mb-1">${name}</h5>
                <small class="text-muted">Available</small>
              </div>
              <div class="text-end">
                <h5 class="mb-0">$${price || '0.00'}</h5>
                <button class="btn btn-sm btn-primary mt-2">Select</button>
              </div>
            `;
            ticketsList.appendChild(ticketItem);
          }
        });

        if (ticketsList.children.length > 0) {
          ticketsContainer.appendChild(ticketsList);
        } else {
          ticketsContainer.innerHTML = '<p class="text-muted">No tickets added yet</p>';
        }
      }
    });
  </script>
</body>

</html>