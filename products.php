<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "kn_raam_hardware");
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Handle Add to Cart form submission
if (isset($_POST['add_to_cart'])) {
    // Check if user logged in
    if (!isset($_SESSION['customer_id'])) {
        // Redirect to login with alert message
        echo '<script>alert("Please login to add products to cart."); window.location.href="login.php";</script>';
        exit();
    }

    $user_id = $_SESSION['customer_id'];
    $product_id = intval($_POST['product_id']);
    $qty = intval($_POST['qty']);
    if ($qty < 1) $qty = 1;

    // Check if product already exists in the cart
    $check = mysqli_query($conn, "SELECT qty FROM cart WHERE user_id=$user_id AND product_id=$product_id");
    if (mysqli_num_rows($check) > 0) {
        // If yes, update the quantity
        $row = mysqli_fetch_assoc($check);
        $new_qty = $row['qty'] + $qty;
        $update = mysqli_query($conn, "UPDATE cart SET qty = $new_qty WHERE user_id = $user_id AND product_id = $product_id");
        if ($update) {
            echo '<script>alert("Cart updated successfully."); window.location.href="products.php";</script>';
            exit();
        } else {
            echo '<script>alert("Failed to update cart."); window.location.href="products.php";</script>';
            exit();
        }
    } else {
        // Else insert new product into cart
        $insert = mysqli_query($conn, "INSERT INTO cart (user_id, product_id, qty) VALUES ($user_id, $product_id, $qty)");
        if ($insert) {
            echo '<script>alert("Product added to cart."); window.location.href="products.php";</script>';
            exit();
        } else {
            echo '<script>alert("Failed to add product to cart."); window.location.href="products.php";</script>';
            exit();
        }
    }
}

// Fetch available products from database
$products = mysqli_query($conn, "SELECT * FROM products WHERE status='Available'");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>K.N. Raam Hardware - Products</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- External CSS -->
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>" />
</head>

<body>
    <?php include('includes/navbar.php'); ?>

    <div class="container mt-5 pt-4">
        <!-- Page Title -->
        <h2 class="text-center fw-bold h-font about-title">OUR PRODUCTS</h2>
        <div class="h-line about-divider"></div>

        <!-- Products Grid -->
        <div class="row g-4">
            <?php while ($row = mysqli_fetch_assoc($products)) :
                $product_id = $row['product_id'];
                $product_name = $row['product_name'];
                $price = $row['price'];
                $stock = $row['stock'];
                $image = $row['image'];
                $category = $row['category'];
            ?>
                <div class="col-md-3 col-sm-6">
                    <!-- Single Product Card -->
                    <div class="card product-card h-100">
                        <img src="images/products/<?php echo htmlspecialchars($image); ?>" 
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
                            <p class="product-category mb-3">Category: <?php echo htmlspecialchars($category); ?></p>

                            <?php if ($stock > 0) : ?>
                                <!-- Add to Cart Form -->
                                <form method="POST" action="" class="mt-auto">
                                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                    <input type="number" name="qty" value="1" min="1" max="<?php echo $stock; ?>" 
                                           class="form-control mb-2" required>
                                    <button type="submit" name="add_to_cart" 
                                            class="btn btn-outline-primary w-100">Add to Cart</button>
                                </form>
                            <?php else : ?>
                                <p class="text-danger mt-auto">Out of Stock</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <?php include('includes/footer.php'); ?>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
