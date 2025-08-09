<?php
// Make sure session is started and login checked outside this snippet
// include('login_check.php');

$user_id = $_SESSION['customer_id'] ?? null;

// Connect once to hardware shop DB
$conn = new mysqli("localhost", "root", "", "kn_raam_hardware");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user info if logged in
$name = '';
if ($user_id) {
    $stmt = $conn->prepare("SELECT name FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($name);
    $stmt->fetch();
    $stmt->close();
}

// Count cart items
$cart_count = 0;
if ($user_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($cart_count);
    $stmt->fetch();
    $stmt->close();
}
?>

<nav class="navbar navbar-expand-lg bg-white px-lg-3 py-lg-2 shadow-sm sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand me-5 fw-bold fs-3 h-font" href="index.php">K.N. Raam Hardware</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" 
            aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarScroll">
      <ul class="navbar-nav ms-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
        
        <li class="nav-item">
          <a class="nav-link me-2" href="index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link me-2" href="products.php">Our Products</a>
        </li>
        <li class="nav-item">
          <a class="nav-link me-2" href="brands.php">Brands</a>
        </li>
        <li class="nav-item">
          <a class="nav-link me-2" href="contactUs.php">Contact Us</a>
        </li>
        <li class="nav-item">
          <a class="nav-link me-2" href="about.php">About</a>
        </li>

        <?php if (!$user_id): ?>
          <li class="nav-item">
            <a class="nav-link me-2" href="login.php">Login</a>
          </li>
          <li class="nav-item">
            <a class="nav-link me-2" href="register.php">Sign Up</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link me-2" href="cart.php">Cart(<?php echo $cart_count; ?>)</a>
          </li>
          <li class="nav-item">
            <a class="nav-link me-2" href="my_profile.php"><?php echo htmlspecialchars($name); ?></a>
          </li>
          <li class="nav-item">
            <a class="nav-link me-2" href="logout.php">Logout</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
