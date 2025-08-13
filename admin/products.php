<?php
include('login_check.php');

$conn = mysqli_connect("localhost", "root", "", "kn_raam_hardware");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "
SELECT p.product_id, p.product_name, p.price, p.stock, p.image, 
       c.category_name, b.brand_name, p.description, p.status
FROM products p
LEFT JOIN categories c ON p.category_id = c.category_id
LEFT JOIN brands b ON p.brand_id = b.brand_id
ORDER BY p.product_id DESC;
";

$res = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Products | K.N. Raam Hardware Admin</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />

  <style>
    /* Gradient backgrounds for cards */
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

    .product-image { width: 60px; height: 60px; object-fit: contain; }
    .table-responsive { max-height: 75vh; overflow-y: auto; }
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
          <li class="nav-item"><a class="nav-link active" href="products.php"><i class="bi bi-box-seam me-2"></i>Products</a></li>
          <li class="nav-item"><a class="nav-link" href="categories.php"><i class="bi bi-tags me-2"></i>Categories</a></li>
          <li class="nav-item"><a class="nav-link" href="brands.php"><i class="bi bi-building me-2"></i>Brands</a></li>
          <li class="nav-item"><a class="nav-link" href="customers.php"><i class="bi bi-people me-2"></i>Customers</a></li>
          <li class="nav-item"><a class="nav-link" href="admins.php"><i class="bi bi-person-badge me-2"></i>Admins</a></li>
          <li class="nav-item"><a class="nav-link" href="pending_orders.php"><i class="bi bi-clock-history me-2"></i>Pending Orders</a></li>
          <li class="nav-item"><a class="nav-link" href="completed_orders.php"><i class="bi bi-check-circle me-2"></i>Completed Orders</a></li>
          <li class="nav-item mt-3 border-top pt-2"><a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
        </ul>
      </div>
    </nav>

    <!-- Main Content -->
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
      <h2 class="mb-4">Products</h2>

      <!-- Alert Message -->
      <?php
      if (isset($_GET['msg'])) {
          $message = '';
          $alertClass = 'alert-success';
          switch ($_GET['msg']) {
              case 'added': $message = 'Product added successfully.'; break;
              case 'updated': $message = 'Product updated successfully.'; break;
              case 'deleted': $message = 'Product deleted successfully.'; break;
          }
          if ($message) {
              echo "<div id='alert-msg' class='alert $alertClass'>$message</div>";
          }
      }
      ?>

      <div class="mb-3 text-end">
        <a href="add_product.php" class="btn btn-success"><i class="bi bi-plus-lg"></i> Add New Product</a>
      </div>

      <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle">
          <thead class="table-dark">
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Brand</th>
              <th>Category</th>
              <th>Price (LKR)</th>
              <th>Stock</th>
              <th>Image</th>
              <th>Description</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if ($res && mysqli_num_rows($res) > 0) {
                while ($row = mysqli_fetch_assoc($res)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['product_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['brand_name'] ?? 'N/A') . "</td>";
                    echo "<td>" . htmlspecialchars($row['category_name'] ?? 'N/A') . "</td>";
                    echo "<td>" . number_format($row['price'], 2) . "</td>";
                    echo "<td>" . intval($row['stock']) . "</td>";
                    echo "<td>";
                    if (!empty($row['image'])) {
                        echo "<img src='../uploads/" . htmlspecialchars($row['image']) . "' alt='Product Image' class='product-image' />";
                    } else { echo "No Image"; }
                    echo "</td>";
                    echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                    echo "<td>";
                    echo "<a href='edit_product.php?id=" . urlencode($row['product_id']) . "' class='btn btn-primary btn-sm me-1'><i class='bi bi-pencil'></i></a>";
                    echo "<a href='delete_product.php?id=" . urlencode($row['product_id']) . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this product?\");'><i class='bi bi-trash'></i></a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo '<tr><td colspan="10" class="text-center">No products found.</td></tr>';
            }
            ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</div>

<!-- Bootstrap JS bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // Sidebar toggle for mobile
  document.getElementById('sidebarToggle')?.addEventListener('click', function () {
    const sidebar = document.getElementById('sidebarMenu');
    sidebar.classList.toggle('show');
  });

  // Auto-hide alert after 2.5 seconds
  const alertMsg = document.getElementById('alert-msg');
  if (alertMsg) {
      setTimeout(() => {
          alertMsg.style.transition = 'opacity 0.5s';
          alertMsg.style.opacity = '0';
          setTimeout(() => alertMsg.remove(), 500);
      }, 2500);
  }
</script>

</body>
</html>
<?php mysqli_close($conn); ?>
