<?php
include('ajax/essentials.php'); // Database connection, functions
include('login_check.php');    // Session/login check

// Connect to database
$conn = mysqli_connect("localhost", "root", "", "kn_raam_hardware");
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
    <?php
    if (file_exists('inc/links.php')) {
        include('inc/links.php');
    }
    ?>
    <title>Orders | K.N. Raam Hardware</title>

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
        .table-responsive {
            max-height: 70vh;
            overflow-y: auto;
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
                                    SELECT o.order_id, o.name, o.total_price, o.address, o.order_status, o.order_date,
                                           IFNULL(SUM(oi.qty), 0) AS total_items
                                    FROM orders o
                                    LEFT JOIN order_items oi ON o.order_id = oi.order_id
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
                                                echo "<td>" . htmlspecialchars($row['name'] ?? 'N/A') . "</td>";
                                                echo "<td>" . htmlspecialchars($row['total_items'] ?? '0') . "</td>";
                                                echo "<td>LKR " . number_format($row['total_price'] ?? 0, 2) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['address'] ?? 'N/A') . "</td>";
                                                echo "<td>";
                                                $status = $row['order_status'] ?? 'N/A';
                                                $badge_class = match ($status) {
                                                    'ordered' => 'bg-danger',
                                                    'processing' => 'bg-primary',
                                                    'delivered' => 'bg-success',
                                                    default => 'bg-secondary'
                                                };
                                                echo "<span class='badge $badge_class'>" . htmlspecialchars($status) . "</span>";
                                                echo "</td>";
                                                echo "<td>" . htmlspecialchars($row['order_date'] ?? 'N/A') . "</td>";
                                                echo "<td>";
                                                echo "<a href='edit_order_status.php?order_id=" . urlencode($row['order_id'] ?? '') . "' class='btn btn-primary btn-sm'>Edit</a>";
                                                echo "</td>";
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
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
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
    </script>
</body>
</html>
