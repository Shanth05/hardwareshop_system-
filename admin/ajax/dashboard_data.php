<?php
include('inc/header.php'); // Common header + navbar/sidebar
include('ajax/essentials.php'); // DB connection
include('login_check.php');    // Session/login check
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4 main-content">
    <h2 class="mb-4">Dashboard Overview</h2>

    <!-- Dashboard Cards -->
    <div class="row g-4">
        <div class="col-md-3">
            <div class="card card-gradient-primary card-hover p-3 text-center shadow" onclick="location.href='customers.php'">
                <h5>Registered Customers</h5>
                <?php
                $res = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM users");
                $cnt = mysqli_fetch_assoc($res)['cnt'] ?? 0;
                ?>
                <h2><?= $cnt ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-gradient-success card-hover p-3 text-center shadow" onclick="location.href='orders.php'">
                <h5>Orders</h5>
                <?php
                $res = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM orders");
                $cnt = mysqli_fetch_assoc($res)['cnt'] ?? 0;
                ?>
                <h2><?= $cnt ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-gradient-warning card-hover p-3 text-center shadow" onclick="location.href='admins.php'">
                <h5>Registered Admins</h5>
                <?php
                $res = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM users WHERE user_type='admin'");
                $cnt = mysqli_fetch_assoc($res)['cnt'] ?? 0;
                ?>
                <h2><?= $cnt ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-gradient-info card-hover p-3 text-center shadow" onclick="location.href='products.php'">
                <h5>Products</h5>
                <?php
                $res = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM products");
                $cnt = mysqli_fetch_assoc($res)['cnt'] ?? 0;
                ?>
                <h2><?= $cnt ?></h2>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-md-3">
            <div class="card card-gradient-secondary card-hover p-3 text-center shadow" onclick="location.href='categories.php'">
                <h5>Categories</h5>
                <?php
                $res = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM categories");
                $cnt = mysqli_fetch_assoc($res)['cnt'] ?? 0;
                ?>
                <h2><?= $cnt ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-gradient-dark card-hover p-3 text-center shadow" onclick="location.href='brands.php'">
                <h5>Brands</h5>
                <?php
                $res = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM brands");
                $cnt = mysqli_fetch_assoc($res)['cnt'] ?? 0;
                ?>
                <h2><?= $cnt ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-gradient-danger card-hover p-3 text-center shadow" onclick="location.href='pending_orders.php'">
                <h5>Pending Orders</h5>
                <?php
                $res = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM orders WHERE order_status='ordered'");
                $cnt = mysqli_fetch_assoc($res)['cnt'] ?? 0;
                ?>
                <h2><?= $cnt ?></h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-gradient-success card-hover p-3 text-center shadow" onclick="location.href='completed_orders.php'">
                <h5>Completed Orders</h5>
                <?php
                $res = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM orders WHERE order_status='delivered'");
                $cnt = mysqli_fetch_assoc($res)['cnt'] ?? 0;
                ?>
                <h2><?= $cnt ?></h2>
            </div>
        </div>
    </div>

    <!-- Latest Orders & Sales Chart -->
    <div class="row mt-5">
        <!-- Latest Orders -->
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="mb-0">Latest Orders</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="latestOrdersBody">
                                <tr><td colspan="8" class="text-center">Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
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
</main>

<?php include('inc/footer.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const salesChartCtx = document.getElementById('salesChart').getContext('2d');
let salesChart = new Chart(salesChartCtx, {
    type: 'line',
    data: {
        labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
        datasets: [{
            label: 'Sales (LKR)',
            data: Array(12).fill(0),
            borderColor: '#0d6efd',
            backgroundColor: 'rgba(13,110,253,0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: { responsive: true, plugins: { legend: { display: true } }, scales: { y: { beginAtZero: true } } }
});

function updateDashboard() {
    fetch('ajax/dashboard_data.php')
    .then(res => res.json())
    .then(data => {
        // Latest Orders
        const tbody = document.getElementById('latestOrdersBody');
        tbody.innerHTML = '';
        if (!data.orders || data.orders.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center">No orders found.</td></tr>';
        } else {
            data.orders.forEach((row,index)=>{
                let badgeClass = '';
                switch(row.order_status){
                    case 'ordered': badgeClass='bg-danger'; break;
                    case 'processing': badgeClass='bg-primary'; break;
                    case 'delivered': badgeClass='bg-success'; break;
                    default: badgeClass='bg-secondary';
                }
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${index+1}</td>
                    <td>${row.customer_name||'N/A'}</td>
                    <td>${row.total_items||0}</td>
                    <td>LKR ${parseFloat(row.total_price).toFixed(2)}</td>
                    <td>${row.address||'N/A'}</td>
                    <td><span class="badge ${badgeClass}">${row.order_status}</span></td>
                    <td>${row.order_date}</td>
                    <td><a href="edit_order_status.php?order_id=${row.order_id}" class="btn btn-sm btn-primary">Edit</a></td>
                `;
                tbody.appendChild(tr);
            });
        }

        // Sales Chart
        if (data.sales) {
            salesChart.data.datasets[0].data = data.sales;
            salesChart.update();
        }
    })
    .catch(err => console.error('Dashboard fetch error:',err));
}

// Initial load
updateDashboard();
setInterval(updateDashboard,30000); // Refresh every 30s
</script>
