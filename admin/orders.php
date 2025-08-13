<?php
include('ajax/essentials.php'); // Database connection, functions
include('login_check.php');      // Session/login check

// Connect to database
$conn = mysqli_connect("localhost", "root", "", "kn_raam_hardware");
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Page title
$page_title = "Orders";
include('header.php'); // Includes <head>, navbar, sidebar, starts <main>
?>

<!-- Custom Styles for Table Columns -->
<style>
    .table-responsive {
        max-height: 70vh;
        overflow-y: auto;
    }
    .table th, .table td {
        vertical-align: middle;
    }
    .table th:nth-child(1), .table td:nth-child(1) { /* # */
        width: 50px;
        min-width: 50px;
    }
    .table th:nth-child(2), .table td:nth-child(2) { /* Customer Name */
        width: 150px;
        min-width: 120px;
    }
    .table th:nth-child(3), .table td:nth-child(3) { /* Products (Qty) */
        width: 100px;
        min-width: 80px;
    }
    .table th:nth-child(4), .table td:nth-child(4) { /* Total Price */
        width: 120px;
        min-width: 100px;
    }
    .table th:nth-child(5), .table td:nth-child(5) { /* Address */
        width: 200px;
        min-width: 150px;
    }
    .table th:nth-child(6), .table td:nth-child(6) { /* Order Status */
        width: 120px;
        min-width: 100px;
    }
    .table th:nth-child(7), .table td:nth-child(7) { /* Order Date */
        width: 150px;
        min-width: 130px;
        word-wrap: break-word;
        white-space: normal;
    }
    .table th:nth-child(8), .table td:nth-child(8) { /* Actions */
        width: 100px;
        min-width: 80px;
    }
</style>

<!-- Main Content -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h2 class="card-title m-0">Customer Orders</h2>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Customer Name</th>
                        <th>Products (Qty)</th>
                        <th>Total Price</th>
                        <th>Address</th>
                        <th>Order Status</th>
                        <th>Order Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "
                    SELECT 
                        o.order_id, 
                        u.name AS customer_name,
                        o.total_price, 
                        o.address, 
                        o.order_status, 
                        o.order_date,
                        IFNULL(SUM(oi.qty), 0) AS total_items
                    FROM orders o
                    LEFT JOIN order_items oi ON o.order_id = oi.order_id
                    LEFT JOIN users u ON o.user_id = u.user_id
                    GROUP BY o.order_id
                    ORDER BY o.order_date DESC
                    ";
                    $res = mysqli_query($conn, $sql);
                    if ($res) {
                        $sn = 1;
                        if (mysqli_num_rows($res) > 0) {
                            while ($row = mysqli_fetch_assoc($res)) {
                                echo "<tr>";
                                echo "<td>" . $sn++ . "</td>";
                                echo "<td>" . htmlspecialchars($row['customer_name'] ?? 'N/A') . "</td>";
                                echo "<td>" . htmlspecialchars($row['total_items'] ?? '0') . "</td>";
                                echo "<td>LKR " . number_format($row['total_price'] ?? 0, 2) . "</td>";
                                echo "<td>" . htmlspecialchars($row['address'] ?? 'N/A') . "</td>";

                                // Order status badge
                                $status = $row['order_status'] ?? 'N/A';
                                $badge_class = match ($status) {
                                    'ordered'    => 'bg-danger',
                                    'processing' => 'bg-primary',
                                    'delivered'  => 'bg-success',
                                    'cancelled'  => 'bg-warning',
                                    default      => 'bg-secondary'
                                };
                                echo "<td><span class='badge $badge_class'>" . htmlspecialchars($status) . "</span></td>";

                                echo "<td>" . htmlspecialchars($row['order_date'] ?? 'N/A') . "</td>";
                                echo "<td><a href='edit_order_status.php?order_id=" . urlencode($row['order_id']) . "' class='btn btn-primary btn-sm'>Edit</a></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo '<tr><td colspan="8" class="text-center">No orders found.</td></tr>';
                        }
                    } else {
                        echo '<tr><td colspan="8" class="text-center">Error fetching data: ' . htmlspecialchars(mysqli_error($conn)) . '</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include('footer.php'); // Closes </main>, </body>, includes JS ?>