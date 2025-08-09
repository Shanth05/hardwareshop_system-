<?php
session_start();

if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['customer_id'];

// Connect to hardware shop database
$conn = new mysqli("localhost", "root", "", "kn_raam_hardware");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user info
$stmt = $conn->prepare("SELECT name, mail, contact_no, username FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $mail, $contact_no, $username);
$stmt->fetch();
$stmt->close();

include('includes/header.php');
?>

<div class="my-5 px-4">
    <h2 class="text-center fw-bold h-font">MY PROFILE</h2>
    <div class="h-line bg-dark"></div>
</div>

<div class="container">
    <div class="row">

        <!-- User Profile Info -->
        <div class="col-lg-12 mb-5 p-4 shadow">
            <center><h3>My Profile</h3></center>
            <center>
                <div class="bg-primary p-3 text-white rounded shadow">
                    <h6>Name: <?php echo htmlspecialchars($name); ?></h6>
                    <p>Email: <?php echo htmlspecialchars($mail); ?></p>
                    <p>Contact no: <?php echo htmlspecialchars($contact_no); ?></p>
                    <p>Username: <?php echo htmlspecialchars($username); ?></p>
                </div>
            </center>
        </div>

        <!-- Order History -->
        <div class="col-lg-12 mb-5 p-4 shadow bg-light">
            <center><h3>Order History</h3></center>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Customer name</th>
                        <th scope="col">Products</th>
                        <th scope="col">Total Price</th>
                        <th scope="col">Address</th>
                        <th scope="col">Order Status</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch orders for this user by name (better if you use user_id in tbl_order, adjust if possible)
                    $stmt = $conn->prepare("SELECT order_id, name, mail, contact_no, addL1, addL2, addL3, total_product, price_total, order_status FROM tbl_order WHERE name = ?");
                    $stmt->bind_param("s", $name);
                    $stmt->execute();
                    $res1 = $stmt->get_result();

                    if ($res1->num_rows > 0) {
                        $sn = 1;
                        while ($row1 = $res1->fetch_assoc()) {
                            $order_id = $row1['order_id'];
                            $cus_name = $row1['name'];
                            $mail = $row1['mail'];
                            $contact_no = $row1['contact_no'];
                            $addL1 = $row1['addL1'];
                            $addL2 = $row1['addL2'];
                            $addL3 = $row1['addL3'];
                            $total_product = $row1['total_product'];
                            $price_total = $row1['price_total'];
                            $order_status = $row1['order_status'];

                            $statusClass = match ($order_status) {
                                "ordered" => "text-danger",
                                "processing" => "text-primary",
                                default => "text-success",
                            };
                            ?>
                            <tr>
                                <th scope="row"><?php echo $sn++; ?></th>
                                <td><?php echo htmlspecialchars($cus_name); ?></td>
                                <td><?php echo htmlspecialchars($total_product); ?></td>
                                <td><?php echo htmlspecialchars(number_format($price_total, 2)); ?></td>
                                <td><?php echo htmlspecialchars("$addL1, $addL2, $addL3"); ?></td>
                                <td class="<?php echo $statusClass; ?>"><?php echo htmlspecialchars($order_status); ?></td>
                                <td>
                                    <a href="cancel_order.php?order_id=<?php echo $order_id; ?>&order_status=<?php echo urlencode($order_status); ?>&mail=<?php echo urlencode($mail); ?>&name=<?php echo urlencode($name); ?>" 
                                       class="btn btn-primary shadow-none btn-sm"
                                       onclick="return confirm('Are you sure you want to cancel this order?');">Cancel Order</a>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo '<tr><td colspan="7" class="text-center">No orders found.</td></tr>';
                    }
                    $stmt->close();
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Update Password Form -->
        <div class="col-lg-12 mb-5 p-4 shadow bg-light">
            <center><h3>Update Password</h3></center>
            <div class="container p-4">
                <form action="" method="POST">
                    <div class="row p-4">
                        <div class="col-md-6 mb-3">
                            <label for="current_password">Old Password *</label>
                            <input type="password" id="current_password" name="current_password" required class="form-control" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="new_password">New Password *</label>
                            <input type="password" id="new_password" name="new_password" required class="form-control" />
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="confirm_password">Again type Password *</label>
                            <input type="password" id="confirm_password" name="confirm_password" required class="form-control" />
                        </div>

                        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>" />

                        <div class="col-md-12 mb-3">
                            <input type="reset" name="reset" value="Clear" class="btn btn-danger float-end ms-1" />
                            <input type="submit" name="update_password" value="Update Password" class="btn btn-primary float-end" />
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<?php include('includes/footer.php'); ?>

<?php
if (isset($_POST['update_password'])) {
    $user_id = $_POST['user_id'];
    $current_password = md5($_POST['current_password']);
    $new_password = md5($_POST['new_password']);
    $confirm_password = md5($_POST['confirm_password']);

    // Verify current password
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = ? AND password = ?");
    $stmt->bind_param("is", $user_id, $current_password);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        if ($new_password === $confirm_password) {
            $stmt_update = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
            $stmt_update->bind_param("si", $new_password, $user_id);
            if ($stmt_update->execute()) {
                echo '<script>
                    alert("Password successfully updated!");
                    window.location.href = "profile.php";
                </script>';
            } else {
                echo '<script>
                    alert("Failed to update password. Please try again.");
                    window.location.href = "profile.php";
                </script>';
            }
            $stmt_update->close();
        } else {
            echo '<script>
                alert("New password and confirmation do not match.");
                window.location.href = "profile.php";
            </script>';
        }
    } else {
        echo '<script>
            alert("Current password is incorrect.");
            window.location.href = "profile.php";
        </script>';
    }
    $stmt->close();
}

$conn->close();
?>
