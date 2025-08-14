<?php
$page_title = "Dashboard";
include('header.php'); // includes login_check.php, DB connection, navbar + sidebar

// ===== FETCH LIVE COUNTS =====
$total_customers  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM users WHERE user_type='customer'"))['count'];
$total_admins     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM users WHERE user_type='admin'"))['count'];
$total_orders     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM orders"))['count'];
$total_products   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM products"))['count'];
$total_categories = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM categories"))['count'];
$total_brands     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM brands"))['count'];
$total_pending    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM orders WHERE order_status='Pending'"))['count'];
$total_completed  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM orders WHERE order_status='Completed'"))['count'];
$total_messages   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM contact_messages WHERE status='Pending'"))['count'];

// ===== FETCH LATEST ORDERS =====
$latest_orders_query = "
    SELECT o.order_id, u.name, o.total_price, o.order_status
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    ORDER BY o.order_id DESC
    LIMIT 5
";
$latest_orders = mysqli_query($conn, $latest_orders_query);
?>

<h2 class="mb-4">Dashboard Overview</h2>

<!-- Dashboard Cards -->
<div class="row g-4">
  <div class="col-md-3">
    <div class="card card-gradient-primary card-hover p-3 text-center shadow" onclick="location.href='customers.php'">
      <h5>Registered Customers</h5>
      <h2><?= $total_customers; ?></h2>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card card-gradient-success card-hover p-3 text-center shadow" onclick="location.href='orders.php'">
      <h5>Orders</h5>
      <h2><?= $total_orders; ?></h2>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card card-gradient-warning card-hover p-3 text-center shadow" onclick="location.href='admins.php'">
      <h5>Registered Admins</h5>
      <h2><?= $total_admins; ?></h2>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card card-gradient-info card-hover p-3 text-center shadow" onclick="location.href='products.php'">
      <h5>Products</h5>
      <h2><?= $total_products; ?></h2>
    </div>
  </div>
</div>

<div class="row g-4 mt-1">
  <div class="col-md-3">
    <div class="card card-gradient-secondary card-hover p-3 text-center shadow" onclick="location.href='categories.php'">
      <h5>Categories</h5>
      <h2><?= $total_categories; ?></h2>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card card-gradient-dark card-hover p-3 text-center shadow" onclick="location.href='brands.php'">
      <h5>Brands</h5>
      <h2><?= $total_brands; ?></h2>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card card-gradient-danger card-hover p-3 text-center shadow" onclick="location.href='pending_orders.php'">
      <h5>Pending Orders</h5>
      <h2><?= $total_pending; ?></h2>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card card-gradient-success card-hover p-3 text-center shadow" onclick="location.href='completed_orders.php'">
      <h5>Completed Orders</h5>
      <h2><?= $total_completed; ?></h2>
    </div>
  </div>
</div>

<div class="row g-4 mt-1">
  <div class="col-md-3">
    <div class="card card-gradient-primary card-hover p-3 text-center shadow" onclick="location.href='messages.php'">
      <h5>Pending Messages</h5>
      <h2><?= $total_messages; ?></h2>
    </div>
  </div>
</div>

<!-- Latest Orders Table -->
<div class="row mt-5">
  <div class="col-md-6">
    <div class="card shadow">
      <div class="card-header">
        <h5 class="mb-0">Latest Orders</h5>
      </div>
      <div class="card-body p-0">
        <table class="table mb-0">
          <thead>
            <tr>
              <th>#</th>
              <th>Customer</th>
              <th>Total</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if (mysqli_num_rows($latest_orders) > 0): ?>
              <?php while ($order = mysqli_fetch_assoc($latest_orders)): ?>
                <?php
                  $status = ucfirst(strtolower($order['order_status']));
                  $badge_class = match ($status) {
                      'Pending'    => 'bg-warning',
                      'Processing' => 'bg-primary',
                      'Completed'  => 'bg-success',
                      'Cancelled'  => 'bg-danger',
                      default      => 'bg-secondary'
                  };
                ?>
                <tr>
                  <td><?= $order['order_id']; ?></td>
                  <td><?= htmlspecialchars($order['name']); ?></td>
                  <td>LKR <?= number_format($order['total_price'], 2); ?></td>
                  <td><span class="badge <?= $badge_class; ?>"><?= $status; ?></span></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="4" class="text-center">No orders found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Sales Chart -->
  <div class="col-md-6">
    <div class="card shadow">
      <div class="card-header">
        <h5 class="mb-0">Sales Overview</h5>
      </div>
      <div class="card-body">
        <canvas id="salesChart" height="200"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('salesChart').getContext('2d');
new Chart(ctx, {
  type: 'line',
  data: {
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
    datasets: [{
      label: 'Sales (LKR)',
      data: [5000, 7000, 8000, 6000, 9000, 12000], // Replace with dynamic DB data later
      borderColor: '#0d6efd',
      backgroundColor: 'rgba(13, 110, 253, 0.1)',
      tension: 0.4,
      fill: true
    }]
  },
  options: { responsive: true, plugins: { legend: { display: true } } }
});
</script>

<?php include('footer.php'); ?>
