<?php
session_start();

if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['order_id'])) {
    die("Invalid access.");
}

$order_id = intval($_GET['order_id']);
$user_id = $_SESSION['customer_id'];

$conn = mysqli_connect("localhost", "root", "", "kn_raam_hardware");
if (!$conn) {
    error_log("Database connection failed: " . mysqli_connect_error());
    die("An error occurred. Please try again later.");
}

// Fetch order details
$order_sql = "SELECT * FROM orders WHERE order_id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $order_sql);
mysqli_stmt_bind_param($stmt, "ii", $order_id, $user_id);
mysqli_stmt_execute($stmt);
$order_res = mysqli_stmt_get_result($stmt);

if (!$order_res || mysqli_num_rows($order_res) == 0) {
    error_log("Order not found for order_id: $order_id, user_id: $user_id");
    die("Order not found.");
}

$order = mysqli_fetch_assoc($order_res);

// Fetch order items
$item_sql = "
    SELECT oi.qty, oi.price, p.product_name 
    FROM order_items oi
    INNER JOIN products p ON oi.product_id = p.product_id
    WHERE oi.order_id = ?
";
$stmt_items = mysqli_prepare($conn, $item_sql);
mysqli_stmt_bind_param($stmt_items, "i", $order_id);
mysqli_stmt_execute($stmt_items);
$items_res = mysqli_stmt_get_result($stmt_items);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success - K.N. Raam Hardware</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
</head>
<body>
<?php include('includes/navbar.php'); ?>

<div class="content checkout-container">
    <h2 class="section-title"><i class="fas fa-check-circle me-2 text-success"></i>Thank You for Your Order!</h2>
    <div class="about-divider"></div>

    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="card product-card">
                <div class="card-header bg-brandblue text-white">
                    <i class="fas fa-file-invoice me-2"></i>Order Details
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6 mb-3">
                            <p><strong><i class="fas fa-hashtag me-1"></i>Order ID:</strong> #<?= htmlspecialchars((string)$order['order_id']); ?></p>
                            <p><strong><i class="fas fa-user me-1"></i>Name:</strong> <?= htmlspecialchars($order['name']); ?></p>
                            <p><strong><i class="fas fa-envelope me-1"></i>Email:</strong> <?= htmlspecialchars($order['mail']); ?></p>
                            <p><strong><i class="fas fa-phone me-1"></i>Contact No:</strong> <?= htmlspecialchars($order['contact_no']); ?></p>
                        </div>
                        <!-- Right Column -->
                        <div class="col-md-6 mb-3">
                            <p><strong><i class="fas fa-map-marker-alt me-1"></i>Address:</strong> <?= htmlspecialchars($order['address']); ?></p>
                            <p><strong><i class="fas fa-credit-card me-1"></i>Payment Method:</strong> <?= htmlspecialchars($order['payment_method'] === 'online' ? 'Pay Online (Card)' : 'Cash on Delivery'); ?></p>
                            <p><strong><i class="fas fa-shopping-cart me-1"></i>Total Items:</strong> <?= htmlspecialchars((string)$order['total_items']); ?></p>
                            <p><strong><i class="fas fa-money-bill-wave me-1"></i>Total Price:</strong> LKR <?= number_format((float)$order['total_price'], 2); ?></p>
                            <p><strong><i class="fas fa-info-circle me-1"></i>Order Status:</strong> <?= htmlspecialchars(ucfirst($order['order_status'])); ?></p>
                            <p><strong><i class="fas fa-calendar-alt me-1"></i>Order Date:</strong> <?= htmlspecialchars($order['order_date']); ?></p>
                        </div>
                    </div>

                    <h5 class="mt-4"><i class="fas fa-list-ul me-2"></i>Order Items</h5>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr class="bg-brandblue text-white">
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Unit Price (LKR)</th>
                                <th>Total (LKR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($item = mysqli_fetch_assoc($items_res)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['product_name']); ?></td>
                                    <td><?= htmlspecialchars((string)$item['qty']); ?></td>
                                    <td class="product-price"><?= number_format((float)$item['price'], 2); ?></td>
                                    <td class="product-price"><?= number_format((float)($item['qty'] * $item['price']), 2); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                    <div class="text-end">
                        <a href="index.php" class="btn btn-checkout"><i class="fas fa-shopping-bag me-2"></i>Continue Shopping</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>