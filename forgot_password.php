<?php
session_start();

// Database connection
$conn = mysqli_connect('localhost', 'root', '', 'kn_raam_hardware');
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$message = '';
$error = '';

// Handle form submission
if (isset($_POST['reset_password'])) {
    $email = trim($_POST['email']);
    
    // Check if email exists
    $stmt = $conn->prepare("SELECT user_id, username, mail FROM users WHERE mail = ? AND user_type = 'customer'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Generate reset token
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Store reset token in database
        $insert_stmt = $conn->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
        $insert_stmt->bind_param("iss", $user['user_id'], $token, $expires);
        
        if ($insert_stmt->execute()) {
            // Send reset email (simulated for now)
            $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=" . $token;
            
            // For demo purposes, we'll show the link instead of sending email
            $message = "Password reset link has been generated!<br><br>";
            $message .= "<strong>Reset Link:</strong><br>";
            $message .= "<a href='$reset_link' class='text-break'>$reset_link</a><br><br>";
            $message .= "<small class='text-muted'>This link will expire in 1 hour.</small>";
        } else {
            $error = "Error generating reset link. Please try again.";
        }
        $insert_stmt->close();
    } else {
        $error = "Email address not found in our system.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password | K.N. Raam Hardware</title>
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
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <i class="bi bi-lock-fill text-primary" style="font-size: 3rem;"></i>
                            <h3 class="text-primary fw-bold mt-2">Forgot Password</h3>
                            <p class="text-muted">Enter your email address to reset your password</p>
                        </div>

                        <?php if ($message): ?>
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                <?= $message ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="bi bi-envelope me-2"></i>Email Address
                                </label>
                                <input type="email" name="email" id="email" class="form-control" 
                                       placeholder="Enter your registered email" required>
                            </div>
                            
                            <div class="d-grid mb-3">
                                <button type="submit" name="reset_password" class="btn btn-primary">
                                    <i class="bi bi-send me-2"></i>Send Reset Link
                                </button>
                            </div>
                        </form>

                        <div class="text-center">
                            <a href="/hardware/login.php" class="text-decoration-none">
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
