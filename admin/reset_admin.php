<?php
// Admin Password Reset Script
// WARNING: This script should be removed after use for security

// Database connection info
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kn_raam_hardware";

// Connect to MySQL
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_username = $_POST['username'] ?? 'admin';
    $admin_password = $_POST['password'] ?? 'admin123';
    $admin_email = $_POST['email'] ?? 'admin@knraamhardware.com';
    
    // Hash the password using Argon2id (more secure)
    $hashed_password = password_hash($admin_password, PASSWORD_ARGON2ID);
    
    // First, check if admin user exists
    $check_stmt = $conn->prepare("SELECT user_id FROM users WHERE user_type = 'admin' LIMIT 1");
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update existing admin
        $update_stmt = $conn->prepare("UPDATE users SET username = ?, password = ?, mail = ? WHERE user_type = 'admin'");
        $update_stmt->bind_param("sss", $admin_username, $hashed_password, $admin_email);
        
        if ($update_stmt->execute()) {
            $message = "✅ Admin credentials updated successfully!<br>";
            $message .= "Username: <strong>$admin_username</strong><br>";
            $message .= "Password: <strong>$admin_password</strong><br>";
            $message .= "Email: <strong>$admin_email</strong>";
        } else {
            $error = "❌ Error updating admin: " . $update_stmt->error;
        }
        $update_stmt->close();
    } else {
        // Create new admin user
        $insert_stmt = $conn->prepare("INSERT INTO users (username, password, mail, user_type) VALUES (?, ?, ?, 'admin')");
        $insert_stmt->bind_param("sss", $admin_username, $hashed_password, $admin_email);
        
        if ($insert_stmt->execute()) {
            $message = "✅ New admin account created successfully!<br>";
            $message .= "Username: <strong>$admin_username</strong><br>";
            $message .= "Password: <strong>$admin_password</strong><br>";
            $message .= "Email: <strong>$admin_email</strong>";
        } else {
            $error = "❌ Error creating admin: " . $insert_stmt->error;
        }
        $insert_stmt->close();
    }
    $check_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Password Reset - K.N. Raam Hardware</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .reset-container { max-width: 500px; margin: 50px auto; }
        .alert { border-radius: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="reset-container">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0">🔐 Admin Password Reset</h4>
                </div>
                <div class="card-body p-4">
                    
                    <?php if (isset($message)): ?>
                        <div class="alert alert-success">
                            <?= $message ?>
                        </div>
                        <div class="text-center">
                            <a href="../admin/login.php" class="btn btn-primary">Go to Admin Login</a>
                        </div>
                    <?php elseif (isset($error)): ?>
                        <div class="alert alert-danger">
                            <?= $error ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-4">
                            Use this form to reset your admin credentials. This will update the existing admin account or create a new one.
                        </p>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Admin Username</label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="admin" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Admin Password</label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       value="admin123" required>
                                <div class="form-text">Choose a strong password</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Admin Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="admin@knraamhardware.com" required>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Reset Admin Credentials</button>
                            </div>
                        </form>
                    <?php endif; ?>
                    
                    <div class="mt-4 text-center">
                        <small class="text-muted">
                            ⚠️ <strong>Security Notice:</strong> Delete this file after use!
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

