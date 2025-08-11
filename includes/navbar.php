<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Connect to database (only if not already connected)
if (!isset($conn) || !$conn) {
    $conn = mysqli_connect("localhost", "root", "", "kn_raam_hardware");
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }
}

// Get cart count if logged in
$cart_count = 0;
if (isset($_SESSION['customer_id'])) {
    $uid = $_SESSION['customer_id'];
    $cart_res = mysqli_query($conn, "SELECT SUM(qty) AS total_qty FROM cart WHERE user_id = $uid");
    $cart_row = mysqli_fetch_assoc($cart_res);
    $cart_count = $cart_row['total_qty'] ?? 0;
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
  <div class="container">

    <a class="navbar-brand fw-bold" href="index.php">K.N. Raam Hardware</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
      <!-- Main nav links container -->
      <ul class="navbar-nav mx-auto main-nav">
        <li class="nav-item">
          <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>" href="index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : '' ?>" href="products.php">Products</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : '' ?>" href="about.php">About</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : '' ?>" href="contact.php">Contact</a>
        </li>
      </ul>

      <!-- User auth links container -->
      <ul class="navbar-nav ms-auto user-nav">
        <?php if (isset($_SESSION['customer_id'])): ?>
          <li class="nav-item">
            <a class="nav-link" href="cart.php">
              My Cart 
              <span class="badge bg-danger"><?= $cart_count ?></span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-warning fw-bold" href="profile.php">Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="logout.php">Logout</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link" href="login.php">Login</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="register.php">Sign Up</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
