<?php
session_start();

// Database connection
$conn = mysqli_connect("localhost", "root", "", "kn_raam_hardware");
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Handle Add to Cart form submission
if (isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['customer_id'])) {
        echo '<script>alert("Please login to add products to cart."); window.location.href="login.php";</script>';
        exit();
    }

    $user_id = intval($_SESSION['customer_id']);
    $product_id = intval($_POST['product_id']);
    $qty = intval($_POST['qty']);
    if ($qty < 1) $qty = 1;

    $check = mysqli_query($conn, "SELECT qty FROM cart WHERE user_id=$user_id AND product_id=$product_id");
    if (mysqli_num_rows($check) > 0) {
        $row = mysqli_fetch_assoc($check);
        $new_qty = $row['qty'] + $qty;
        $update = mysqli_query($conn, "UPDATE cart SET qty = $new_qty WHERE user_id = $user_id AND product_id = $product_id");
        echo '<script>alert("' . ($update ? 'Cart updated successfully.' : 'Failed to update cart.') . '"); window.location.href="products.php";</script>';
        exit();
    } else {
        $insert = mysqli_query($conn, "INSERT INTO cart (user_id, product_id, qty) VALUES ($user_id, $product_id, $qty)");
        echo '<script>alert("' . ($insert ? 'Product added to cart.' : 'Failed to add product to cart.') . '"); window.location.href="products.php";</script>';
        exit();
    }
}

// Fetch available products with category name
$products = mysqli_query($conn, "
    SELECT p.*, c.category_name 
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.category_id
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>K.N. Raam Hardware - Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>" />
</head>
<body>
<?php include('includes/navbar.php'); ?>

<div class="container mt-5 pt-4">
    <h2 class="text-center fw-bold h-font about-title">OUR PRODUCTS</h2>
    <div class="h-line about-divider"></div>

    <div class="row g-4">
        <?php while ($row = mysqli_fetch_assoc($products)) :
            $product_id = $row['product_id'];
            $product_name = $row['product_name'];
            $price = $row['price'];
            $stock = $row['stock'];
            $image = $row['image'] ?? '';
            $category = $row['category_name'] ?? 'N/A';
            $status = $row['status'];

            // Use default image if missing
            $image_path = !empty($image) ? "images/products/" . htmlspecialchars($image) : "images/products/default.jpg";

            // Status color classes
            $status_class = '';
            if ($status == 'Available') {
                $status_class = 'text-success fw-bold';
            } elseif ($status == 'Unavailable') {
                $status_class = 'text-warning fw-bold';
            } elseif ($status == 'Out of Stock') {
                $status_class = 'text-danger fw-bold';
            }
        ?>
            <div class="col-md-3 col-sm-6">
                <div class="card product-card h-100">
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
                        <p class="mb-3"><strong>Status:</strong> <span class="<?php echo $status_class; ?>"><?php echo htmlspecialchars($status); ?></span></p>

                        <?php if ($stock > 0 && $status == 'Available') : ?>
                            <form method="POST" action="" class="mt-auto">
                                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                <input type="number" name="qty" value="1" min="1" max="<?php echo $stock; ?>" 
                                       class="form-control mb-2" required>
                                <button type="submit" name="add_to_cart" 
                                        class="btn btn-outline-primary w-100">Add to Cart</button>
                            </form>
                        <?php else : ?>
                            <p class="text-danger mt-auto">Not Available for Order</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include('includes/footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
