<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['customer_id'] ?? null;

// DB connection
$conn = new mysqli("localhost", "root", "", "kn_raam_hardware");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Fetch username if logged in
$user_name = '';
if ($user_id) {
    $stmt = $conn->prepare("SELECT username FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($user_name);
    $stmt->fetch();
    $stmt->close();
}

// Helper to check active page
function isActive($page) {
    return basename($_SERVER['PHP_SELF']) == $page ? 'active' : '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>K.N. Raam Hardware</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
    <!-- Custom CSS -->
    <link href="assets/css/style.css?v=<?php echo time(); ?>" rel="stylesheet" />
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow fixed-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">K.N. Raam Hardware</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav mx-auto main-nav">
                <li class="nav-item"><a class="nav-link <?= isActive('index.php') ?>" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link <?= isActive('products.php') ?>" href="products.php">Products</a></li>
                <li class="nav-item"><a class="nav-link <?= isActive('about.php') ?>" href="about.php">About</a></li>
                <li class="nav-item"><a class="nav-link <?= isActive('contact.php') ?>" href="contact.php">Contact</a></li>
            </ul>

            <ul class="navbar-nav ms-auto user-nav">
                <?php if ($user_id): ?>
                    <li class="nav-item"><a class="nav-link" href="cart.php">My Cart</a></li>
                    <li class="nav-item"><a class="nav-link text-warning fw-bold" href="profile.php">Welcome, <?= htmlspecialchars($user_name) ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="register.php">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Add top padding to prevent content being hidden under fixed navbar -->
<div style="padding-top: 70px;"></div>
