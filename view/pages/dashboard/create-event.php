<?php

include '../../../config/database.php';
include '../../../config/session.php';
include '../../../controller/event.php';
include '../../../controller/categories.php';
include '../../../controller/ticket.php';


$categories = getCategories($cnx);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['submit'])) {
  $response = [
    'success' => false,
    'message' => '',
    'event_id' => null,
    'status' => ''
  ];

  try {
    $eventData = [
      'organizer_id' => $_POST['organizer_id'] ?? null,
      'title' => $_POST['title'] ?? null,
      'description' => $_POST['description'] ?? null,
      'start_date' => $_POST['start_date'] ?? null,
      'end_date' => $_POST['end_date'] ?? null,
      'door_time' => $_POST['door_time'] ?? null,
      'location' => $_POST['location'] ?? null,
      'online_link' => $_POST['online_link'] ?? null,
      'event_type' => $_POST['event_type'] ?? null,
      'category_id' => $_POST['category_id'] ?? null,
      'capacity' => empty($_POST['capacity']) ? null : $_POST['capacity'],
      'age_restriction' => $_POST['age_restriction'] ?? null,
      'status' => $_POST['status'] ?? 'draft'
    ];

    if (empty($eventData['title']) || empty($eventData['description']) || empty($eventData['start_date'])) {
      $response['message'] = "Required fields are missing";
      echo json_encode($response);
      exit;
    }
    $fileData = null;
    if (isset($_FILES['eventImage']) && $_FILES['eventImage']['error'] !== UPLOAD_ERR_NO_FILE) {
      $fileData = $_FILES['eventImage'];
      event_log('Image upload received for event creation', 'INFO', ['filename' => $fileData['name'], 'size' => $fileData['size']]);
    }

    event_log('Attempting to create event', 'INFO', ['title' => $eventData['title'], 'type' => $eventData['event_type']]);

    $eventId = createEvent($cnx, $eventData, $fileData);

    if ($eventId) {
      $response['success'] = true;
      $response['event_id'] = $eventId;
      $response['status'] = $eventData['status'];
      $response['message'] = "Your event has been " .
        ($eventData['status'] === 'published' ? "published" : "saved as draft");

      if ($eventId) {
        if (isset($_POST['tickets'])) {
          $tickets = json_decode($_POST['tickets'], true);
          if (is_array($tickets) && !empty($tickets)) {
            saveEventTickets($cnx, $eventId, $tickets);
          }
        }
        if (isset($_POST['faqs'])) {
          $faqs = json_decode($_POST['faqs'], true);
          if (is_array($faqs) && !empty($faqs)) {
            saveEventFaqs($cnx, $eventId, $faqs);
          }
        }
      }
    } else {
      $response['message'] = "Error creating event. Please try again.";
    }
  } catch (Exception $e) {
    $response['message'] = "Server error: " . $e->getMessage();
  }

  echo json_encode($response);
  exit;
}
?>






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

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
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

      <!-- Alert for form submission feedback -->
      <div id="formAlert" class="alert d-none mb-4"></div>

      <div class="row">
        <div class="col-lg-8">
          <!-- Beginning of the form wrapper -->
          <form id="createEventForm" enctype="multipart/form-data">

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
                <button type="button" class="btn btn-gradient rounded-pill px-4">Browse Files</button>
                <input type="file" name="eventImage" id="imageInput" class="d-none" accept="image/*">
              </div>
            </div>

            <!-- Basic Info Section -->
            <div class="form-section">
              <h3 class="text-gradient">Basic Information</h3>

              <div class="mb-4">
                <label for="eventTitle" class="form-label font-medium">Event Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="title" id="eventTitle" placeholder="Keep it short and sweet" required>
                <div class="form-text">Attendees will see this as the event name. Example: "Summer Music Festival 2025"</div>
              </div>

              <div class="mb-4">
                <label for="eventDescription" class="form-label font-medium">Event Description <span class="text-danger">*</span></label>
                <textarea class="form-control" name="description" id="eventDescription" rows="4" placeholder="Tell people what your event is about" required></textarea>
                <div class="form-text">Provide a compelling description that will make people want to attend.</div>
              </div>

              <div class="row mb-4">
                <div class="col-md-6">
                  <label class="form-label font-medium">Event Date <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <span class="input-group-text bg-primary-light text-primary-dark"><i class="far fa-calendar-alt"></i></span>
                    <input type="date" class="form-control" name="start_date" id="eventDate" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label font-medium">Event Time <span class="text-danger">*</span></label>
                  <div class="input-group">
                    <span class="input-group-text bg-secondary-light text-secondary-purple"><i class="far fa-clock"></i></span>
                    <input type="time" class="form-control" name="event_time" id="eventTime" required>
                  </div>
                </div>
              </div>

              <div class="mb-4">
                <label class="form-label font-medium">End Date</label>
                <div class="input-group">
                  <span class="input-group-text bg-primary-light text-primary-dark"><i class="far fa-calendar-alt"></i></span>
                  <input type="date" class="form-control" name="end_date" id="eventEndDate">
                </div>
                <div class="form-text">Optional. If left blank, the event will be considered a single-day event.</div>
              </div>

              <div class="mb-4 p-3 rounded bg-gradient-light">
                <label class="form-label font-medium">Event Type <span class="text-danger">*</span></label>
                <div class="d-flex">
                  <div class="form-check me-4">
                    <input class="form-check-input" type="radio" name="event_type" id="inPersonEvent" value="in-person" checked>
                    <label class="form-check-label" for="inPersonEvent">
                      In-person
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="event_type" id="onlineEvent" value="online">
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
                  <input type="text" class="form-control" name="location" id="eventLocation" placeholder="Enter venue name or address">
                </div>
              </div>

              <div class="mb-4 d-none" id="onlineSection">
                <label for="eventLink" class="form-label font-medium">Online Event Link <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text bg-secondary-light text-secondary-purple"><i class="fas fa-link"></i></span>
                  <input type="text" class="form-control" name="online_link" id="eventLink" placeholder="https://zoom.us/j/123456789">
                </div>
                <div class="form-text">You can add a Zoom, Google Meet, or any other video conferencing link.</div>
              </div>
            </div>

            <!-- Event Details Section -->
            <div class="form-section">
              <h3 class="text-gradient">Event Details</h3>

              <div class="mb-4">
                <label for="eventCategory" class="form-label font-medium">Category <span class="text-danger">*</span></label>
                <select class="form-select" name="category_id" id="eventCategory" required>
                  <option value="" selected disabled>Select a category</option>
                  <?php foreach ($categories as $category) : ?>
                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="mb-4 p-3 rounded bg-gradient-light">
                <label class="form-label font-medium">Age Restrictions</label>
                <select class="form-select" name="age_restriction" id="ageRestriction">
                  <option value="All Ages" selected>All Ages</option>
                  <option value="18+">18+</option>
                  <option value="21+">21+</option>
                  <option value="Custom">Custom</option>
                </select>
              </div>

              <div class="mb-4">
                <label for="doorTime" class="form-label font-medium">Door Time</label>
                <div class="input-group">
                  <span class="input-group-text bg-primary-light text-primary-dark"><i class="far fa-clock"></i></span>
                  <input type="time" class="form-control" name="door_time" id="doorTime">
                </div>
                <div class="form-text">When should attendees arrive? (e.g., 30 minutes before start time)</div>
              </div>

              <div class="mb-4">
                <label for="capacity" class="form-label font-medium">Capacity</label>
                <input type="number" class="form-control" name="capacity" id="capacity" placeholder="Maximum number of attendees">
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
                    <input type="text" class="form-control" name="faqs[0][question]" placeholder="E.g., Is there parking available?">
                  </div>
                  <div>
                    <label class="form-label font-medium">Answer</label>
                    <textarea class="form-control" name="faqs[0][answer]" rows="2" placeholder="Provide a clear answer"></textarea>
                  </div>
                </div>
              </div>

              <button type="button" class="btn btn-outline-primary mt-2" id="addFaqBtn">
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
                    <input type="text" class="form-control" name="tickets[0][name]" placeholder="E.g., General Admission, VIP, Early Bird">
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <label class="form-label font-medium">Price</label>
                      <div class="input-group">
                        <span class="input-group-text bg-primary-light text-primary-dark">$</span>
                        <input type="number" class="form-control" name="tickets[0][price]" placeholder="0.00" step="0.01">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label font-medium">Quantity</label>
                      <input type="number" class="form-control" name="tickets[0][quantity]" placeholder="Number of tickets available">
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <label class="form-label font-medium">Sale Start</label>
                      <input type="date" class="form-control" name="tickets[0][sale_start]">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label font-medium">Sale End</label>
                      <input type="date" class="form-control" name="tickets[0][sale_end]">
                    </div>
                  </div>
                </div>
              </div>

              <button type="button" class="btn btn-outline-primary mt-2" id="addTicketBtn">
                <i class="fas fa-plus me-2"></i> Add Another Ticket
              </button>
            </div>

            <!-- Promo Codes Section -->
            <!-- <div class="form-section">
              <h3 class="text-gradient">Promo Codes</h3>
              <p class="text-gray-500 mb-4">Create promotional codes to offer discounts on tickets.</p>

              <div id="promoContainer">
                <div class="promo-item mb-3 p-3 border rounded">
                  <div class="mb-3">
                    <label class="form-label font-medium">Code</label>
                    <input type="text" class="form-control" name="promos[0][code]" placeholder="E.g., SUMMER25, EARLYBIRD">
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <label class="form-label font-medium">Discount Type</label>
                      <select class="form-select" name="promos[0][type]">
                        <option value="percentage" selected>Percentage</option>
                        <option value="fixed">Fixed Amount</option>
                      </select>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label font-medium">Discount Value</label>
                      <div class="input-group">
                        <input type="number" class="form-control" name="promos[0][value]" placeholder="10" step="0.01">
                        <span class="input-group-text bg-primary-light text-primary-dark">%</span>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <label class="form-label font-medium">Valid From</label>
                      <input type="date" class="form-control" name="promos[0][valid_from]">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label font-medium">Valid Until</label>
                      <input type="date" class="form-control" name="promos[0][valid_until]">
                    </div>
                  </div>
                </div>
              </div>

              <button type="button" class="btn btn-outline-primary mt-2" id="addPromoBtn">
                <i class="fas fa-plus me-2"></i> Add Another Promo Code
              </button>
            </div> -->

            <!-- Hidden field for organizer_id - would typically come from your session -->
            <input type="hidden" name="organizer_id" id="organizerId" value="1"> <!-- Replace with actual user ID from session -->
            <input type="hidden" name="status" id="eventStatus" value="draft">

            <!-- Publish Button -->
            <div class="d-flex justify-content-between mt-4 mb-5 p-4 rounded bg-gradient-light">
              <button type="button" class="btn btn-outline-secondary rounded-pill px-4" id="saveDraftBtn" data-status="draft">Save Draft</button>
              <button type="submit" class="btn btn-primary rounded-pill px-5 py-2" id="publishBtn" data-status="published">Publish Event</button>
            </div>
          </form>
          <!-- End of form wrapper -->
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
              <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" id="viewFullPreviewBtn">
                <i class="fas fa-external-link-alt me-1"></i> View Full Preview
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Success Modal -->
  <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title">Success!</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center py-4">
          <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
          <h4 class="mt-3">Event Created Successfully</h4>
          <p class="text-muted" id="successMessage">Your event has been created successfully.</p>
        </div>
        <div class="modal-footer justify-content-center">
          <button type="button" class="btn btn-outline-secondary me-2" data-bs-dismiss="modal">Close</button>
          <a href="" class="btn btn-primary" id="viewEventBtn">View Event</a>
          <a href="/lama/view/pages/dashboard" class="btn btn-success">Go to Dashboard</a>
        </div>
      </div>
    </div>
  </div>

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
      if (navbar && window.scrollY > 50) {
        navbar.classList.add('navbar-scrolled');
      } else if (navbar) {
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

    // Handle form submission status
    document.getElementById('saveDraftBtn').addEventListener('click', function() {
      document.getElementById('eventStatus').value = 'draft';
      $("#createEventForm").submit();
    });

    document.getElementById('publishBtn').addEventListener('click', function() {
      document.getElementById('eventStatus').value = 'published';
    });

    // Add FAQ button
    let faqCounter = 1;
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
          <input type="text" class="form-control" name="faqs[${faqCounter}][question]" placeholder="E.g., Is there parking available?">
        </div>
        <div>
          <label class="form-label font-medium">Answer</label>
          <textarea class="form-control" name="faqs[${faqCounter}][answer]" rows="2" placeholder="Provide a clear answer"></textarea>
        </div>
      `;
      faqContainer.appendChild(faqItem);
      faqCounter++;

      // Add remove functionality
      faqItem.querySelector('.remove-faq').addEventListener('click', function() {
        faqContainer.removeChild(faqItem);
      });
    });

    // Add Ticket button
    let ticketCounter = 1;
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
          <input type="text" class="form-control" name="tickets[${ticketCounter}][name]" placeholder="E.g., General Admission, VIP, Early Bird">
        </div>
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label font-medium">Price</label>
            <div class="input-group">
              <span class="input-group-text bg-primary-light text-primary-dark">$</span>
              <input type="number" class="form-control" name="tickets[${ticketCounter}][price]" placeholder="0.00" step="0.01">
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label font-medium">Quantity</label>
            <input type="number" class="form-control" name="tickets[${ticketCounter}][quantity]" placeholder="Number of tickets available">
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <label class="form-label font-medium">Sale Start</label>
            <input type="date" class="form-control" name="tickets[${ticketCounter}][sale_start]">
          </div>
          <div class="col-md-6">
            <label class="form-label font-medium">Sale End</label>
            <input type="date" class="form-control" name="tickets[${ticketCounter}][sale_end]">
          </div>
        </div>
      `;
      ticketContainer.appendChild(ticketItem);
      ticketCounter++;

      // Add remove functionality
      ticketItem.querySelector('.remove-ticket').addEventListener('click', function() {
        ticketContainer.removeChild(ticketItem);
      });
    });

    // Add Promo Code button
    // let promoCounter = 1;
    // document.getElementById('addPromoBtn').addEventListener('click', function() {
    //   const promoContainer = document.getElementById('promoContainer');
    //   const promoItem = document.createElement('div');
    //   promoItem.className = 'promo-item mb-3 p-3 border rounded';
    //   promoItem.innerHTML = `
    //     <div class="d-flex justify-content-between mb-2">
    //       <h5 class="mb-0">New Promo Code</h5>
    //       <button type="button" class="btn-close remove-promo"></button>
    //     </div>
    //     <div class="mb-3">
    //       <label class="form-label font-medium">Code</label>
    //       <input type="text" class="form-control" name="promos[${promoCounter}][code]" placeholder="E.g., SUMMER25, EARLYBIRD">
    //     </div>
    //     <div class="row mb-3">
    //       <div class="col-md-6">
    //         <label class="form-label font-medium">Discount Type</label>
    //         <select class="form-select" name="promos[${promoCounter}][type]">
    //           <option value="percentage" selected>Percentage</option>
    //           <option value="fixed">Fixed Amount</option>
    //         </select>
    //       </div>
    //       <div class="col-md-6">
    //         <label class="form-label font-medium">Discount Value</label>
    //         <div class="input-group">
    //           <input type="number" class="form-control" name="promos[${promoCounter}][value]" placeholder="10" step="0.01">
    //           <span class="input-group-text bg-primary-light text-primary-dark">%</span>
    //         </div>
    //       </div>
    //     </div>
    //     <div class="row">
    //       <div class="col-md-6">
    //         <label class="form-label font-medium">Valid From</label>
    //         <input type="date" class="form-control" name="promos[${promoCounter}][valid_from]">
    //       </div>
    //       <div class="col-md-6">
    //         <label class="form-label font-medium">Valid Until</label>
    //         <input type="date" class="form-control" name="promos[${promoCounter}][valid_until]">
    //       </div>
    //     </div>
    //   `;
    //   promoContainer.appendChild(promoItem);
    //   promoCounter++;

    //   // Add remove functionality
    //   promoItem.querySelector('.remove-promo').addEventListener('click', function() {
    //     promoContainer.removeChild(promoItem);
    //   });
    // });

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
      const successModal = new bootstrap.Modal(document.getElementById('successModal'));

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

      // Form submission handling
      $("#createEventForm").submit(function(e) {
        e.preventDefault();

        // Show loading state
        $("#formAlert").removeClass("d-none alert-danger alert-success").addClass("alert-info").html('<i class="fas fa-spinner fa-spin me-2"></i> Creating your event...');

        // Get form data
        let formData = new FormData(this);

        // Add event time to start date
        const eventDate = $("#eventDate").val();
        const eventTime = $("#eventTime").val();
        if (eventDate && eventTime) {
          const combinedDateTime = eventDate + " " + eventTime + ":00";
          formData.set("start_date", combinedDateTime);
        }

        // Process tickets data into a structured format
        const ticketItems = document.querySelectorAll('.ticket-item');
        let tickets = [];
        ticketItems.forEach((item, index) => {
          const nameInput = item.querySelector('input[name^="tickets["][name$="[name]"]');
          const priceInput = item.querySelector('input[name^="tickets["][name$="[price]"]');
          const quantityInput = item.querySelector('input[name^="tickets["][name$="[quantity]"]');
          const saleStartInput = item.querySelector('input[name^="tickets["][name$="[sale_start]"]');
          const saleEndInput = item.querySelector('input[name^="tickets["][name$="[sale_end]"]');

          if (nameInput && nameInput.value) {
            tickets.push({
              name: nameInput.value,
              price: priceInput ? priceInput.value : '',
              quantity: quantityInput ? quantityInput.value : '',
              sale_start: saleStartInput ? saleStartInput.value : '',
              sale_end: saleEndInput ? saleEndInput.value : ''
            });
          }
        });

        // Convert to JSON string and add to form data
        formData.set('tickets', JSON.stringify(tickets));

        // Similarly process FAQs
        const faqItems = document.querySelectorAll('.faq-item');
        let faqs = [];
        faqItems.forEach((item, index) => {
          const questionInput = item.querySelector('input[name^="faqs["]');
          const answerInput = item.querySelector('textarea[name^="faqs["]');

          if (questionInput && questionInput.value && answerInput && answerInput.value) {
            faqs.push({
              question: questionInput.value,
              answer: answerInput.value
            });
          }
        });
        formData.set('faqs', JSON.stringify(faqs));

        // Process promo codes
        // const promoItems = document.querySelectorAll('.promo-item');
        // let promos = [];
        // promoItems.forEach((item, index) => {
        //   const codeInput = item.querySelector('input[name^="promos["][name$="[code]"]');
        //   const typeSelect = item.querySelector('select[name^="promos["][name$="[type]"]');
        //   const valueInput = item.querySelector('input[name^="promos["][name$="[value]"]');
        //   const validFromInput = item.querySelector('input[name^="promos["][name$="[valid_from]"]');
        //   const validUntilInput = item.querySelector('input[name^="promos["][name$="[valid_until]"]');

        //   if (codeInput && codeInput.value) {
        //     promos.push({
        //       code: codeInput.value,
        //       type: typeSelect ? typeSelect.value : 'percentage',
        //       value: valueInput ? valueInput.value : '',
        //       valid_from: validFromInput ? validFromInput.value : '',
        //       valid_until: validUntilInput ? validUntilInput.value : ''
        //     });
        //   }
        // });
        // formData.set('promos', JSON.stringify(promos));

        $.ajax({
          url: window.location.href, // Submit to the current page
          type: "POST",
          data: formData,
          contentType: false,
          processData: false,
          success: function(response) {
            try {
              const result = JSON.parse(response);

              if (result.success) {
                // Update success modal content
                $("#successMessage").text(result.message);
                $("#viewEventBtn").attr("href", "/lama/view/pages/event-detail.php?id=" + result.event_id);

                // Show success modal
                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();

                // Reset form if it was a successful publish (not a draft)
                if (result.status === 'published') {
                  $("#createEventForm")[0].reset();
                }
              } else {
                // Show error message
                $("#formAlert").removeClass("alert-info alert-success").addClass("alert-danger").html('<i class="fas fa-exclamation-circle me-2"></i> ' + result.message);
              }
            } catch (e) {
              // Handle parsing error
              $("#formAlert").removeClass("alert-info alert-success").addClass("alert-danger").html('<i class="fas fa-exclamation-circle me-2"></i> An unexpected error occurred.');
              console.error("Error parsing response:", e);
            }
          },
          error: function(xhr, status, error) {
            // Show error message
            $("#formAlert").removeClass("alert-info alert-success").addClass("alert-danger").html('<i class="fas fa-exclamation-circle me-2"></i> Error: ' + error);
          }
        });
      });
    });
  </script>
</body>

</html>