<?php
include('ajax/essentials.php');
include('login_check.php');

$conn = mysqli_connect("localhost", "root", "", "kn_raam_hardware");
if (!$conn) die("Database connection failed: " . mysqli_connect_error());

// Include email notification system
include('../includes/email_notifications.php');

// Handle POST request and redirect BEFORE including header.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
        $_SESSION['error'] = "Invalid order ID.";
        header("Location: orders.php");
        exit;
    }
    
    $order_id = intval($_GET['order_id']);
    $new_status = $_POST['order_status'] ?? '';
    $allowed_statuses = ['pending','processing','delivered','cancelled'];
    
    if (in_array($new_status, $allowed_statuses)) {
        // Get current order status before update
        $current_status_query = "SELECT order_status FROM orders WHERE order_id = ?";
        $current_stmt = $conn->prepare($current_status_query);
        $current_stmt->bind_param("i", $order_id);
        $current_stmt->execute();
        $current_result = $current_stmt->get_result();
        $current_order = $current_result->fetch_assoc();
        $current_status = $current_order['order_status'] ?? '';
        
        $update_stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
        $update_stmt->bind_param("si", $new_status, $order_id);
        if ($update_stmt->execute()) {
            // Send notification only if status actually changed
            if ($current_status !== $new_status) {
                $email_notifications = new EmailNotifications($conn);
                $email_notifications->sendOrderStatusUpdate($order_id, $new_status);
            }
            
            $_SESSION['success'] = "Order status updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update: " . htmlspecialchars($conn->error);
        }
        $update_stmt->close();
    } else {
        $_SESSION['error'] = "Invalid status selected.";
    }
    
    header("Location: orders.php");
    exit;
}

$page_title = "Edit Order Status";
include('header.php');

if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) die("Invalid order ID.");
$order_id = intval($_GET['order_id']);

$sql = "
SELECT o.order_id, o.name, o.mail, o.contact_no, o.address, o.payment_method, o.total_price, o.order_status, o.order_date,
       IFNULL(SUM(oi.qty),0) AS total_items
FROM orders o
LEFT JOIN order_items oi ON o.order_id = oi.order_id
WHERE o.order_id = ?
GROUP BY o.order_id
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) die("Order not found.");
$order = $result->fetch_assoc();

$error = '';

$items_stmt = $conn->prepare("
SELECT oi.order_item_id, oi.product_id, p.product_name, oi.qty, oi.price
FROM order_items oi
JOIN products p ON oi.product_id = p.product_id
WHERE oi.order_id = ?
");
$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();
$order_items = $items_stmt->get_result();
?>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-dark text-white">
        <h4>Edit Order Status - Order #<?php echo $order['order_id']; ?></h4>
    </div>
    <div class="card-body">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3"><label>Customer Name</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($order['name']); ?>" disabled>
                </div>
                <div class="col-md-6 mb-3"><label>Email</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($order['mail']); ?>" disabled>
                </div>
                <div class="col-md-6 mb-3"><label>Contact No</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($order['contact_no']); ?>" disabled>
                </div>
                <div class="col-md-6 mb-3"><label>Address</label>
                    <textarea class="form-control" rows="1" disabled><?php echo htmlspecialchars($order['address']); ?></textarea>
                </div>
                <div class="col-md-6 mb-3"><label>Payment Method</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($order['payment_method']); ?>" disabled>
                </div>
                <div class="col-md-3 mb-3"><label>Total Items</label>
                    <input type="text" class="form-control" value="<?php echo is_numeric($order['total_items']) ? $order['total_items'] : 0; ?>" disabled>
                </div>
                <div class="col-md-3 mb-3"><label>Total Price (LKR)</label>
                    <input type="text" class="form-control" value="<?php echo number_format((float)($order['total_price'] ?? 0),2); ?>" disabled>
                </div>
                <div class="col-md-3 mb-3"><label>Order Date</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($order['order_date']); ?>" disabled>
                </div>
                <div class="col-md-3 mb-3">
                    <label>Order Status</label>
                    <select name="order_status" class="form-select" required>
                        <?php foreach(['pending','processing','delivered','cancelled'] as $status): 
                            $selected = ($status === $order['order_status']) ? 'selected' : '';
                        ?>
                        <option value="<?php echo $status; ?>" <?php echo $selected; ?>><?php echo ucfirst($status); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button class="btn btn-primary">Update Status</button>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white"><h5>Order Items</h5></div>
    <div class="card-body">
        <?php if($order_items->num_rows > 0): ?>
        <table class="table table-bordered table-striped">
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
                while($item = $order_items->fetch_assoc()):
                    $qty = is_numeric($item['qty']) ? $item['qty'] : 0;
                    $price = is_numeric($item['price']) ? $item['price'] : 0;
                    $total = $qty * $price;
                    $grand_total += $total;
                ?>
                <tr>
                    <td><?php echo $item['order_item_id']; ?></td>
                    <td><?php echo $item['product_id']; ?></td>
                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                    <td><?php echo $qty; ?></td>
                    <td><?php echo number_format((float)$price,2); ?></td>
                    <td><?php echo number_format((float)$total,2); ?></td>
                </tr>
                <?php endwhile; ?>
                <tr class="fw-bold">
                    <td colspan="5" class="text-end">Grand Total:</td>
                    <td><?php echo number_format((float)$grand_total,2); ?></td>
                </tr>
            </tbody>
        </table>
        <?php else: ?>
            <p class="text-muted">No items found for this order.</p>
        <?php endif; ?>
    </div>
</div>

<?php include('footer.php'); ?>
