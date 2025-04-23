document.addEventListener("DOMContentLoaded", function () {
  const preferenceTags = document.querySelectorAll(".preference-tag");
  const preferencesForm = document.getElementById("preferencesForm");

  preferenceTags.forEach((tag) => {
    tag.addEventListener("click", function () {
      this.classList.toggle("active");
      updateSelectedCategories();
    });
  });

  function updateSelectedCategories() {
    const selectedCategories = Array.from(preferenceTags)
      .filter((tag) => tag.classList.contains("active"))
      .map((tag) => tag.getAttribute("data-id"));

    document.getElementById("selectedCategories").value =
      selectedCategories.join(",");
  }

  preferencesForm.addEventListener("submit", function (event) {
    event.preventDefault();

    const selectedCategories =
      document.getElementById("selectedCategories").value;
    if (!selectedCategories) {
      Swal.fire({
        icon: "warning",
        title: "No Preferences Selected",
        text: "Please select at least one category.",
        confirmButtonText: "OK",
      });
      return;
    }

    const formData = new FormData();
    formData.append("selectedCategories", selectedCategories);

    fetch("/lama/controller/api/save-preferences.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error("Network response was not ok");
        }
        return response.json();
      })
      .then((data) => {
        if (data.status === "success") {
          Swal.fire({
            icon: "success",
            title: "Preferences Saved!",
            text: "Your event preferences have been updated.",
            showConfirmButton: false,
            timer: 1500,
          });
          loadRecommendedEvents();
        } else {
          throw new Error(data.message || "Failed to save preferences");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        Swal.fire({
          icon: "error",
          title: "Error",
          text:
            error.message ||
            "Something went wrong while saving your preferences",
          confirmButtonText: "OK",
        });
      });
  });

  function loadRecommendedEvents() {
    fetch("/lama/controller/api/get-recommended-events.php")
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          updateRecommendedEventsUI(data.events);
        }
      })
      .catch((error) => console.error("Error loading recommendations:", error));
  }

  function updateRecommendedEventsUI(events) {
    const container = document.querySelector(".card-body");
    if (!container) return;

    let html =
      '<h3 class="card-title text-xl font-bold mb-4">Your Recommended Events</h3>';

    if (events && events.length > 0) {
      events.forEach((event) => {
        html += `
                    <div class="recommended-event d-flex mb-4">
                        <div class="flex-shrink-0 me-3" style="
                            width: 80px;
                            height: 80px;
                            background-image: url('${
                              event.image ||
                              "../assets/images/default-event.jpg"
                            }');
                            background-size: cover;
                            border-radius: 0.5rem;
                        "></div>
                        <div>
                            <h4 class="text-lg font-semibold">${
                              event.title
                            }</h4>
                            <p class="text-sm text-gray-500 mb-1">
                                <i class="far fa-calendar-alt me-1"></i> ${new Date(
                                  event.start_date
                                ).toLocaleDateString()}
                            </p>
                            <p class="text-sm text-gray-500">
                                <i class="fas fa-map-marker-alt me-1"></i> ${
                                  event.location || "Online Event"
                                }
                            </p>
                        </div>
                    </div>
                `;
      });
    } else {
      html +=
        '<p class="text-center text-gray-500">No recommended events yet. Select your interests to see personalized recommendations!</p>';
    }

    html += `<div class="text-center mt-4">
                <a href="#" class="btn btn-outline-primary rounded-pill px-4">View All Recommendations</a>
              </div>`;
    container.innerHTML = html;
  }

  updateSelectedCategories();
  loadRecommendedEvents();
});
