<?php
session_start();

if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['customer_id'];

// Connect to DB
$conn = new mysqli("localhost", "root", "", "kn_raam_hardware");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle profile update form
if (isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $mail = $_POST['mail'];
    $contact_no = $_POST['contact_no'];
    $username = $_POST['username'];

    $stmt = $conn->prepare("UPDATE users SET name=?, mail=?, contact_no=?, username=? WHERE user_id=?");
    $stmt->bind_param("ssssi", $name, $mail, $contact_no, $username, $user_id);

    if ($stmt->execute()) {
        $_SESSION['username'] = $username; // Update session username if changed
        echo '<script>alert("Profile updated successfully."); window.location.href="profile.php";</script>';
        exit;
    } else {
        echo '<script>alert("Failed to update profile.");</script>';
    }
    $stmt->close();
}

// Handle password update form
if (isset($_POST['update_password'])) {
    $current_password = md5($_POST['current_password']);
    $new_password = md5($_POST['new_password']);
    $confirm_password = md5($_POST['confirm_password']);

    // Verify current password
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id=? AND password=?");
    $stmt->bind_param("is", $user_id, $current_password);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        if ($new_password === $confirm_password) {
            $stmt_update = $conn->prepare("UPDATE users SET password=? WHERE user_id=?");
            $stmt_update->bind_param("si", $new_password, $user_id);
            if ($stmt_update->execute()) {
                echo '<script>alert("Password updated successfully."); window.location.href="profile.php";</script>';
                exit;
            } else {
                echo '<script>alert("Failed to update password.");</script>';
            }
            $stmt_update->close();
        } else {
            echo '<script>alert("New password and confirmation do not match.");</script>';
        }
    } else {
        echo '<script>alert("Current password is incorrect.");</script>';
    }
    $stmt->close();
}

// Fetch user info
$stmt = $conn->prepare("SELECT name, mail, contact_no, username FROM users WHERE user_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $mail, $contact_no, $username);
$stmt->fetch();
$stmt->close();

include('includes/header.php');
?>

<div class="container my-5">

    <h2 class="text-center mb-4">My Profile</h2>

    <div class="row">

        <!-- Profile Info + Update -->
        <div class="col-lg-6 mb-5">
            <div class="card shadow-sm p-4">
                <h4 class="mb-3">Profile Details</h4>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($name) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="mail" class="form-label">Email</label>
                        <input type="email" name="mail" id="mail" class="form-control" value="<?= htmlspecialchars($mail) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="contact_no" class="form-label">Contact Number</label>
                        <input type="text" name="contact_no" id="contact_no" class="form-control" value="<?= htmlspecialchars($contact_no) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" id="username" class="form-control" value="<?= htmlspecialchars($username) ?>" required>
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>

        <!-- Change Password -->
        <div class="col-lg-6 mb-5">
            <div class="card shadow-sm p-4">
                <h4 class="mb-3">Change Password</h4>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" name="current_password" id="current_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" name="new_password" id="new_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" name="update_password" class="btn btn-warning">Update Password</button>
                </form>
            </div>
        </div>

    </div>

    <hr>

    <h2 class="text-center mb-4">My Orders</h2>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-primary">
                <tr>
                    <th>#</th>
                    <th>Order No</th>
                    <th>Total Items</th>
                    <th>Total Price</th>
                    <th>Shipping Address</th>
                    <th>Order Status</th>
                    <th>Order Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $conn->prepare("SELECT order_id, total_items, total_price, address, order_status, order_date FROM orders WHERE user_id = ? ORDER BY order_id DESC");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $res = $stmt->get_result();

                if ($res->num_rows > 0):
                    $sn = 1;
                    while ($row = $res->fetch_assoc()):
                        $order_status = strtolower($row['order_status'] ?? '');

                        if ($order_status === '') {
                            $displayStatus = 'Pending';
                        } elseif ($order_status === 'cancelled') {
                            $displayStatus = 'Cancelled';
                        } else {
                            $displayStatus = ucfirst($order_status);
                        }

                        $statusClass = match ($order_status) {
                            "ordered" => "text-success",     // green
                            "processing" => "text-primary",  // blue
                            "cancelled" => "text-danger fw-bold", // red bold
                            default => "",
                        };
                ?>
                <tr>
                    <td><?= $sn++ ?></td>
                    <td><?= htmlspecialchars($row['order_id'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['total_items'] ?? '0') ?></td>
                    <td><?= number_format($row['total_price'] ?? 0, 2) ?></td>
                    <td><?= htmlspecialchars($row['address'] ?? '') ?></td>
                    <td class="<?= $statusClass ?>"><?= $displayStatus ?></td>
                    <td><?= htmlspecialchars(date('Y-m-d', strtotime($row['order_date'] ?? ''))) ?></td>
                    <td>
                        <?php if ($order_status === 'ordered' || $order_status === 'processing'): ?>
                            <a href="cancel_order.php?order_id=<?= urlencode($row['order_id'] ?? '') ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to cancel this order?');">Cancel</a>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php
                    endwhile;
                else:
                    echo '<tr><td colspan="8" class="text-center">No orders found.</td></tr>';
                endif;

                $stmt->close();
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

</div>

<?php include('includes/footer.php'); ?>
