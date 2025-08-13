<?php
include('login_check.php');

$conn = new mysqli("localhost", "root", "", "kn_raam_hardware");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch categories and brands for dropdowns
$categories = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_name ASC");
$brands = $conn->query("SELECT brand_id, brand_name FROM brands ORDER BY brand_name ASC");

$error = '';

if (isset($_POST['add_product'])) {
    $name = trim($_POST['product_name']);
    $category = $_POST['category'];    // category_id
    $brand = $_POST['brand'];          // brand_id
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $description = trim($_POST['description']);
    $status = $_POST['status'];

    // Handle image upload
    $image_name = null;
    if (!empty($_FILES['image']['name'])) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['image']['type'], $allowed_types)) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image_name = uniqid('prod_') . '.' . $ext;

            // Ensure uploads folder exists
            $upload_dir = realpath(__DIR__ . '/../uploads');
            if (!$upload_dir) {
                $error = "Uploads folder does not exist. Please create the folder 'uploads' one level above the admin directory.";
            } else {
                $upload_path = $upload_dir . DIRECTORY_SEPARATOR . $image_name;
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $error = "Failed to move uploaded file.";
                }
            }
        } else {
            $error = "Invalid image format. Only JPG, PNG, GIF allowed.";
        }
    }

    if (!$error) {
        $stmt = $conn->prepare("INSERT INTO products (product_name, category_id, brand_id, price, stock, description, status, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("siidsiss", $name, $category, $brand, $price, $stock, $description, $status, $image_name);

        if ($stmt->execute()) {
            // Redirect to products.php with success message
            header("Location: products.php?msg=added");
            exit();
        } else {
            $error = "Insert failed: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Add Product - Admin | K.N. Raam Hardware</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
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
      <h2>Add New Product</h2>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
          <label class="form-label">Product Name</label>
          <input type="text" name="product_name" class="form-control" required />
        </div>
        <div class="mb-3">
          <label class="form-label">Category</label>
          <select name="category" class="form-select" required>
            <option value="">Select category</option>
            <?php while ($cat = $categories->fetch_assoc()): ?>
              <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Brand</label>
          <select name="brand" class="form-select" required>
            <option value="">Select brand</option>
            <?php while ($brand = $brands->fetch_assoc()): ?>
              <option value="<?= $brand['brand_id'] ?>"><?= htmlspecialchars($brand['brand_name']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Price (LKR)</label>
          <input type="number" step="0.01" name="price" class="form-control" required />
        </div>
        <div class="mb-3">
          <label class="form-label">Stock Quantity</label>
          <input type="number" name="stock" class="form-control" required />
        </div>
        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" rows="4"></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Status</label>
          <select name="status" class="form-select" required>
            <option value="active" selected>Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Product Image</label>
          <input type="file" name="image" accept="image/*" class="form-control" />
        </div>
        <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
        <a href="products.php" class="btn btn-secondary">Cancel</a>
      </form>
    </main>
  </div>
</div>

<!-- Bootstrap JS bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  document.getElementById('sidebarToggle')?.addEventListener('click', function () {
    const sidebar = document.getElementById('sidebarMenu');
    sidebar.classList.toggle('show');
  });
</script>

</body>
</html>

<?php
$conn->close();
?>
