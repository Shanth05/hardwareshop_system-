<?php
include('login_check.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Dashboard | K.N. Raam Hardware</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />

  <style>
    /* Gradient backgrounds for cards */
    .card-gradient-primary {
      background: linear-gradient(135deg, #0d6efd, #6610f2);
      color: #fff;
    }
    .card-gradient-success {
      background: linear-gradient(135deg, #198754, #20c997);
      color: #fff;
    }
    .card-gradient-warning {
      background: linear-gradient(135deg, #ffc107, #ff922b);
      color: #212529;
    }
    .card-gradient-info {
      background: linear-gradient(135deg, #0dcaf0, #3bc9db);
      color: #212529;
    }
    .card-gradient-secondary {
      background: linear-gradient(135deg, #6c757d, #495057);
      color: #fff;
    }
    .card-gradient-dark {
      background: linear-gradient(135deg, #212529, #343a40);
      color: #fff;
    }
    .card-gradient-danger {
      background: linear-gradient(135deg, #dc3545, #bb2d3b);
      color: #fff;
    }

    .card-hover:hover {
      filter: brightness(1.1);
      cursor: pointer;
      transition: 0.3s;
    }

    /* Sidebar style without title */
    #sidebarMenu {
      min-height: 100vh;
    }

    /* Sidebar toggle for mobile */
    @media (max-width: 767.98px) {
      #sidebarMenu {
        position: fixed;
        z-index: 1030;
        top: 56px; /* navbar height */
        height: calc(100% - 56px);
        background-color: #fff;
      }
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
    <nav
      id="sidebarMenu"
      class="col-md-3 col-lg-2 d-md-block bg-white sidebar shadow-sm collapse show"
      style="min-height: 100vh;"
    >
      <div class="position-sticky pt-3">
        <ul class="nav flex-column">
          <li class="nav-item"><a class="nav-link active" href="dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="customers.php"><i class="bi bi-people me-2"></i>Registered Customers</a></li>
          <li class="nav-item"><a class="nav-link" href="orders.php"><i class="bi bi-cart-check me-2"></i>Orders</a></li>
          <li class="nav-item"><a class="nav-link" href="admins.php"><i class="bi bi-person-badge me-2"></i>Registered Admins</a></li>
          <li class="nav-item"><a class="nav-link" href="products.php"><i class="bi bi-box-seam me-2"></i>Products</a></li>
          <li class="nav-item"><a class="nav-link" href="categories.php"><i class="bi bi-tags me-2"></i>Categories</a></li>
          <li class="nav-item"><a class="nav-link" href="brands.php"><i class="bi bi-building me-2"></i>Brands</a></li>
          <li class="nav-item"><a class="nav-link" href="pending_orders.php"><i class="bi bi-clock-history me-2"></i>Pending Orders</a></li>
          <li class="nav-item"><a class="nav-link" href="completed_orders.php"><i class="bi bi-check-circle me-2"></i>Completed Orders</a></li>
          <li class="nav-item mt-3 border-top pt-2"><a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
        </ul>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
      <h2 class="mb-4">Dashboard Overview</h2>

      <!-- Dashboard Cards -->
      <div class="row g-4">
        <div class="col-md-3">
          <div class="card card-gradient-primary card-hover p-3 text-center shadow" onclick="location.href='customers.php'">
            <h5>Registered Customers</h5>
            <h2>7</h2>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card card-gradient-success card-hover p-3 text-center shadow" onclick="location.href='orders.php'">
            <h5>Orders</h5>
            <h2>20</h2>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card card-gradient-warning card-hover p-3 text-center shadow" onclick="location.href='admins.php'">
            <h5>Registered Admins</h5>
            <h2>2</h2>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card card-gradient-info card-hover p-3 text-center shadow" onclick="location.href='products.php'">
            <h5>Products</h5>
            <h2>2</h2>
          </div>
        </div>
      </div>

      <div class="row g-4 mt-1">
        <div class="col-md-3">
          <div class="card card-gradient-secondary card-hover p-3 text-center shadow" onclick="location.href='categories.php'">
            <h5>Categories</h5>
            <h2>3</h2>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card card-gradient-dark card-hover p-3 text-center shadow" onclick="location.href='brands.php'">
            <h5>Brands</h5>
            <h2>0</h2>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card card-gradient-danger card-hover p-3 text-center shadow" onclick="location.href='pending_orders.php'">
            <h5>Pending Orders</h5>
            <h2>0</h2>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card card-gradient-success card-hover p-3 text-center shadow" onclick="location.href='completed_orders.php'">
            <h5>Completed Orders</h5>
            <h2>0</h2>
          </div>
        </div>
      </div>

      <!-- Latest Orders & Sales Chart -->
      <div class="row mt-5">
        <!-- Latest Orders -->
        <div class="col-md-6">
          <div class="card shadow">
            <div class="card-header">
              <h5 class="mb-0">Latest Orders</h5>
            </div>
            <div class="card-body p-0">
              <table class="table mb-0">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>1</td>
                    <td>John Doe</td>
                    <td>LKR 5,000</td>
                    <td><span class="badge bg-warning">Pending</span></td>
                  </tr>
                  <tr>
                    <td>2</td>
                    <td>Mary Jane</td>
                    <td>LKR 3,200</td>
                    <td><span class="badge bg-success">Completed</span></td>
                  </tr>
                  <tr>
                    <td>3</td>
                    <td>David Smith</td>
                    <td>LKR 1,500</td>
                    <td><span class="badge bg-danger">Cancelled</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Sales Chart -->
        <div class="col-md-6">
          <div class="card shadow">
            <div class="card-header">
              <h5 class="mb-0">Sales Overview</h5>
            </div>
            <div class="card-body">
              <canvas id="salesChart" height="200"></canvas>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>

<!-- Bootstrap JS bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  // Sidebar toggle for mobile
  document.getElementById('sidebarToggle').addEventListener('click', function () {
    const sidebar = document.getElementById('sidebarMenu');
    sidebar.classList.toggle('show');
  });

  // Sales Chart
  const ctx = document.getElementById('salesChart').getContext('2d');
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
      datasets: [{
        label: 'Sales (LKR)',
        data: [5000, 7000, 8000, 6000, 9000, 12000],
        borderColor: '#0d6efd',
        backgroundColor: 'rgba(13, 110, 253, 0.1)',
        tension: 0.4,
        fill: true
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: true }
      }
    }
  });
</script>

</body>
</html>    