<?php
session_start();

// Database connection
$conn = mysqli_connect("localhost", "root", "", "kn_raam_hardware");
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Get product ID
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

if ($product_id <= 0) {
    header("Location: products.php");
    exit();
}

// Fetch product details
$product_query = "
    SELECT p.*, c.category_name 
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.category_id
    WHERE p.product_id = $product_id
";
$product_result = mysqli_query($conn, $product_query);

if (mysqli_num_rows($product_result) == 0) {
    header("Location: products.php");
    exit();
}

$product = mysqli_fetch_assoc($product_result);

// Handle Add to Cart
if (isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['customer_id'])) {
        echo '<script>alert("Please login to add products to cart."); window.location.href="login.php";</script>';
        exit();
    }

    $user_id = intval($_SESSION['customer_id']);
    $qty = intval($_POST['qty']);
    if ($qty < 1) $qty = 1;

    $check = mysqli_query($conn, "SELECT qty FROM cart WHERE user_id=$user_id AND product_id=$product_id");
    if (mysqli_num_rows($check) > 0) {
        $row = mysqli_fetch_assoc($check);
        $new_qty = $row['qty'] + $qty;
        $update = mysqli_query($conn, "UPDATE cart SET qty = $new_qty WHERE user_id = $user_id AND product_id = $product_id");
        echo '<script>alert("' . ($update ? 'Cart updated successfully.' : 'Failed to update cart.') . '");</script>';
    } else {
        $insert = mysqli_query($conn, "INSERT INTO cart (user_id, product_id, qty) VALUES ($user_id, $product_id, $qty)");
        echo '<script>alert("' . ($insert ? 'Product added to cart.' : 'Failed to add product to cart.') . '");</script>';
    }
}

// Fetch related products
$related_query = "
    SELECT p.*, c.category_name 
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.category_id
    WHERE p.category_id = {$product['category_id']} 
    AND p.product_id != $product_id 
    AND p.status = 'Available'
    LIMIT 4
";
$related_products = mysqli_query($conn, $related_query);

$image_path = !empty($product['image']) ? "uploads/" . htmlspecialchars($product['image']) : "assets/images/default-product.jpg";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($product['product_name']) ?> - K.N. Raam Hardware</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    
    <!-- Open Graph Meta Tags for Social Sharing -->
    <meta property="og:title" content="<?= htmlspecialchars($product['product_name']) ?> - K.N. Raam Hardware">
    <meta property="og:description" content="<?= htmlspecialchars(substr($product['description'] ?? '', 0, 160)) ?>">
    <meta property="og:image" content="<?= $image_path ?>">
    <meta property="og:url" content="<?= $_SERVER['REQUEST_URI'] ?>">
    <meta property="og:type" content="product">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($product['product_name']) ?> - K.N. Raam Hardware">
    <meta name="twitter:description" content="<?= htmlspecialchars(substr($product['description'] ?? '', 0, 160)) ?>">
    <meta name="twitter:image" content="<?= $image_path ?>">
