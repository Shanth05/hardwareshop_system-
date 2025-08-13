<?php
include('login_check.php');

$conn = mysqli_connect("localhost", "root", "", "kn_raam_hardware");
if (!$conn) die("Connection failed: " . mysqli_connect_error());

$error = '';

if (isset($_POST['add_category'])) {
    $name = trim($_POST['category_name']);
    $description = trim($_POST['description']);

    if (!$name) {
        $error = "Category name is required.";
    } else {
        // Check if category name already exists
        $check = $conn->prepare("SELECT category_id FROM categories WHERE category_name = ?");
        $check->bind_param("s", $name);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Category name already exists. Please use a different name.";
        } else {
            $stmt = $conn->prepare("INSERT INTO categories (category_name, description) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $description);

            if ($stmt->execute()) {
                // Redirect to categories.php with success message
                header("Location: categories.php?msg=added");
                exit();
            } else {
                $error = "Insert failed: " . $stmt->error;
            }
            $stmt->close();
        }
        $check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Add Category - K.N. Raam Hardware Admin</title>
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
      <h2 class="mb-4">Add New Category</h2>

      <?php if (!empty($error)): ?>
        <div id="error-alert" class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <script>
          setTimeout(() => { document.getElementById('error-alert').style.display = 'none'; }, 2000);
        </script>
      <?php endif; ?>

      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Category Name</label>
          <input type="text" name="category_name" class="form-control" required />
        </div>
        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" rows="3"></textarea>
        </div>
        <button type="submit" name="add_category" class="btn btn-primary">Add Category</button>
        <a href="categories.php" class="btn btn-secondary">Cancel</a>
      </form>
    </main>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.getElementById('sidebarToggle')?.addEventListener('click', function () {
    document.getElementById('sidebarMenu').classList.toggle('show');
  });
</script>

</body>
</html>

<?php mysqli_close($conn); ?>
