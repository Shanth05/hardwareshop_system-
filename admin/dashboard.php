<?php
include('login_check.php');

$conn = mysqli_connect("localhost", "root", "", "kn_raam_hardware"); // your DB

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

function getCount($conn, $table, $condition = '') {
    $sql = "SELECT COUNT(*) as count FROM $table";
    if ($condition != '') {
        $sql .= " WHERE $condition";
    }
    $res = mysqli_query($conn, $sql);
    if ($res) {
        $row = mysqli_fetch_assoc($res);
        return $row['count'];
    }
    return 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <?php include('inc/links.php'); ?>
    <title>Hardware Admin Panel</title>
</head>
<body class="bg-white">
<?php include('inc/header.php'); ?>

<div class="container-fluid" id="main-content">
    <div class="row">
        <div class="col-lg-10 ms-auto p-4 overflow-hidden">
            <h4 class="mt-2 text-dark">DASHBOARD</h4>
            <div class="container p-4">
                <div class="row">

                    <!-- Registered Customers -->
                    <div class="col-3 p-2 m-3 border rounded border-primary">
                        <div class="grid">
                            <h1><?= getCount($conn, 'users', "user_type='customer'"); ?></h1>
                            <span class="fw-bold text-primary">Registered Customers</span>
                        </div>
                    </div>

                    <!-- Orders -->
                    <div class="col-3 p-2 m-3 border rounded border-primary">
                        <div class="grid">
                            <h1><?= getCount($conn, 'orders'); ?></h1>
                            <span class="fw-bold text-primary">Orders</span>
                        </div>
                    </div>

                    <!-- Registered Admins -->
                    <div class="col-3 p-2 m-3 border rounded border-primary">
                        <div class="grid">
                            <h1><?= getCount($conn, 'users', "user_type='admin'"); ?></h1>
                            <span class="fw-bold text-primary">Registered Admins</span>
                        </div>
                    </div>

                    <!-- Products -->
                    <div class="col-3 p-2 m-3 border rounded border-primary">
                        <div class="grid">
                            <h1><?= getCount($conn, 'products'); ?></h1>
                            <span class="fw-bold text-primary">Products</span>
                        </div>
                    </div>

                    <!-- Available Brands -->
                    <div class="col-3 p-2 m-3 border rounded border-primary">
                        <div class="grid">
                            <h1><?= getCount($conn, 'brands'); ?></h1>
                            <span class="fw-bold text-primary">Available Brands</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include('inc/scripts.php'); ?>
</body>
</html>
