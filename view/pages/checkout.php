<?php
include_once __DIR__ . "/../../config/database.php";
include_once __DIR__ . "/../../config/session.php";
include_once __DIR__ . "/../../controller/order.php";
include_once __DIR__ . "/../../controller/event.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: /view/pages/login.php");
    exit();
}

$ticketId = isset($_GET['ticket_id']) ? (int)$_GET['ticket_id'] : 0;
$eventId = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;

if ($ticketId) {
    $stmt = $cnx->prepare("
        SELECT t.*, e.title as event_title, e.start_date, e.location, e.image
        FROM tickets t
        JOIN events e ON t.event_id = e.id
        WHERE t.id = ?
    ");
    $stmt->execute([$ticketId]);
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ticket) {
        header("Location: /view/pages/error.php");
        exit();
    }
} else {
    header("Location: /view/pages/error.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantity = (int)$_POST['quantity'];
    $attendeeName = $_POST['attendee_name'];
    $attendeeEmail = $_POST['attendee_email'];

    $result = createOrder($cnx, $_SESSION['user_id'], $ticket['id'], $quantity, $attendeeName, $attendeeEmail);

    if ($result['success']) {
        header("Location: /lama/view/pages/payment.php?order_id=" . $result['order_id']);
        exit();
    } else {
        $error = $result['error'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Lamma</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/view/styles/checkout.css">
    <!-- Configure Tailwind -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        blue: {
                            light: "#4F46E5",
                            DEFAULT: "#3730A3",
                            dark: "#312E81",
                        },
                        purple: {
                            light: "#A78BFA",
                            DEFAULT: "#8B5CF6",
                            dark: "#7C3AED",
                        },
                    },
                },
            },
        };
    </script>
</head>

<body class="bg-gray-50">

    <div class="container mx-auto px-4 py-10">
        <!-- Checkout Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold mb-2">Complete Your Registration</h1>
            <p class="text-gray-600">You're just a few steps away from securing your spot!</p>
        </div>

        <div class="max-w-4xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Checkout Form -->
                <div class="lg:col-span-2">
                    <div class="card shadow-sm mb-6">
                        <div class="card-header bg-white py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center mr-3">
                                    <span class="font-bold">1</span>
                                </div>
                                <h2 class="text-xl font-bold">Attendee Information</h2>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger mb-4">
                                    <?php echo htmlspecialchars($error); ?>
                                </div>
                            <?php endif; ?>

                            <form method="POST" action="">
                                <div class="mb-4">
                                    <h3 class="font-bold mb-3">Primary Attendee</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="form-group">
                                            <label for="attendee_name" class="form-label">Full Name</label>
                                            <input type="text" class="form-control" id="attendee_name" name="attendee_name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="attendee_email" class="form-label">Email Address</label>
                                            <input type="email" class="form-control" id="attendee_email" name="attendee_email" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <label for="quantity" class="form-label">Number of Tickets</label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" min="1" max="<?php echo $ticket['quantity_available']; ?>" value="1" required>
                                    <small class="text-gray-500">Maximum <?php echo $ticket['quantity_available']; ?> tickets available</small>
                                </div>

                                <div class="mt-6">
                                    <button type="submit" class="btn btn-primary w-full py-3">
                                        Complete Registration
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


                <div class="lg:col-span-1">
                    <div class="card shadow-sm sticky top-24">
                        <div class="card-header bg-white">
                            <h3 class="font-bold">Order Summary</h3>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <div class="flex items-center mb-4">
                                    <img src="<?php echo htmlspecialchars($ticket['image']); ?>" class="w-16 h-16 object-cover rounded-lg">
                                    <div class="ml-3">
                                        <h4 class="font-medium"><?php echo htmlspecialchars($ticket['event_title']); ?></h4>
                                        <p class="text-sm text-gray-500"><?php echo date('F j, Y', strtotime($ticket['start_date'])); ?></p>
                                        <p class="text-sm text-gray-500"><?php echo htmlspecialchars($ticket['location']); ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-b py-4 mb-4">
                                <div class="flex justify-between mb-2">
                                    <span><?php echo htmlspecialchars($ticket['event_title']); ?> × <span id="ticketCount">1</span></span>
                                    <span class="ticket-price" id="ticketPrice"><?php echo number_format($ticket['price'], 2); ?> dt</span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="flex justify-between mb-2">
                                    <span>Subtotal</span>
                                    <span id="subtotal"><?php echo number_format($ticket['price'], 2); ?> dt</span>
                                </div>
                            </div>

                            <div class="flex justify-between font-bold text-lg">
                                <span>Total</span>
                                <span id="totalPrice"><?php echo number_format($ticket['price'], 2); ?> dt</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script>
        // Update ticket count and prices when quantity changes
        document.getElementById('quantity').addEventListener('change', function() {
            const quantity = parseInt(this.value);
            const ticketPrice = <?php echo $ticket['price']; ?>;
            const total = quantity * ticketPrice;

            document.getElementById('ticketCount').textContent = quantity;
            document.getElementById('ticketPrice').textContent = ticketPrice.toFixed(2) + ' dt';
            document.getElementById('subtotal').textContent = total.toFixed(2) + ' dt';
            document.getElementById('totalPrice').textContent = total.toFixed(2) + ' dt';
        });
    </script>
</body>

</html>