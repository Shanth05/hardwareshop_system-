<?php
include('login_check.php');

$conn = mysqli_connect("localhost", "root", "", "kn_raam_hardware");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch all categories
$res = mysqli_query($conn, "SELECT * FROM categories ORDER BY category_name ASC");

// Handle messages from Add/Edit/Delete
$msg = $_GET['msg'] ?? '';
$alert = '';
if ($msg == 'added') {
    $alert = '<div id="alert-msg" class="alert alert-success">Category added successfully.</div>';
} elseif ($msg == 'updated') {
    $alert = '<div id="alert-msg" class="alert alert-success">Category updated successfully.</div>';
} elseif ($msg == 'deleted') {
    $alert = '<div id="alert-msg" class="alert alert-success">Category deleted successfully.</div>';
} elseif ($msg == 'delete_error') {
    $alert = '<div id="alert-msg" class="alert alert-danger">Failed to delete category.</div>';
} elseif ($msg == 'invalid') {
    $alert = '<div id="alert-msg" class="alert alert-warning">Invalid category ID.</div>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Categories | K.N. Raam Hardware Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
  <style>
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
          <li class="nav-item"><a class="nav-link active" href="categories.php"><i class="bi bi-tags me-2"></i>Categories</a></li>
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
      <h2 class="mb-4">Categories</h2>

      <!-- Alert Messages -->
      <?= $alert ?>
      <script>
        setTimeout(() => {
          const el = document.getElementById('alert-msg');
          if(el) el.style.display = 'none';
        }, 2500);
      </script>

      <div class="mb-3 text-end">
        <a href="add_category.php" class="btn btn-success"><i class="bi bi-plus-lg"></i> Add New Category</a>
      </div>

      <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle">
          <thead class="table-dark">
            <tr>
              <th>ID</th>
              <th>Category Name</th>
              <th>Description</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if ($res && mysqli_num_rows($res) > 0) {
                while ($row = mysqli_fetch_assoc($res)) {
                    echo "<tr>";
                    echo "<td>" . intval($row['category_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['category_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['description'] ?? '') . "</td>";
                    echo "<td>";
                    echo "<a href='edit_category.php?id=" . urlencode($row['category_id']) . "' class='btn btn-primary btn-sm me-1'><i class='bi bi-pencil'></i></a>";
                    echo "<a href='delete_category.php?id=" . urlencode($row['category_id']) . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this category?\");'><i class='bi bi-trash'></i></a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo '<tr><td colspan="4" class="text-center">No categories found.</td></tr>';
            }
            ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.getElementById('sidebarToggle')?.addEventListener('click', function () {
    const sidebar = document.getElementById('sidebarMenu');
    sidebar.classList.toggle('show');
  });
</script>

</body>
</html>
<?php mysqli_close($conn); ?>
