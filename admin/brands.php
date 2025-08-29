<?php
$page_title = "Brands";
include('header.php');

$message = '';
$error = '';

// Handle brand creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_brand'])) {
    $brand_name = mysqli_real_escape_string($conn, $_POST['brand_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    // Check if brand already exists
    $check_sql = "SELECT brand_id FROM brands WHERE brand_name = '$brand_name'";
    $check_result = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($check_result) > 0) {
        $error = "Brand already exists.";
    } else {
        $insert_sql = "INSERT INTO brands (brand_name, description) VALUES ('$brand_name', '$description')";
        if (mysqli_query($conn, $insert_sql)) {
            $message = "Brand added successfully!";
        } else {
            $error = "Error adding brand: " . mysqli_error($conn);
        }
    }
}

// Handle brand deletion
if (isset($_GET['delete']) && $_GET['delete'] > 0) {
    $delete_id = intval($_GET['delete']);
    
    // Check if brand is used in products
    $check_products = mysqli_query($conn, "SELECT product_id FROM products WHERE brand_id = $delete_id");
    if (mysqli_num_rows($check_products) > 0) {
        $error = "Cannot delete brand. It is used by existing products.";
    } else {
        $delete_sql = "DELETE FROM brands WHERE brand_id = $delete_id";
        if (mysqli_query($conn, $delete_sql)) {
            $message = "Brand deleted successfully!";
        } else {
            $error = "Error deleting brand: " . mysqli_error($conn);
        }
    }
}

// Fetch all brands
$sql = "SELECT * FROM brands ORDER BY brand_name ASC";
$result = mysqli_query($conn, $sql);
?>

<h2 class="mb-4">Brands Management</h2>

<?php if ($message): ?>
    <div class="alert alert-success" id="successAlert"><?= $message; ?></div>
    <script>setTimeout(() => { document.getElementById('successAlert').style.display = 'none'; }, 3000);</script>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger" id="errorAlert"><?= $error; ?></div>
    <script>setTimeout(() => { document.getElementById('errorAlert').style.display = 'none'; }, 3000);</script>
<?php endif; ?>

<div class="row">
    <!-- Add New Brand -->
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Add New Brand</h5>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label for="brand_name" class="form-label">Brand Name</label>
                        <input type="text" class="form-control" id="brand_name" name="brand_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    
                    <button type="submit" name="add_brand" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Add Brand
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Brands List -->
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-building me-2"></i>Brands List</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Brand Name</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                                <?php $sn = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?= $sn++; ?></td>
                                        <td><?= htmlspecialchars($row['brand_name'] ?? ''); ?></td>
                                        <td><?= htmlspecialchars($row['description'] ?? ''); ?></td>
                                        <td>
                                            <a href="edit_brand.php?id=<?= $row['brand_id']; ?>" class="btn btn-primary btn-sm me-1">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <a href="?delete=<?= $row['brand_id']; ?>" class="btn btn-danger btn-sm" 
                                               onclick="return confirm('Are you sure you want to delete this brand?')">
                                                <i class="bi bi-trash"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">No brands found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>
