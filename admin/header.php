<?php
include('login_check.php'); // Keep your authentication check

// Database connection (make it available globally)
$conn = mysqli_connect("localhost", "root", "", "kn_raam_hardware");
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Optional: Fetch total pending messages for dashboard badge
$total_pending_messages = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM contact_messages WHERE status='Pending'"))['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?php echo isset($page_title) ? $page_title : "Admin Panel"; ?> | K.N. Raam Hardware</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />

  <style>
    /* Dashboard card gradients */
    .card-gradient-primary { background: linear-gradient(135deg, #0d6efd, #6610f2); color: #fff; }
    .card-gradient-success { background: linear-gradient(135deg, #198754, #20c997); color: #fff; }
    .card-gradient-warning { background: linear-gradient(135deg, #ffc107, #ff922b); color: #212529; }
    .card-gradient-info { background: linear-gradient(135deg, #0dcaf0, #3bc9db); color: #212529; }
    .card-gradient-secondary { background: linear-gradient(135deg, #6c757d, #495057); color: #fff; }
    .card-gradient-dark { background: linear-gradient(135deg, #212529, #343a40); color: #fff; }
    .card-gradient-danger { background: linear-gradient(135deg, #dc3545, #bb2d3b); color: #fff; }
    .card-hover:hover { filter: brightness(1.1); cursor: pointer; transition: 0.3s; }
    #sidebarMenu { min-height: 100vh; }
    @media (max-width: 767.98px) {
      #sidebarMenu { position: fixed; z-index: 1030; top: 56px; height: calc(100% - 56px); background-color: #fff; }
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-dark bg-dark sticky-top shadow">
  <div class="container-fluid">
    <button class="btn btn-outline-light me-2 d-md-none" id="sidebarToggle"><i class="bi bi-list"></i></button>
    <span class="navbar-brand mb-0 h1">K.N. Raam Hardware - Admin Panel</span>
  </div>
</nav>

<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-white sidebar shadow-sm collapse show">
      <div class="position-sticky pt-3">
        <ul class="nav flex-column">
          <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="orders.php"><i class="bi bi-cart-check me-2"></i>Orders</a></li>
          <li class="nav-item"><a class="nav-link" href="products.php"><i class="bi bi-box-seam me-2"></i>Products</a></li>
          <li class="nav-item"><a class="nav-link" href="categories.php"><i class="bi bi-tags me-2"></i>Categories</a></li>
          <li class="nav-item"><a class="nav-link" href="brands.php"><i class="bi bi-building me-2"></i>Brands</a></li>
          <li class="nav-item"><a class="nav-link" href="customers.php"><i class="bi bi-people me-2"></i>Customers</a></li>
          <li class="nav-item"><a class="nav-link" href="admins.php"><i class="bi bi-person-badge me-2"></i>Admins</a></li>
          <li class="nav-item"><a class="nav-link" href="pending_orders.php"><i class="bi bi-clock-history me-2"></i>Pending Orders</a></li>
          <li class="nav-item"><a class="nav-link" href="completed_orders.php"><i class="bi bi-check-circle me-2"></i>Completed Orders</a></li>
          <li class="nav-item">
            <a class="nav-link d-flex justify-content-between align-items-center" href="messages.php">
              <span><i class="bi bi-envelope me-2"></i>Messages</span>
              <?php if($total_pending_messages > 0): ?>
                <span class="badge bg-danger rounded-pill"><?= $total_pending_messages; ?></span>
              <?php endif; ?>
            </a>
          </li>
          <li class="nav-item mt-3 border-top pt-2"><a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
        </ul>
      </div>
    </nav>

    <!-- Main Content Start -->
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
