<?php
session_start();

if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['customer_id'];

// Connect to hardware shop database
$conn = mysqli_connect("localhost", "root", "", "kn_raam_hardware");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch cart items for current user
$sql = "SELECT * FROM cart WHERE user_id = $user_id";
$res = mysqli_query($conn, $sql);

$totalPrice = 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>K.N. Raam Hardware - Cart</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="includes/css/style.css?v=<?php echo time(); ?>" />
</head>

<body>

    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-5">
        <h2 class="mb-4 text-center">Your Cart</h2>

        <?php if ($res && mysqli_num_rows($res) > 0): ?>
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Price (LKR)</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sn = 1;
                    while ($row = mysqli_fetch_assoc($res)) {
                        $cart_id = $row['cart_id'];
                        $product_name = $row['product_name'];  // Adjust if your column is named differently
                        $qty = $row['qty'];
                        $price = $row['price'];
                        $totalPrice += $price;
                        ?>
                        <tr>
                            <td><?php echo $sn++; ?></td>
                            <td><?php echo htmlspecialchars($product_name); ?></td>
                            <td>
                                <input type="number" class="form-control" value="<?php echo $qty; ?>" min="1" readonly />
                                <!-- You can add update qty functionality later -->
                            </td>
                            <td><?php echo number_format($price, 2); ?></td>
                            <td>
                                <a href="remove_cart.php?cart_id=<?php echo $cart_id; ?>" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to remove this item?');">Remove</a>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                    <tr>
                        <td colspan="3" class="text-end fw-bold">Total Price</td>
                        <td colspan="2" class="fw-bold"><?php echo number_format($totalPrice, 2); ?> LKR</td>
                    </tr>
                </tbody>
            </table>
            <div class="text-end">
                <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
            </div>
        <?php else: ?>
            <p class="text-center">Your cart is empty.</p>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
