<?php
$page_title = "Admin Profile";
include('header.php');

// Get current admin info
$admin_id = $_SESSION['admin_id'];
$sql = "SELECT * FROM users WHERE user_id = $admin_id AND user_type = 'admin'";
$result = mysqli_query($conn, $sql);
$admin = mysqli_fetch_assoc($result);

$message = '';
$error = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $contact_no = mysqli_real_escape_string($conn, $_POST['contact_no']);
    
    // Check if username already exists (excluding current admin)
    $check_username = mysqli_query($conn, "SELECT user_id FROM users WHERE username = '$username' AND user_id != $admin_id");
    if (mysqli_num_rows($check_username) > 0) {
        $error = "Username already exists. Please choose a different username.";
    } else {
        // Check if email already exists (excluding current admin)
        $check_email = mysqli_query($conn, "SELECT user_id FROM users WHERE mail = '$email' AND user_id != $admin_id");
        if (mysqli_num_rows($check_email) > 0) {
            $error = "Email already exists. Please use a different email address.";
        } else {
            // Update profile
            $update_query = "UPDATE users SET name = '$name', username = '$username', mail = '$email', contact_no = '$contact_no' WHERE user_id = $admin_id";
            if (mysqli_query($conn, $update_query)) {
                $message = "Profile updated successfully!";
                // Refresh admin data
                $result = mysqli_query($conn, $sql);
                $admin = mysqli_fetch_assoc($result);
            } else {
                $error = "Error updating profile: " . mysqli_error($conn);
            }
        }
    }
}

// Handle password update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($new_password !== $confirm_password) {
        $error = "New passwords do not match.";
    } elseif (!password_verify($current_password, $admin['password'])) {
        $error = "Current password is incorrect.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_query = "UPDATE users SET password = '$hashed_password' WHERE user_id = $admin_id";
        if (mysqli_query($conn, $update_query)) {
            $message = "Password updated successfully!";
        } else {
            $error = "Error updating password: " . mysqli_error($conn);
        }
    }
}
?>

<h2 class="mb-4">Admin Profile</h2>

<?php if ($message): ?>
    <div class="alert alert-success" id="successAlert"><?= $message; ?></div>
    <script>setTimeout(() => { document.getElementById('successAlert').style.display = 'none'; }, 3000);</script>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger" id="errorAlert"><?= $error; ?></div>
    <script>setTimeout(() => { document.getElementById('errorAlert').style.display = 'none'; }, 3000);</script>
<?php endif; ?>

<div class="row">
    <!-- Profile Information -->
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>Profile Information</h5>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($admin['name'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($admin['username'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($admin['mail'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="contact_no" class="form-label">Contact Number</label>
                        <input type="text" class="form-control" id="contact_no" name="contact_no" value="<?= htmlspecialchars($admin['contact_no'] ?? ''); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">User Type</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($admin['user_type'] ?? ''); ?>" readonly>
                    </div>
                    
                    <button type="submit" name="update_profile" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Update Profile
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Change Password -->
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-lock me-2"></i>Change Password</h5>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <button type="submit" name="update_password" class="btn btn-warning">
                        <i class="bi bi-key me-1"></i>Change Password
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Account Statistics -->
        <div class="card shadow mt-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Account Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="text-primary"><?= date('M Y'); ?></h4>
                        <small class="text-muted">Current Month</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success"><?= date('Y'); ?></h4>
                        <small class="text-muted">Current Year</small>
                    </div>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col-6">
                        <h5 class="text-info"><?= date('d'); ?></h5>
                        <small class="text-muted">Day</small>
                    </div>
                    <div class="col-6">
                        <h5 class="text-warning"><?= date('H:i'); ?></h5>
                        <small class="text-muted">Time</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>
