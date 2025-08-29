<?php
session_start();

// Database connection
$conn = mysqli_connect("localhost", "root", "", "kn_raam_hardware");
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Get search parameters
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$category = isset($_GET['category']) ? intval($_GET['category']) : 0;
$min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
$max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 999999;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name_asc';

// Build search query
$where_conditions = ["p.status = 'Available'"];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(p.product_name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($category > 0) {
    $where_conditions[] = "p.category_id = ?";
    $params[] = $category;
}

if ($min_price > 0) {
    $where_conditions[] = "p.price >= ?";
    $params[] = $min_price;
}

if ($max_price < 999999) {
    $where_conditions[] = "p.price <= ?";
    $params[] = $max_price;
}

$where_clause = implode(" AND ", $where_conditions);

// Sort options
$sort_clause = "ORDER BY ";
switch ($sort) {
    case 'price_asc':
        $sort_clause .= "p.price ASC";
        break;
    case 'price_desc':
        $sort_clause .= "p.price DESC";
        break;
    case 'name_desc':
        $sort_clause .= "p.product_name DESC";
        break;
    default:
        $sort_clause .= "p.product_name ASC";
}

// Prepare and execute search query
$search_query = "
    SELECT p.*, c.category_name 
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.category_id
    WHERE $where_clause
    $sort_clause
";

$stmt = mysqli_prepare($conn, $search_query);
if (!empty($params)) {
    $types = str_repeat('s', count($params));
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$search_results = mysqli_stmt_get_result($stmt);

// Get categories for filter
$categories_query = "SELECT * FROM categories ORDER BY category_name";
$categories = mysqli_query($conn, $categories_query);

// Get price range for filter
$price_range = mysqli_fetch_assoc(mysqli_query($conn, "SELECT MIN(price) as min_price, MAX(price) as max_price FROM products WHERE status='Available'"));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Search Products - K.N. Raam Hardware</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>
<body>
    <?php include('includes/navbar.php'); ?>

    <div class="container mt-5 pt-4">
        <h2 class="text-center fw-bold h-font about-title">SEARCH PRODUCTS</h2>
        <div class="h-line about-divider"></div>

        <!-- Search Form -->
        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="GET" action="search.php" class="row g-3">
                            <div class="col-md-4">
                                <label for="search" class="form-label">Search Products</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="<?= htmlspecialchars($search) ?>" 
                                       placeholder="Enter product name or description...">
                            </div>
                            <div class="col-md-2">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select" id="category" name="category">
                                    <option value="0">All Categories</option>
                                    <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                                        <option value="<?= $cat['category_id'] ?>" 
                                                <?= $category == $cat['category_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['category_name']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="min_price" class="form-label">Min Price (LKR)</label>
                                <input type="number" class="form-control" id="min_price" name="min_price" 
                                       value="<?= $min_price > 0 ? $min_price : '' ?>" min="0" step="0.01">
                            </div>
                            <div class="col-md-2">
                                <label for="max_price" class="form-label">Max Price (LKR)</label>
                                <input type="number" class="form-control" id="max_price" name="max_price" 
                                       value="<?= $max_price < 999999 ? $max_price : '' ?>" min="0" step="0.01">
                            </div>
                            <div class="col-md-2">
                                <label for="sort" class="form-label">Sort By</label>
                                <select class="form-select" id="sort" name="sort">
                                    <option value="name_asc" <?= $sort == 'name_asc' ? 'selected' : '' ?>>Name A-Z</option>
                                    <option value="name_desc" <?= $sort == 'name_desc' ? 'selected' : '' ?>>Name Z-A</option>
                                    <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>Price Low-High</option>
                                    <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Price High-Low</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i> Search
                                </button>
                                <a href="search.php" class="btn btn-secondary">
                                    <i class="bi bi-arrow-clockwise"></i> Clear Filters
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Results -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Search Results</h4>
                    <span class="text-muted">
                        <?= mysqli_num_rows($search_results) ?> product(s) found
                    </span>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <?php if (mysqli_num_rows($search_results) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($search_results)): 
                    $product_id = $row['product_id'];
                    $product_name = $row['product_name'];
                    $price = $row['price'];
                    $stock = $row['stock'];
                    $image = $row['image'] ?? '';
                    $category = $row['category_name'] ?? 'N/A';
                    $status = $row['status'];

                    $image_path = !empty($image) ? "uploads/" . htmlspecialchars($image) : "assets/images/default-product.jpg";
                ?>
                    <div class="col-md-3 col-sm-6">
                        <div class="card product-card h-100 shadow-sm">
                            <img src="<?php echo $image_path; ?>" 
                                 class="card-img-top product-img" 
                                 alt="<?php echo htmlspecialchars($product_name); ?>">
                            <div class="card-body d-flex flex-column">
                                <h5 class="fw-bold mb-2">
                                    <a href="product_details.php?product_id=<?php echo $product_id; ?>" 
                                       class="text-decoration-none text-dark">
                                        <?php echo htmlspecialchars($product_name); ?>
                                    </a>
                                </h5>
                                <p class="product-price mb-1">LKR <?php echo number_format($price, 2); ?></p>
                                <p class="product-category mb-1">Category: <?php echo htmlspecialchars($category); ?></p>
                                <p class="mb-3"><strong>Stock:</strong> <?php echo $stock; ?> units</p>

                                <?php if ($stock > 0 && $status == 'Available'): ?>
                                    <form method="POST" action="products.php" class="mt-auto">
                                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                        <input type="number" name="qty" value="1" min="1" max="<?php echo $stock; ?>" 
                                               class="form-control mb-2" required>
                                        <button type="submit" name="add_to_cart" 
                                                class="btn btn-outline-primary w-100">Add to Cart</button>
                                    </form>
                                <?php else: ?>
                                    <p class="text-danger mt-auto">Not Available for Order</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="bi bi-search" style="font-size: 3rem; color: #ccc;"></i>
                        <h4 class="mt-3 text-muted">No products found</h4>
                        <p class="text-muted">Try adjusting your search criteria or browse all products</p>
                        <a href="products.php" class="btn btn-primary">Browse All Products</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include('includes/footer.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>
</body>
</html>
