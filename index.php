<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>K.N. Raam Hardware</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom Font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }

    .nav-link {
      position: relative;
      transition: color 0.3s;
    }

    .nav-link::after {
      content: "";
      position: absolute;
      width: 0%;
      height: 2px;
      bottom: 0;
      left: 0;
      background-color: #000;
      transition: width 0.3s;
    }

    .nav-link:hover::after {
      width: 100%;
    }

    .navbar {
      animation: fadeDown 0.6s ease-in-out;
    }

    @keyframes fadeDown {
      from {
        transform: translateY(-20px);
        opacity: 0;
      }
      to {
        transform: translateY(0);
        opacity: 1;
      }
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
  <div class="container">
    <!-- Center Brand -->
    <div class="mx-auto order-0">
      <a class="navbar-brand fw-bold fs-3 text-center" href="index.php">K.N. Raam Hardware</a>
    </div>

    <!-- Toggler for Mobile -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Center Navigation -->
    <div class="collapse navbar-collapse justify-content-center order-lg-1" id="navbarMain">
      <ul class="navbar-nav mx-auto text-center">
        <li class="nav-item"><a class="nav-link mx-2" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link mx-2" href="products.php">Products</a></li>
        <li class="nav-item"><a class="nav-link mx-2" href="brands.php">Brands</a></li>
        <li class="nav-item"><a class="nav-link mx-2" href="contactUs.php">Contact</a></li>
        <li class="nav-item"><a class="nav-link mx-2" href="about.php">About</a></li>
      </ul>
    </div>

    <!-- Right User/Cart/Login Section -->
    <div class="d-flex align-items-center order-lg-2">
      <ul class="navbar-nav">
        <?php if (!isset($_SESSION['customer_id'])): ?>
          <li class="nav-item"><a class="nav-link mx-2" href="login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link mx-2" href="register.php">Sign Up</a></li>
        <?php else:
          $user_id = $_SESSION['customer_id'];
          $conn = mysqli_connect('localhost','root','','kn_raam_hardware');

          $cart_query = mysqli_query($conn, "SELECT * FROM cart WHERE user_id=$user_id");
          $cart_count = mysqli_num_rows($cart_query);
        ?>
          <li class="nav-item"><a class="nav-link mx-2" href="cart.php">Cart(<?php echo $cart_count; ?>)</a></li>
          <li class="nav-item">
            <a class="nav-link mx-2" href="my_profile.php">
              <?php
              $name_query = mysqli_query($conn, "SELECT name FROM users WHERE user_id=$user_id");
              if ($row = mysqli_fetch_assoc($name_query)) echo $row['name'];
              ?>
            </a>
          </li>
          <li class="nav-item"><a class="nav-link mx-2" href="logout.php">Logout</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- Hero Section or Homepage Content -->
<div class="container mt-5 text-center">
  <h1>Welcome to K.N. Raam Hardware</h1>
  <p class="lead">Your trusted destination for quality hardware tools and building materials.</p>
  <a href="products.php" class="btn btn-dark mt-3">Shop Now</a>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
