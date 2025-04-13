<!DOCTYPE html>
<?php
include "../../../config/session.php";
checkSession();

?>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,800" rel="stylesheet" />
  <!-- Nucleo Icons -->
  <link href="https://demos.creative-tim.com/soft-ui-dashboard/assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="https://demos.creative-tim.com/soft-ui-dashboard/assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>

  <!-- CSS Files -->
  <link
    id="pagestyle"
    href="../../styles/css/soft-ui-dashboard.css"
    rel="stylesheet" />

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="g-sidenav-show bg-gray-100">
  <?php include '../../partials/dashboar-sidebar.php' ?>
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <?php include '../../partials/dashboard-navbar.php' ?>
    <?php
    if (isset($_SESSION['user'])) {
      echo '<div class="container-fluid py-4">';
      echo '<div class="card bg-gradient-primary border-0">';
      echo '<div class="card-body p-5">';
      echo '<div class="row align-items-center">';
      echo '<div class="col-md-8">';
      echo '<h2 class="text-white mb-3">Welcome back, ' . htmlspecialchars($_SESSION['user_full_name']) . '! 👋</h2>';
      echo '<p class="text-white opacity-8 mb-0">We\'re glad to see you again. Ready to manage your events?</p>';
      echo '</div>';
      echo '<div class="col-md-4 text-end">';
      echo '</div>';
      echo '</div>';
      echo '</div>';
      echo '</div>';
      echo '</div>';
    } else {
      echo '<div class="container-fluid py-4">';
      echo '<div class="alert alert-warning">No active session found</div>';
      echo '</div>';
    }
    ?>

    <div class="container-fluid py-4">
      <div class="card">
        <div class="card-header p-3 pt-2">
          <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute pt-3 text-white">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-ticket-check-icon lucide-ticket-check">
              <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z" />
              <path d="m9 12 2 2 4-4" />
            </svg>
          </div>
          <div class="text-end pt-1">
            <p class="text-sm mb-0 text-capitalize">Ticket Sales</p>
            <h4 class="mb-0">420</h4>
          </div>
        </div>
        <div class="card-body p-3">
          <canvas id="ticketSalesChart"></canvas>
        </div>
      </div>
      <div class="row mb-4">
        <div class="col-xl-6 col-sm-6 mt-4">
          <div class="card">
            <div class="card-header p-3 pt-2">
              <div class="icon icon-lg icon-shape bg-gradient-warning shadow-primary text-center border-radius-xl mt-n4 position-absolute pt-3 text-white">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-ticket-percent-icon lucide-ticket-percent">
                  <path d="M2 9a3 3 0 1 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 1 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z" />
                  <path d="M9 9h.01" />
                  <path d="m15 9-6 6" />
                  <path d="M15 15h.01" />
                </svg>
              </div>
              <div class="text-end pt-1">
                <p class="text-sm mb-0 text-capitalize">Promo Codes</p>
                <h4 class="mb-0">3 Active</h4>
              </div>
            </div>
            <div class="card-body p-3">
              <canvas id="promoCodeChart"></canvas>
            </div>
          </div>
        </div>

        <div class="col-xl-6 col-sm-6 mt-4">
          <div class="card">
            <div class="card-header p-3 pt-2">
              <div class="icon icon-lg icon-shape bg-gradient-info shadow-primary text-center border-radius-xl mt-n4 position-absolute pt-3 text-white">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pinned-icon lucide-map-pinned">
                  <path d="M18 8c0 3.613-3.869 7.429-5.393 8.795a1 1 0 0 1-1.214 0C9.87 15.429 6 11.613 6 8a6 6 0 0 1 12 0" />
                  <circle cx="12" cy="8" r="2" />
                  <path d="M8.714 14h-3.71a1 1 0 0 0-.948.683l-2.004 6A1 1 0 0 0 3 22h18a1 1 0 0 0 .948-1.316l-2-6a1 1 0 0 0-.949-.684h-3.712" />
                </svg>
              </div>
              <div class="text-end pt-1">
                <p class="text-sm mb-0 text-capitalize">Attendees</p>
                <h4 class="mb-0">5 Locations</h4>
              </div>
            </div>
            <div class="card-body p-3">
              <canvas id="locationChart"></canvas>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-header p-3 pt-2">
              <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute pt-3 text-white">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-ticket-icon lucide-ticket">
                  <path d="M2 9a3 3 0 0 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z" />
                  <path d="M13 5v2" />
                  <path d="M13 17v2" />
                  <path d="M13 11v2" />
                </svg>
              </div>
              <div class="text-end pt-1">
                <p class="text-sm mb-0 text-capitalize">Ticket Types</p>
                <h4 class="mb-0">3 Types</h4>
              </div>
            </div>
            <div class="card-body p-3">
              <canvas id="ticketTypeChart"></canvas>
            </div>
          </div>
        </div>

        <div class="col-xl-8 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-header p-3 pt-2">
              <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute pt-3 text-white">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-clock-icon lucide-calendar-clock">
                  <path d="M21 7.5V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h3.5" />
                  <path d="M16 2v4" />
                  <path d="M8 2v4" />
                  <path d="M3 10h5" />
                  <path d="M17.5 17.5 16 16.3V14" />
                  <circle cx="16" cy="16" r="6" />
                </svg>
              </div>
              <div class="text-end pt-1">
                <p class="text-sm mb-0 text-capitalize">Latest Events</p>
                <h4 class="mb-0">5 New</h4>
              </div>
            </div>
            <div class="card-body p-3">
              <div class="table-responsive">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Event</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Date</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm">Summer Music Festival</h6>
                            <p class="text-xs text-secondary mb-0">Main Stage Arena</p>
                          </div>
                        </div>
                      </td>
                      <td>
                        <p class="text-xs font-weight-bold mb-0">Apr 25, 2024</p>
                      </td>
                      <td>
                        <span class="badge badge-sm bg-gradient-success">Active</span>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm">Tech Conference 2024</h6>
                            <p class="text-xs text-secondary mb-0">Convention Center</p>
                          </div>
                        </div>
                      </td>
                      <td>
                        <p class="text-xs font-weight-bold mb-0">Apr 20, 2024</p>
                      </td>
                      <td>
                        <span class="badge badge-sm bg-gradient-success">Active</span>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm">Food & Wine Festival</h6>
                            <p class="text-xs text-secondary mb-0">City Park</p>
                          </div>
                        </div>
                      </td>
                      <td>
                        <p class="text-xs font-weight-bold mb-0">Apr 15, 2024</p>
                      </td>
                      <td>
                        <span class="badge badge-sm bg-gradient-warning">Soon</span>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm">Art Exhibition</h6>
                            <p class="text-xs text-secondary mb-0">Modern Gallery</p>
                          </div>
                        </div>
                      </td>
                      <td>
                        <p class="text-xs font-weight-bold mb-0">Apr 10, 2024</p>
                      </td>
                      <td>
                        <span class="badge badge-sm bg-gradient-secondary">Ended</span>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm">Comedy Night</h6>
                            <p class="text-xs text-secondary mb-0">City Theater</p>
                          </div>
                        </div>
                      </td>
                      <td>
                        <p class="text-xs font-weight-bold mb-0">Apr 5, 2024</p>
                      </td>
                      <td>
                        <span class="badge badge-sm bg-gradient-secondary">Ended</span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script>
    // Chart 1 - Ticket Sales Over Time
    new Chart(document.getElementById('ticketSalesChart'), {
      type: 'line',
      data: {
        labels: ['Apr 1', 'Apr 5', 'Apr 10', 'Apr 15', 'Apr 20', 'Apr 25'],
        datasets: [{
          label: 'Tickets Sold',
          data: [50, 120, 200, 280, 350, 420],
          borderColor: '#4f46e5',
          backgroundColor: 'rgba(79, 70, 229, 0.1)',
          fill: true,
          tension: 0.4,
          borderWidth: 2,
          pointRadius: 4,
          pointBackgroundColor: '#4f46e5',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          }
        },
        layout: {
          padding: {
            left: 10,
            right: 25,
            top: 25,
            bottom: 10
          },
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: 'rgba(0, 0, 0, 0.05)',
              drawBorder: false
            },
            ticks: {
              padding: 10,
              font: {
                size: 12
              }
            }
          },
          x: {
            grid: {
              display: false,
              drawBorder: false
            },
            ticks: {
              padding: 10,
              font: {
                size: 12
              }
            }
          }
        }
      }
    });

    // Chart 2 - Ticket Type Distribution
    new Chart(document.getElementById('ticketTypeChart'), {
      type: 'doughnut',
      data: {
        labels: ['VIP', 'General', 'Early Bird'],
        datasets: [{
          data: [70, 200, 130],
          backgroundColor: ['#4f46e5', '#10b981', '#f59e0b'],
          borderRadius: 8
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom'
          }
        }
      }
    });

    // Chart 3 - Promo Code Usage
    new Chart(document.getElementById('promoCodeChart'), {
      type: 'bar',
      data: {
        labels: ['WELCOME10', 'VIP50', 'EARLYBIRD'],
        datasets: [{
          label: 'Uses',
          data: [45, 75, 60],
          backgroundColor: '#f59e0b',
          borderRadius: 10
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: 'rgba(0, 0, 0, 0.1)'
            }
          },
          x: {
            grid: {
              color: 'rgba(0, 0, 0, 0.1)'
            }
          }
        }
      }
    });

    // Chart 4 - Attendees by Location
    new Chart(document.getElementById('locationChart'), {
      type: 'bar',
      data: {
        labels: ['New York', 'London', 'Tokyo', 'Berlin', 'Tunis'],
        datasets: [{
          label: 'Attendees',
          data: [90, 60, 80, 50, 30],
          backgroundColor: '#6366f1',
          borderRadius: 10
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: 'rgba(0, 0, 0, 0.1)'
            }
          },
          x: {
            grid: {
              color: 'rgba(0, 0, 0, 0.1)'
            }
          }
        }
      }
    });
  </script>
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../../scripts/core/popper.min.js"></script>
  <script src="../../scripts/core/bootstrap.min.js"></script>
  <script src="../../scripts/soft-ui-dashboard.js"></script>
  <!-- <script src="../../scripts/soft-ui-dashboard.min.js"></script> -->
</body>

</html>