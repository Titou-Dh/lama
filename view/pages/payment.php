<?php
include_once "../../config/database.php";
include_once "../../config/session.php";
include_once "../../controller/order.php";

checkSession();

$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

$order = getOrderById($cnx, $orderId);

if (!$order) {
    header("Location: /view/pages/404.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation - Lamma</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/view/styles/checkout.css">
    <!-- Configure Tailwind -->
    <style>
        .btn-gradient {
            background: linear-gradient(90deg, #4F46E5, #8B5CF6);
            color: white;
            transition: all 0.3s ease;
        }

        .btn-gradient:hover {
            background: linear-gradient(90deg, #3730A3, #7C3AED);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3);
        }

        #mainNav {
            background-color: rgba(37, 99, 235, 0.2);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
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
    <!-- Header/Navigation -->
    <?php include '../partials/navbar.php'; ?>

    <div class="container mx-auto px-4 py-10">
        <div class="max-w-3xl mx-auto">
            <div class="card shadow-sm">
                <div class="card-body text-center py-10">
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-check text-green-500 text-4xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold mb-2">Registration Complete!</h2>
                    <p class="text-gray-600 mb-6">Your tickets have been sent to your email address.</p>

                    <div class="max-w-md mx-auto bg-gray-50 rounded-lg p-6 mb-6">
                        <div class="flex justify-between mb-4">
                            <span class="text-gray-600">Order Number:</span>
                            <span class="font-bold">#<?php echo str_pad($order['id'], 8, '0', STR_PAD_LEFT); ?></span>
                        </div>
                        <div class="flex justify-between mb-4">
                            <span class="text-gray-600">Date:</span>
                            <span><?php echo date('F j, Y', strtotime($order['created_at'])); ?></span>
                        </div>
                        <div class="flex justify-between mb-4">
                            <span class="text-gray-600">Event:</span>
                            <span><?php echo htmlspecialchars($order['event_title']); ?></span>
                        </div>
                        <div class="flex justify-between mb-4">
                            <span class="text-gray-600">Tickets:</span>
                            <span><?php echo $order['quantity'] . ' × ' . htmlspecialchars($order['ticket_name']); ?></span>
                        </div>
                        <div class="flex justify-between mb-4">
                            <span class="text-gray-600">Total Amount:</span>
                            <span class="font-bold"><?php echo number_format($order['total_amount'], 2); ?> dt</span>
                        </div>
                    </div>

                    <div class="mb-8">
                        <h3 class="text-lg font-bold mb-4">Ticket Delivery</h3>
                        <div class="flex items-center justify-center mb-4">
                            <div class="bg-blue-50 text-blue-500 rounded-full p-3 mr-3">
                                <i class="fas fa-envelope text-xl"></i>
                            </div>
                            <div class="text-left">
                                <p class="font-medium">Tickets sent to your email</p>
                                <p class="text-gray-600"><?php echo htmlspecialchars($order['attendee_email']); ?></p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-500">You can also access your tickets from your account dashboard.</p>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-center gap-4">
                        <a href="/view/pages/tickets.php" class="btn btn-primary">
                            <i class="fas fa-ticket-alt mr-2"></i> View My Tickets
                        </a>
                        <a href="/view/pages/events.php" class="btn btn-outline-primary">
                            <i class="fas fa-search mr-2"></i> Explore More Events
                        </a>
                    </div>
                </div>
            </div>

            <div class="mt-8">
                <h3 class="text-xl font-bold mb-4">Event Details</h3>
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="flex flex-col md:flex-row">
                            <div class="md:w-1/4">
                                <img src="<?php echo htmlspecialchars($order['image']); ?>" alt="test" class="w-full h-auto rounded-lg">
                            </div>
                            <div class="md:w-3/4 md:pl-6 mt-4 md:mt-0">
                                <h4 class="text-lg font-bold"><?php echo htmlspecialchars($order['event_title']); ?></h4>
                                <div class="flex items-center text-gray-600 mt-2">
                                    <i class="fas fa-calendar-day mr-2 text-blue-500"></i>
                                    <?php echo date('F j, Y', strtotime($order['start_date'])); ?>
                                </div>
                                <div class="flex items-center text-gray-600 mt-1">
                                    <i class="fas fa-map-marker-alt mr-2 text-blue-500"></i>
                                    <?php echo htmlspecialchars($order['location']); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include '../partials/footer.php'; ?>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>