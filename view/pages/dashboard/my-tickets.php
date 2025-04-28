<?php
include_once __DIR__ . "/../../../config/database.php";
include_once __DIR__ . "/../../../config/session.php";
include_once __DIR__ . "/../../../controller/order.php";

checkSession();

if (isOrganizer()) {
    header("Location: /lama/view/pages/organizer/dashboard.php");
    exit();
}

$stmt = $cnx->prepare("
    SELECT o.id as order_id, o.total_amount, o.status as order_status, o.created_at,
           oi.quantity, oi.price as paid_price, oi.attendee_name, oi.attendee_email,
           t.name as ticket_name, t.price as original_price, 
           e.title as event_title, e.start_date, e.location, e.image, e.id as event_id
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN tickets t ON oi.ticket_id = t.id
    JOIN events e ON t.event_id = e.id
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tickets - Lamma</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tailwind CSS -->
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/view/styles/my-tickets.css">
    <!-- Configure Tailwind -->
    <link href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,800" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link href="https://demos.creative-tim.com/soft-ui-dashboard/assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="https://demos.creative-tim.com/soft-ui-dashboard/assets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- CSS Files -->
    <link
        id="pagestyle"
        href="../../styles/css/soft-ui-dashboard.css"
        rel="stylesheet" />


</head>

<body class="bg-gray-50">
    <?php include '../../partials/dashboar-sidebar.php'; ?>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <?php include '../../partials/dashboard-navbar.php'; ?>
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header pb-0">
                            <h6>My Tickets</h6>
                        </div>
                        <div class="card-body px-0 pt-0 pb-2">
                            <?php if (empty($tickets)): ?>
                                <div class="text-center py-8">
                                    <i class="fas fa-ticket-alt text-gray-400 text-6xl mb-4"></i>
                                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No Tickets Yet</h3>
                                    <p class="text-gray-500 mb-4">You haven't purchased any tickets yet.</p>
                                    <a href="/view/pages/events.php" class="btn btn-primary">
                                        <i class="fas fa-calendar-alt mr-2"></i> Browse Events
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive p-0">
                                    <table class="table align-items-center mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Event</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Ticket</th>
                                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
                                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($tickets as $ticket): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex px-2 py-1">
                                                            <div>
                                                                <img src="<?php echo htmlspecialchars($ticket['image']); ?>" class="me-3" alt="event" style="width: 40px; height: 22.5px; object-fit: cover;">
                                                            </div>
                                                            <div class="d-flex flex-column justify-content-center">
                                                                <h6 class="mb-0 text-sm"><?php echo htmlspecialchars($ticket['event_title']); ?></h6>
                                                                <p class="text-xs text-secondary mb-0"><?php echo htmlspecialchars($ticket['location']); ?></p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <p class="text-xs font-weight-bold mb-0"><?php echo htmlspecialchars($ticket['ticket_name']); ?></p>
                                                        <p class="text-xs text-secondary mb-0"><?php echo number_format($ticket['paid_price'], 2); ?> dt</p>
                                                        <p class="text-xs text-secondary mb-0">Qty: <?php echo htmlspecialchars($ticket['quantity']); ?></p>
                                                    </td>
                                                    <td class="align-middle text-center text-sm">
                                                        <span class="text-secondary text-xs font-weight-bold"><?php echo date('M d, Y', strtotime($ticket['start_date'])); ?></span>
                                                    </td>
                                                    <td class="align-middle text-center text-sm">
                                                        <span class="badge badge-sm bg-gradient-<?php echo ($ticket['order_status'] == 'completed') ? 'success' : (($ticket['order_status'] == 'cancelled') ? 'danger' : 'warning'); ?>">
                                                            <?php echo ucfirst(htmlspecialchars($ticket['order_status'] ? $ticket['order_status'] : 'Pending')); ?>
                                                        </span>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <a href="/lama/view/pages/event-details.php?id=<?php echo $ticket['event_id']; ?>" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="View event">
                                                            <i class="fas fa-eye text-primary"></i>

                                                            <?php if ($ticket['order_status'] != 'cancelled'): ?>
                                                                <a href="#" class="text-secondary font-weight-bold text-xs ms-2 cancel-ticket" data-order="<?php echo $ticket['order_id']; ?>" data-toggle="tooltip" data-original-title="Cancel ticket">
                                                                    <i class="fas fa-times text-danger"></i>
                                                                </a>
                                                            <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> <!-- Custom JavaScript -->
    <script src="../../scripts/core/popper.min.js"></script>
    <script src="../../scripts/core/bootstrap.min.js"></script>
    <script src="../../scripts/soft-ui-dashboard.js"></script>
    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });



        document.querySelectorAll('.cancel-ticket').forEach(function(element) {
            element.addEventListener('click', function(e) {
                e.preventDefault();
                const orderId = this.getAttribute('data-order');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to cancel this ticket? This action cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, cancel it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Processing...',
                            text: 'Cancelling your ticket',
                            icon: 'info',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        fetch('/lama/controller/api/cancel-ticket.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({
                                    order_id: orderId
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        title: 'Cancelled!',
                                        text: 'Your ticket has been cancelled successfully.',
                                        icon: 'success',
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error',
                                        text: data.message || 'Failed to cancel ticket',
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire({
                                    title: 'Error',
                                    text: 'An unexpected error occurred. Please try again.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            });
                    }
                });
            });
        });
    </script>
</body>

</html>