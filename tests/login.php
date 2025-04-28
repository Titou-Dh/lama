<?php
require_once __DIR__ . '/../config/database.php';
session_start();

if (isset($_SESSION['tester_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        try {
            $stmt = $cnx->prepare("SELECT id, username, password_hash FROM testers WHERE email = ? AND is_active = TRUE");
            $stmt->execute([$email]);
            $tester = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($tester && password_verify($password, $tester['password_hash'])) {
                $updateStmt = $cnx->prepare("UPDATE testers SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
                $updateStmt->execute([$tester['id']]);

                $_SESSION['tester_id'] = $tester['id'];
                $_SESSION['tester_username'] = $tester['username'];

                header('Location: index.php');
                exit;
            } else {
                $error = 'Invalid username or password';
            }
        } catch (Exception $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LAMA Test Suite - Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.2.0/dist/css/tabler.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/@tabler/core@1.2.0/dist/js/tabler.min.js"></script>
</head>

<body class="d-flex flex-column">
    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                <h2 class="navbar-brand navbar-brand-autodark">LAMA Test Suite</h2>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success" role="alert">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <div class="card card-md">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Login to your account</h2>
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="text" class="form-control" name="email" placeholder="Enter email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" placeholder="Enter password" required>
                        </div>
                        <div class="form-footer">
                            <button type="submit" name="login" class="btn btn-primary w-100">Sign in</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>