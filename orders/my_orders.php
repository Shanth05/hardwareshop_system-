<?php
session_start();

if (!isset($_SESSION['customer_id'])) {
    header("Location: ../login.php");
    exit;
}

$customer_id = $_SESSION['customer_id'];

// Connect to database
$conn = mysqli_connect("localhost", "root", "", "kn_raam_hardware");
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch orders for the customer
$orders_query = "SELECT * FROM orders WHERE user_id = $customer_id ORDER BY order_date DESC";
$orders_result = mysqli_query($conn, $orders_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders - K.N. Raam Hardware</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>
<body>

<?php include('../includes/navbar.php'); ?>

<div class="container mt-5 pt-4">
    <div class="row">
        <div class="col-12">
            <h2 class="text-center mb-4">My Orders</h2>
            
            <?php if (mysqli_num_rows($orders_result) > 0): ?>
                <div class="row">
                    <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                        <div class="col-12 mb-4">
                            <div class="card shadow-sm">
                                <div class="card-header bg-primary text-white">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <h5 class="mb-0">
                                                <i class="bi bi-box me-2"></i>
                                                Order #<?= $order['order_id'] ?>
                                            </h5>
                                        </div>
                                        <div class="col-md-6 text-md-end">
                                            <span class="badge bg-light text-dark">
                                                <?= date('M d, Y', strtotime($order['order_date'])) ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Order Date:</strong> <?= date('F d, Y', strtotime($order['order_date'])) ?></p>
                                            <p><strong>Total Amount:</strong> LKR <?= number_format($order['total_price'], 2) ?></p>
                                            <p><strong>Status:</strong> 
                                                <span class="badge bg-<?= $order['order_status'] == 'Completed' ? 'success' : ($order['order_status'] == 'Pending' ? 'warning' : 'info') ?>">
                                                    <?= $order['order_status'] ?>
                                                </span>
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Shipping Address:</strong></p>
                                            <p class="text-muted"><?= htmlspecialchars($order['address']) ?></p>
                                        </div>
                                    </div>
                                    
                                    <!-- Order Items -->
                                    <?php
                                    $order_id = $order['order_id'];
                                                                         $items_query = "SELECT oi.*, p.product_name, p.price 
                                                    FROM order_items oi 
                                                    JOIN products p ON oi.product_id = p.product_id 
                                                    WHERE oi.order_id = $order_id";
                                    $items_result = mysqli_query($conn, $items_query);
                                    ?>
                                    
                                    <div class="mt-3">
                                        <h6>Order Items:</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Product</th>
                                                        <th>Quantity</th>
                                                        <th>Price</th>
                                                        <th>Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($item['product_name']) ?></td>
                                                            <td><?= $item['qty'] ?></td>
                                                            <td>LKR <?= number_format($item['price'], 2) ?></td>
                                                            <td>LKR <?= number_format($item['price'] * $item['qty'], 2) ?></td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-box" style="font-size: 4rem; color: #ccc;"></i>
                    <h4 class="mt-3 text-muted">No Orders Found</h4>
                    <p class="text-muted">You haven't placed any orders yet.</p>
                                         <a href="/hardware/products.php" class="btn btn-primary">
                         <i class="bi bi-cart-plus me-2"></i>Start Shopping
                     </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/script.js"></script>
</body>
</html>

<?php mysqli_close($conn); ?>
