<?php
session_start();
include('ajax/essentials.php');
include('login_check.php');

// DB Connection
$conn = mysqli_connect("localhost", "root", "", "kn_raam_hardware");
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Validate & get order ID
if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    die("Invalid order ID.");
}
$order_id = intval($_GET['order_id']);

// Fetch order details with total_items calculated dynamically
$sql = "
SELECT o.order_id, o.name, o.mail, o.contact_no, o.address, o.payment_method, o.total_price, o.order_status, o.order_date,
       IFNULL(SUM(oi.qty), 0) AS total_items
FROM orders o
LEFT JOIN order_items oi ON o.order_id = oi.order_id
WHERE o.order_id = ?
GROUP BY o.order_id
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Order not found.");
}

$order = $result->fetch_assoc();

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = $_POST['order_status'] ?? '';
    $allowed_statuses = ['ordered', 'processing', 'delivered']; // Removed 'cancelled'

    if (!in_array($new_status, $allowed_statuses)) {
        $error = "Invalid status selected: " . htmlspecialchars($new_status);
    } else {
        $update_sql = "UPDATE orders SET order_status = ? WHERE order_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("si", $new_status, $order_id);
        if ($update_stmt->execute()) {
            $_SESSION['success'] = "Order status updated successfully!";
            header("Location: orders.php");
            exit;
        } else {
            $error = "Failed to update order status: " . htmlspecialchars($conn->error);
        }
        $update_stmt->close();
    }
}

// Fetch order items
$items_sql = "
    SELECT oi.order_item_id, oi.product_id, p.product_name, oi.qty, oi.price 
    FROM order_items oi
    JOIN products p ON oi.product_id = p.product_id
    WHERE oi.order_id = ?";
$items_stmt = $conn->prepare($items_sql);
$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();
$order_items = $items_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Order Status | K.N. Raam Hardware</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
    <?php
    if (file_exists('inc/links.php')) {
        include('inc/links.php');
    }
    ?>
    <style>
        #sidebarMenu { min-height: 100vh; }
        @media (max-width: 767.98px) {
            #sidebarMenu {
                position: fixed;
                z-index: 1030;
                top: 56px;
                height: calc(100% - 56px);
                background-color: #fff;
            }
            .main-content { margin-top: 56px; }
        }
        /* Mobile-friendly card table */
        @media (max-width: 768px) {
            table thead { display: none; }
            table tbody tr {
                display: block;
                margin-bottom: 1rem;
                background: #fff;
                padding: 10px;
                border-radius: 8px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            }
            table tbody td {
                display: flex;
                justify-content: space-between;
                padding: 5px 0;
            }
            table tbody td::before {
                content: attr(data-label);
                font-weight: bold;
            }
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
                    <li class="nav-item"><a class="nav-link" href="customers.php"><i class="bi bi-people me-2"></i>Registered Customers</a></li>
                    <li class="nav-item"><a class="nav-link active" href="orders.php"><i class="bi bi-cart-check me-2"></i>Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="admins.php"><i class="bi bi-person-badge me-2"></i>Registered Admins</a></li>
                    <li class="nav-item"><a class="nav-link" href="products.php"><i class="bi bi-box-seam me-2"></i>Products</a></li>
                    <li class="nav-item"><a class="nav-link" href="categories.php"><i class="bi bi-tags me-2"></i>Categories</a></li>
                    <li class="nav-item"><a class="nav-link" href="brands.php"><i class="bi bi-building me-2"></i>Brands</a></li>
                    <li class="nav-item mt-3 border-top pt-2"><a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4 main-content">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-dark text-white">
                    <h4>Edit Order Status - Order #<?php echo htmlspecialchars($order['order_id']); ?></h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if (!empty($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
                            <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Customer Name</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($order['name']); ?>" disabled>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($order['mail']); ?>" disabled>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contact No</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($order['contact_no']); ?>" disabled>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" rows="1" disabled><?php echo htmlspecialchars($order['address']); ?></textarea>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Payment Method</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($order['payment_method']); ?>" disabled>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Total Items</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($order['total_items']); ?>" disabled>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Total Price (LKR)</label>
                                <input type="text" class="form-control" value="<?php echo number_format($order['total_price'], 2); ?>" disabled>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Order Date</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($order['order_date']); ?>" disabled>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Order Status</label>
                                <select name="order_status" class="form-select" required>
                                    <?php
                                    $allowed_statuses = ['ordered', 'processing', 'delivered']; // Removed 'cancelled'
                                    foreach ($allowed_statuses as $status) {
                                        $selected = ($status === $order['order_status']) ? 'selected' : '';
                                        echo "<option value=\"$status\" $selected>" . ucfirst($status) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Status</button>
                        <!-- Removed Back to Orders button -->
                    </form>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5>Order Items</h5>
                </div>
                <div class="card-body">
                    <?php if ($order_items->num_rows > 0): ?>
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Order Item ID</th>
                                    <th>Product ID</th>
                                    <th>Product Name</th>
                                    <th>Qty</th>
                                    <th>Price (LKR)</th>
                                    <th>Total (LKR)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $grand_total = 0;
                                while ($item = $order_items->fetch_assoc()): 
                                    $total = $item['qty'] * $item['price'];
                                    $grand_total += $total;
                                ?>
                                    <tr>
                                        <td data-label="Order Item ID"><?php echo $item['order_item_id']; ?></td>
                                        <td data-label="Product ID"><?php echo $item['product_id']; ?></td>
                                        <td data-label="Product Name"><?php echo htmlspecialchars($item['product_name']); ?></td>
                                        <td data-label="Qty"><?php echo $item['qty']; ?></td>
                                        <td data-label="Price (LKR)"><?php echo number_format($item['price'], 2); ?></td>
                                        <td data-label="Total (LKR)"><?php echo number_format($total, 2); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                                <tr class="fw-bold">
                                    <td colspan="5" class="text-end">Grand Total:</td>
                                    <td><?php echo number_format($grand_total, 2); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-muted">No items found for this order.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php
if (file_exists('inc/scripts.php')) {
    include('inc/scripts.php');
}
?>

<script>
    document.getElementById('sidebarToggle').addEventListener('click', function () {
        const sidebar = document.getElementById('sidebarMenu');
        sidebar.classList.toggle('show');
    });

    // Auto-hide success alert after 5 seconds (not needed here as redirect happens)
</script>
</body>
</html>
