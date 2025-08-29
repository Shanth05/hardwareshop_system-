<?php
$page_title = "Completed Orders";
include('header.php');

// Fetch completed orders
$sql = "
    SELECT o.*, u.name as customer_name, u.mail as customer_email 
    FROM orders o 
    JOIN users u ON o.user_id = u.user_id 
    WHERE o.order_status = 'Completed' 
    ORDER BY o.order_date DESC
";
$res = mysqli_query($conn, $sql);
?>

<h2 class="mb-4">Completed Orders</h2>

<div class="card shadow">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-bordered mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Total Amount</th>
                        <th>Order Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($res && mysqli_num_rows($res) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($res)): ?>
                            <tr>
                                <td>#<?= $row['order_id']; ?></td>
                                <td><?= htmlspecialchars($row['customer_name']); ?></td>
                                <td><?= htmlspecialchars($row['customer_email']); ?></td>
                                <td>LKR <?= number_format($row['total_price'], 2); ?></td>
                                <td><?= date('M j, Y', strtotime($row['order_date'])); ?></td>
                                <td>
                                    <span class="badge bg-success"><?= $row['order_status']; ?></span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#orderModal<?= $row['order_id']; ?>">
                                        <i class="bi bi-eye"></i> View Details
                                    </button>
                                </td>
                            </tr>
                            
                            <!-- Order Details Modal -->
                            <div class="modal fade" id="orderModal<?= $row['order_id']; ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Completed Order #<?= $row['order_id']; ?> Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <strong>Customer:</strong> <?= htmlspecialchars($row['customer_name']); ?><br>
                                                    <strong>Email:</strong> <?= htmlspecialchars($row['customer_email']); ?><br>
                                                    <strong>Order Date:</strong> <?= date('M j, Y g:i A', strtotime($row['order_date'])); ?>
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Total Amount:</strong> LKR <?= number_format($row['total_price'], 2); ?><br>
                                                    <strong>Status:</strong> <span class="badge bg-success"><?= $row['order_status']; ?></span>
                                                </div>
                                            </div>
                                            
                                            <h6>Order Items:</h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Product</th>
                                                            <th>Price</th>
                                                            <th>Quantity</th>
                                                            <th>Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $items_sql = "
                                                            SELECT oi.*, p.product_name, p.price 
                                                            FROM order_items oi 
                                                            JOIN products p ON oi.product_id = p.product_id 
                                                            WHERE oi.order_id = {$row['order_id']}
                                                        ";
                                                        $items_res = mysqli_query($conn, $items_sql);
                                                        while ($item = mysqli_fetch_assoc($items_res)):
                                                        ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($item['product_name']); ?></td>
                                                            <td>LKR <?= number_format($item['price'], 2); ?></td>
                                                            <td><?= $item['qty']; ?></td>
                                                            <td>LKR <?= number_format($item['price'] * $item['qty'], 2); ?></td>
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
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No completed orders found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>
