<?php
session_start();

// Database connection
$conn = mysqli_connect('localhost', 'root', '', 'kn_raam_hardware');
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$message = '';
$error = '';
$valid_token = false;
$user_id = null;

// Check if token is provided and valid
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // Check if token exists and is not expired
    $stmt = $conn->prepare("SELECT user_id FROM password_resets WHERE token = ? AND expires_at > NOW() AND used = 0");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $reset_data = $result->fetch_assoc();
        $user_id = $reset_data['user_id'];
        $valid_token = true;
    } else {
        $error = "Invalid or expired reset link. Please request a new password reset.";
    }
    $stmt->close();
} else {
    $error = "No reset token provided.";
}

// Handle password reset form submission
if (isset($_POST['update_password']) && $valid_token) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate password
    if (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Hash new password
        $hashed_password = password_hash($password, PASSWORD_ARGON2ID);
        
        // Update user password
        $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $update_stmt->bind_param("si", $hashed_password, $user_id);
        
        if ($update_stmt->execute()) {
            // Mark reset token as used
            $mark_used_stmt = $conn->prepare("UPDATE password_resets SET used = 1 WHERE token = ?");
            $mark_used_stmt->bind_param("s", $token);
            $mark_used_stmt->execute();
            $mark_used_stmt->close();
            
            $message = "Password updated successfully! You can now login with your new password.";
        } else {
            $error = "Error updating password. Please try again.";
        }
        $update_stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password | K.N. Raam Hardware</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #3b8d99, #6b6b83, #aa4b6b);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            animation: fadeIn 1s ease-in-out;
            border: none;
            border-radius: 15px;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
        }
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
        .btn-primary {
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .password-strength {
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <i class="bi bi-key-fill text-primary" style="font-size: 3rem;"></i>
                            <h3 class="text-primary fw-bold mt-2">Reset Password</h3>
                            <p class="text-muted">Enter your new password</p>
                        </div>

                        <?php if ($message): ?>
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                <?= $message ?>
                            </div>
                            <div class="text-center">
                                <a href="login.php" class="btn btn-primary">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Go to Login
                                </a>
                            </div>
                        <?php elseif ($error): ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <?= htmlspecialchars($error) ?>
                            </div>
                            <div class="text-center">
                                <a href="forgot_password.php" class="btn btn-primary">
                                    <i class="bi bi-arrow-left me-2"></i>Request New Reset
                                </a>
                            </div>
                        <?php elseif ($valid_token): ?>
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label for="password" class="form-label">
                                        <i class="bi bi-lock me-2"></i>New Password
                                    </label>
                                    <input type="password" name="password" id="password" class="form-control" 
                                           placeholder="Enter new password" required minlength="8">
                                    <div class="password-strength text-muted">
                                        Password must be at least 8 characters long
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="confirm_password" class="form-label">
                                        <i class="bi bi-lock-fill me-2"></i>Confirm Password
                                    </label>
                                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" 
                                           placeholder="Confirm new password" required minlength="8">
                                </div>
                                
                                <div class="d-grid mb-3">
                                    <button type="submit" name="update_password" class="btn btn-primary">
                                        <i class="bi bi-check-circle me-2"></i>Update Password
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>

                        <div class="text-center">
                            <a href="login.php" class="text-decoration-none">
                                <i class="bi bi-arrow-left me-1"></i>Back to Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