</head>
<body>
    <?php include('includes/navbar.php'); ?>

    <div class="container mt-5 pt-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="products.php">Products</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['product_name']) ?></li>
            </ol>
        </nav>

        <div class="row">
            <!-- Product Image -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm">
                    <img src="<?= $image_path ?>" 
                         class="card-img-top product-detail-img" 
                         alt="<?= htmlspecialchars($product['product_name']) ?>"
                         style="max-height: 500px; object-fit: contain;">
                </div>
            </div>

            <!-- Product Details -->
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h1 class="h2 fw-bold mb-3"><?= htmlspecialchars($product['product_name']) ?></h1>
                        
                        <div class="mb-3">
                            <span class="h3 text-primary fw-bold">LKR <?= number_format($product['price'], 2) ?></span>
                        </div>

                        <div class="mb-3">
                            <span class="badge bg-secondary me-2"><?= htmlspecialchars($product['category_name'] ?? 'N/A') ?></span>
                            <span class="badge <?= $product['status'] == 'Available' ? 'bg-success' : 'bg-danger' ?>">
                                <?= htmlspecialchars($product['status']) ?>
                            </span>
                        </div>

                        <div class="mb-3">
                            <strong>Stock Available:</strong> <?= $product['stock'] ?> units
                        </div>

                        <?php if (!empty($product['description'])): ?>
                            <div class="mb-4">
                                <h5>Description</h5>
                                <p class="text-muted"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                            </div>
                        <?php endif; ?>

                        <!-- Add to Cart Form -->
                        <?php if ($product['stock'] > 0 && $product['status'] == 'Available'): ?>
                            <form method="POST" action="" class="mb-4">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="qty" class="form-label">Quantity</label>
                                        <input type="number" id="qty" name="qty" value="1" min="1" 
                                               max="<?= $product['stock'] ?>" class="form-control" required>
                                    </div>
                                    <div class="col-md-8 d-flex align-items-end">
                                        <button type="submit" name="add_to_cart" class="btn btn-primary btn-lg w-100">
                                            <i class="bi bi-cart-plus"></i> Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i> This product is currently not available for order.
                            </div>
                        <?php endif; ?>

                        <!-- Social Sharing -->
                        <div class="border-top pt-3">
                            <h6 class="mb-2">Share this product:</h6>
                            <div class="d-flex gap-2">
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($_SERVER['REQUEST_URI']) ?>" 
                                   target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-facebook"></i> Facebook
                                </a>
                                <a href="https://twitter.com/intent/tweet?text=<?= urlencode('Check out this product: ' . $product['product_name']) ?>&url=<?= urlencode($_SERVER['REQUEST_URI']) ?>" 
                                   target="_blank" class="btn btn-outline-info btn-sm">
                                    <i class="bi bi-twitter"></i> Twitter
                                </a>
                                <a href="https://wa.me/?text=<?= urlencode('Check out this product: ' . $product['product_name'] . ' - ' . $_SERVER['REQUEST_URI']) ?>" 
                                   target="_blank" class="btn btn-outline-success btn-sm">
                                    <i class="bi bi-whatsapp"></i> WhatsApp
                                </a>
                                <button onclick="copyToClipboard()" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-link-45deg"></i> Copy Link
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <?php if (mysqli_num_rows($related_products) > 0): ?>
            <div class="row mt-5">
                <div class="col-12">
                    <h3 class="mb-4">Related Products</h3>
                    <div class="row g-4">
                        <?php while ($related = mysqli_fetch_assoc($related_products)): 
                            $related_image = !empty($related['image']) ? "uploads/" . htmlspecialchars($related['image']) : "assets/images/default-product.jpg";
                        ?>
                            <div class="col-md-3 col-sm-6">
                                <div class="card product-card h-100 shadow-sm">
                                    <img src="<?= $related_image ?>" 
                                         class="card-img-top product-img" 
                                         alt="<?= htmlspecialchars($related['product_name']) ?>">
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="fw-bold mb-2">
                                            <a href="product_details.php?product_id=<?= $related['product_id'] ?>" 
                                               class="text-decoration-none text-dark">
                                                <?= htmlspecialchars($related['product_name']) ?>
                                            </a>
                                        </h5>
                                        <p class="product-price mb-1">LKR <?= number_format($related['price'], 2) ?></p>
                                        <p class="product-category mb-1">Category: <?= htmlspecialchars($related['category_name'] ?? 'N/A') ?></p>
                                        
                                        <?php if ($related['stock'] > 0 && $related['status'] == 'Available'): ?>
                                            <form method="POST" action="products.php" class="mt-auto">
                                                <input type="hidden" name="product_id" value="<?= $related['product_id'] ?>">
                                                <input type="number" name="qty" value="1" min="1" max="<?= $related['stock'] ?>" 
                                                       class="form-control mb-2" required>
                                                <button type="submit" name="add_to_cart" 
                                                        class="btn btn-outline-primary w-100">Add to Cart</button>
                                            </form>
                                        <?php else: ?>
                                            <p class="text-danger mt-auto">Not Available</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include('includes/footer.php'); ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/script.js"></script>
    <script>
        function copyToClipboard() {
            navigator.clipboard.writeText(window.location.href).then(function() {
                showNotification('Link copied to clipboard!', 'success');
            }, function(err) {
                console.error('Could not copy text: ', err);
                showNotification('Failed to copy link', 'error');
            });
        }
    </script>
</body>
</html>
