<?php
$page_title = "Manage Admins";
include('header.php');

$message = '';
$error = '';

// Handle admin creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_admin'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $contact_no = mysqli_real_escape_string($conn, $_POST['contact_no']);
    
    // Check if username or email already exists
    $check_sql = "SELECT user_id FROM users WHERE username = '$username' OR mail = '$email'";
    $check_result = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($check_result) > 0) {
        $error = "Username or email already exists.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $insert_sql = "INSERT INTO users (name, username, mail, password, contact_no, user_type) VALUES ('$name', '$username', '$email', '$hashed_password', '$contact_no', 'admin')";
        
        if (mysqli_query($conn, $insert_sql)) {
            $message = "Admin account created successfully!";
        } else {
            $error = "Error creating admin account: " . mysqli_error($conn);
        }
    }
}

// Handle admin deletion
if (isset($_GET['delete']) && $_GET['delete'] > 0) {
    $delete_id = intval($_GET['delete']);
    
    // Prevent deleting self
    if ($delete_id == $_SESSION['admin_id']) {
        $error = "You cannot delete your own account.";
    } else {
        $delete_sql = "DELETE FROM users WHERE user_id = $delete_id AND user_type = 'admin'";
        if (mysqli_query($conn, $delete_sql)) {
            $message = "Admin account deleted successfully!";
        } else {
            $error = "Error deleting admin account: " . mysqli_error($conn);
        }
    }
}

// Fetch all admins
$sql = "SELECT user_id, name, username, mail, contact_no, user_type FROM users WHERE user_type = 'admin' ORDER BY name";
$result = mysqli_query($conn, $sql);
?>

<h2 class="mb-4">Manage Admins</h2>

<?php if ($message): ?>
    <div class="alert alert-success" id="successAlert"><?= $message; ?></div>
    <script>setTimeout(() => { document.getElementById('successAlert').style.display = 'none'; }, 3000);</script>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger" id="errorAlert"><?= $error; ?></div>
    <script>setTimeout(() => { document.getElementById('errorAlert').style.display = 'none'; }, 3000);</script>
<?php endif; ?>

<div class="row">
    <!-- Add New Admin -->
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-person-plus me-2"></i>Add New Admin</h5>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="contact_no" class="form-label">Contact Number</label>
                        <input type="text" class="form-control" id="contact_no" name="contact_no">
                    </div>
                    
                    <button type="submit" name="add_admin" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Add Admin
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Admin List -->
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-people me-2"></i>Admin Accounts</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Contact</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                                <?php $sn = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?= $sn++; ?></td>
                                        <td><?= htmlspecialchars($row['name'] ?? ''); ?></td>
                                        <td><?= htmlspecialchars($row['username'] ?? ''); ?></td>
                                        <td><?= htmlspecialchars($row['mail'] ?? ''); ?></td>
                                        <td><?= htmlspecialchars($row['contact_no'] ?? 'N/A'); ?></td>
                                        <td>
                                            <?php if ($row['user_id'] == $_SESSION['admin_id']): ?>
                                                <span class="badge bg-primary">Current User</span>
                                            <?php else: ?>
                                                <a href="?delete=<?= $row['user_id']; ?>" class="btn btn-danger btn-sm" 
                                                   onclick="return confirm('Are you sure you want to delete this admin account?')">
                                                    <i class="bi bi-trash"></i> Delete
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No admin accounts found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>
