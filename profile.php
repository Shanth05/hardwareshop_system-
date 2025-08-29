<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}

// Connect to database
$conn = mysqli_connect("localhost", "root", "", "kn_raam_hardware");
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$customer_id = $_SESSION['customer_id'];
$message = '';
$error = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    
    // Check if username already exists (excluding current user)
    $check_username = mysqli_query($conn, "SELECT user_id FROM users WHERE username = '$username' AND user_id != $customer_id");
    if (mysqli_num_rows($check_username) > 0) {
        $error = "Username already exists. Please choose a different username.";
    } else {
        // Check if email already exists (excluding current user)
        $check_email = mysqli_query($conn, "SELECT user_id FROM users WHERE mail = '$email' AND user_id != $customer_id");
        if (mysqli_num_rows($check_email) > 0) {
            $error = "Email already exists. Please use a different email address.";
        } else {
            // Update profile - use correct column names (removed address field)
            $update_query = "UPDATE users SET username = '$username', mail = '$email', contact_no = '$phone' WHERE user_id = $customer_id";
            if (mysqli_query($conn, $update_query)) {
                $message = "Profile updated successfully!";
                $_SESSION['username'] = $username; // Update session
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
    
    // Verify current password
    $check_password_query = "SELECT password FROM users WHERE user_id = $customer_id";
    $password_result = mysqli_query($conn, $check_password_query);
    $password_row = mysqli_fetch_assoc($password_result);
    
    if (!password_verify($current_password, $password_row['password'])) {
        $error = "Current password is incorrect.";
    } elseif ($new_password !== $confirm_password) {
        $error = "New password and confirm password do not match.";
    } elseif (strlen($new_password) < 6) {
        $error = "New password must be at least 6 characters long.";
    } else {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Update password
        $update_password_query = "UPDATE users SET password = '$hashed_password' WHERE user_id = $customer_id";
        if (mysqli_query($conn, $update_password_query)) {
            $message = "Password updated successfully!";
        } else {
            $error = "Error updating password: " . mysqli_error($conn);
        }
    }
}

// Fetch current user data
$user_query = "SELECT * FROM users WHERE user_id = $customer_id";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Fetch user's order history
$orders_query = "SELECT * FROM orders WHERE user_id = $customer_id ORDER BY order_date DESC LIMIT 5";
$orders_result = mysqli_query($conn, $orders_query);

// Check if notifications table exists, if not create it
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'notifications'");
if (mysqli_num_rows($table_check) == 0) {
    $create_table = "
        CREATE TABLE notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            type ENUM('admin', 'customer') NOT NULL,
            action VARCHAR(50) NOT NULL,
            message TEXT NOT NULL,
            reference_id INT,
            user_id INT,
            is_read TINYINT(1) DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_type (type),
            INDEX idx_user_id (user_id),
            INDEX idx_is_read (is_read)
        )
    ";
    mysqli_query($conn, $create_table);
}

// Fetch user's notifications
$notifications_query = "SELECT * FROM notifications WHERE user_id = $customer_id ORDER BY created_at DESC LIMIT 10";
$notifications_result = mysqli_query($conn, $notifications_query);

// Fetch unread notification count
$unread_query = "SELECT COUNT(*) as count FROM notifications WHERE user_id = $customer_id AND is_read = 0";
$unread_result = mysqli_query($conn, $unread_query);
$unread_count = mysqli_fetch_assoc($unread_result)['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile - K.N. Raam Hardware</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>
<body>

<?php include('includes/navbar.php'); ?>

<div class="container mt-5 pt-4">
    <div class="row">
        <div class="col-12">
            <h2 class="text-center mb-4">My Profile</h2>
            
            <?php if ($message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i><?= $message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i><?= $error ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <div class="row">
                <!-- Profile Information -->
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-person-circle me-2"></i>Profile Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="username" name="username" 
                                               value="<?= htmlspecialchars($user['username']) ?>" required>
                                    </div>
                                                                         <div class="col-md-6 mb-3">
                                         <label for="email" class="form-label">Email</label>
                                         <input type="email" class="form-control" id="email" name="email" 
                                                value="<?= htmlspecialchars($user['mail'] ?? '') ?>" required>
                                     </div>
                                </div>
                                
                                                                 <div class="row">
                                     <div class="col-md-6 mb-3">
                                         <label for="phone" class="form-label">Phone Number</label>
                                         <input type="tel" class="form-control" id="phone" name="phone" 
                                                value="<?= htmlspecialchars($user['contact_no'] ?? '') ?>">
                                     </div>
                                 </div>
                                
                                                                 <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                     <button type="submit" class="btn btn-primary">
                                         <i class="bi bi-check-circle me-2"></i>Update Profile
                                     </button>
                                 </div>
                             </form>
                         </div>
                     </div>
                     
                                           <!-- Password Update Section -->
                      <div class="card shadow-sm mt-4">
                          <div class="card-header bg-warning text-dark">
                              <h5 class="mb-0">
                                  <i class="bi bi-shield-lock me-2"></i>Change Password
                              </h5>
                          </div>
                          <div class="card-body">
                              <form method="POST" action="">
                                  <div class="row">
                                      <div class="col-md-12 mb-3">
                                          <label for="current_password" class="form-label">Current Password</label>
                                          <input type="password" class="form-control" id="current_password" name="current_password" required>
                                                                                     <div class="form-text">
                                               <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">
                                                   <i class="bi bi-question-circle me-1"></i>Forgot your current password?
                                               </a>
                                           </div>
                                      </div>
                                  </div>
                                 
                                 <div class="row">
                                     <div class="col-md-6 mb-3">
                                         <label for="new_password" class="form-label">New Password</label>
                                         <input type="password" class="form-control" id="new_password" name="new_password" 
                                                minlength="6" required>
                                         <div class="form-text">Password must be at least 6 characters long.</div>
                                     </div>
                                     <div class="col-md-6 mb-3">
                                         <label for="confirm_password" class="form-label">Confirm New Password</label>
                                         <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                                minlength="6" required>
                                     </div>
                                 </div>
                                 
                                 <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                     <button type="submit" name="update_password" class="btn btn-warning">
                                         <i class="bi bi-shield-check me-2"></i>Update Password
                                     </button>
                                 </div>
                             </form>
                         </div>
                     </div>
                 </div>
                
                <!-- Account Summary -->
                <div class="col-lg-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="bi bi-info-circle me-2"></i>Account Summary
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Member Since:</span>
                                <strong><?= date('M d, Y', strtotime($user['created_at'] ?? 'now')) ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total Orders:</span>
                                <strong><?= mysqli_num_rows($orders_result) ?></strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Account Status:</span>
                                <span class="badge bg-success">Active</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0">
                                <i class="bi bi-lightning me-2"></i>Quick Actions
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                                                 <a href="/hardware/orders/my_orders.php" class="btn btn-outline-primary btn-sm">
                                     <i class="bi bi-box me-2"></i>View All Orders
                                 </a>
                                 <a href="/hardware/cart.php" class="btn btn-outline-success btn-sm">
                                     <i class="bi bi-cart3 me-2"></i>My Cart
                                 </a>
                                 <a href="/hardware/products.php" class="btn btn-outline-info btn-sm">
                                     <i class="bi bi-shop me-2"></i>Browse Products
                                 </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Orders -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">
                                <i class="bi bi-clock-history me-2"></i>Recent Orders
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if (mysqli_num_rows($orders_result) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Order No</th>
                                                <th>Date</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($order = mysqli_fetch_assoc($orders_result)): ?>
                                                <tr>
                                                    <td>#<?= $order['order_id'] ?></td>
                                                    <td><?= date('M d, Y', strtotime($order['order_date'])) ?></td>
                                                    <td>LKR <?= number_format($order['total_price'], 2) ?></td>
                                                                                            <td>
                                            <span class="badge bg-<?= $order['order_status'] == 'Completed' ? 'success' : ($order['order_status'] == 'Pending' ? 'warning' : 'info') ?>">
                                                <?= $order['order_status'] ?>
                                            </span>
                                        </td>
                                                    <td>
                                                                                                                 <a href="/hardware/orders/my_orders.php" class="btn btn-sm btn-outline-primary">
                                                             <i class="bi bi-eye me-1"></i>View
                                                         </a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="text-center mt-3">
                                                                     <a href="/hardware/orders/my_orders.php" class="btn btn-primary">
                                     <i class="bi bi-box me-2"></i>View All Orders
                                 </a>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="bi bi-box" style="font-size: 3rem; color: #ccc;"></i>
                                    <h5 class="mt-3 text-muted">No Orders Yet</h5>
                                    <p class="text-muted">You haven't placed any orders yet.</p>
                                                                         <a href="/hardware/products.php" class="btn btn-primary">
                                         <i class="bi bi-cart-plus me-2"></i>Start Shopping
                                     </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Notifications Section -->
            <div class="row mt-4" id="notifications">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-bell me-2"></i>Notifications
                                <?php if ($unread_count > 0): ?>
                                    <span class="badge bg-warning ms-2"><?= $unread_count ?> new</span>
                                <?php endif; ?>
                            </h5>
                            <button class="btn btn-sm btn-outline-light" onclick="markAllNotificationsRead()">
                                <i class="bi bi-check-all"></i> Mark All Read
                            </button>
                        </div>
                        <div class="card-body">
                            <?php if (mysqli_num_rows($notifications_result) > 0): ?>
                                <div class="list-group list-group-flush">
                                    <?php while ($notification = mysqli_fetch_assoc($notifications_result)): ?>
                                        <div class="list-group-item list-group-item-action <?= $notification['is_read'] == 0 ? 'list-group-item-primary' : '' ?>" 
                                             onclick="markNotificationRead(<?= $notification['id'] ?>)">
                                            <div class="d-flex w-100 justify-content-between">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1"><?= htmlspecialchars($notification['message']) ?></h6>
                                                    <small class="text-muted">
                                                        <?= date('M d, Y g:i A', strtotime($notification['created_at'])) ?>
                                                        <?php if ($notification['is_read'] == 0): ?>
                                                            <span class="badge bg-primary ms-2">New</span>
                                                        <?php endif; ?>
                                                    </small>
                                                </div>
                                                <div class="ms-3">
                                                    <button class="btn btn-sm btn-outline-danger" onclick="event.stopPropagation(); deleteNotification(<?= $notification['id'] ?>)">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="bi bi-bell" style="font-size: 3rem; color: #ccc;"></i>
                                    <h5 class="mt-3 text-muted">No Notifications</h5>
                                    <p class="text-muted">You don't have any notifications yet.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 </div>
 
 <!-- Forgot Password Modal -->
 <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content">
             <div class="modal-header bg-primary text-white">
                 <h5 class="modal-title" id="forgotPasswordModalLabel">
                     <i class="bi bi-lock-fill me-2"></i>Forgot Password
                 </h5>
                 <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body">
                 <div id="forgotPasswordMessage"></div>
                 <form id="forgotPasswordForm">
                     <div class="mb-3">
                         <label for="resetEmail" class="form-label">
                             <i class="bi bi-envelope me-2"></i>Email Address
                         </label>
                         <input type="email" class="form-control" id="resetEmail" name="email" 
                                placeholder="Enter your registered email" required>
                         <div class="form-text">We'll send you a password reset link to your email.</div>
                     </div>
                     <div class="d-grid">
                         <button type="submit" class="btn btn-primary">
                             <i class="bi bi-send me-2"></i>Send Reset Link
                         </button>
                     </div>
                 </form>
             </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
             </div>
         </div>
     </div>
 </div>
 
 <?php include('includes/footer.php'); ?>
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
 <script src="assets/js/script.js"></script>
 
   <script>
  // Password confirmation validation
  document.addEventListener('DOMContentLoaded', function() {
      const newPassword = document.getElementById('new_password');
      const confirmPassword = document.getElementById('confirm_password');
      const updatePasswordBtn = document.querySelector('button[name="update_password"]');
      
      function validatePasswordMatch() {
          if (newPassword.value && confirmPassword.value) {
              if (newPassword.value === confirmPassword.value) {
                  confirmPassword.classList.remove('is-invalid');
                  confirmPassword.classList.add('is-valid');
                  updatePasswordBtn.disabled = false;
              } else {
                  confirmPassword.classList.remove('is-valid');
                  confirmPassword.classList.add('is-invalid');
                  updatePasswordBtn.disabled = true;
              }
          } else {
              confirmPassword.classList.remove('is-valid', 'is-invalid');
              updatePasswordBtn.disabled = false;
          }
      }
      
      newPassword.addEventListener('input', validatePasswordMatch);
      confirmPassword.addEventListener('input', validatePasswordMatch);
      
      // Password strength indicator
      newPassword.addEventListener('input', function() {
          const password = this.value;
      });
      
      // Notification functions
      function markNotificationRead(notificationId) {
          fetch('mark_customer_notification_read.php', {
              method: 'POST',
              headers: {
                  'Content-Type': 'application/x-www-form-urlencoded',
              },
              body: 'notification_id=' + notificationId
          })
          .then(response => response.json())
          .then(data => {
              if (data.success) {
                  location.reload();
              } else {
                  alert('Failed to mark notification as read');
              }
          })
          .catch(error => {
              console.error('Error:', error);
              alert('An error occurred');
          });
      }
      
      function markAllNotificationsRead() {
          fetch('mark_all_customer_notifications_read.php', {
              method: 'POST',
              headers: {
                  'Content-Type': 'application/x-www-form-urlencoded',
              }
          })
          .then(response => response.json())
          .then(data => {
              if (data.success) {
                  location.reload();
              } else {
                  alert('Failed to mark all notifications as read');
              }
          })
          .catch(error => {
              console.error('Error:', error);
              alert('An error occurred');
          });
      }
      
      function deleteNotification(notificationId) {
          if (confirm('Are you sure you want to delete this notification?')) {
              fetch('delete_customer_notification.php', {
                  method: 'POST',
                  headers: {
                      'Content-Type': 'application/x-www-form-urlencoded',
                  },
                  body: 'notification_id=' + notificationId
              })
              .then(response => response.json())
              .then(data => {
                  if (data.success) {
                      location.reload();
                  } else {
                      alert('Failed to delete notification');
                  }
              })
              .catch(error => {
                  console.error('Error:', error);
                  alert('An error occurred');
              });
          }
      }
      
      // Make functions globally available
      window.markNotificationRead = markNotificationRead;
      window.markAllNotificationsRead = markAllNotificationsRead;
      window.deleteNotification = deleteNotification;
          const strength = 0;
          
          if (password.length >= 6) strength++;
          if (password.match(/[a-z]/)) strength++;
          if (password.match(/[A-Z]/)) strength++;
          if (password.match(/[0-9]/)) strength++;
          if (password.match(/[^a-zA-Z0-9]/)) strength++;
          
          const feedback = this.parentNode.querySelector('.form-text');
          if (strength < 2) {
              feedback.textContent = 'Weak password';
              feedback.className = 'form-text text-danger';
          } else if (strength < 4) {
              feedback.textContent = 'Medium strength password';
              feedback.className = 'form-text text-warning';
          } else {
              feedback.textContent = 'Strong password';
              feedback.className = 'form-text text-success';
          }
      });
      
      // Forgot Password Modal Functionality
      const forgotPasswordForm = document.getElementById('forgotPasswordForm');
      const forgotPasswordMessage = document.getElementById('forgotPasswordMessage');
      
      forgotPasswordForm.addEventListener('submit', function(e) {
          e.preventDefault();
          
          const email = document.getElementById('resetEmail').value;
          const submitBtn = this.querySelector('button[type="submit"]');
          const originalText = submitBtn.innerHTML;
          
          // Show loading state
          submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Sending...';
          submitBtn.disabled = true;
          
          // Send AJAX request
          fetch('/hardware/forgot_password.php', {
              method: 'POST',
              headers: {
                  'Content-Type': 'application/x-www-form-urlencoded',
              },
              body: 'reset_password=1&email=' + encodeURIComponent(email)
          })
          .then(response => response.text())
          .then(data => {
              // Reset button
              submitBtn.innerHTML = originalText;
              submitBtn.disabled = false;
              
              // Check if response contains success indicators
              if (data.includes('Password reset link has been generated') || data.includes('Reset Link:')) {
                  forgotPasswordMessage.innerHTML = `
                      <div class="alert alert-success">
                          <i class="bi bi-check-circle-fill me-2"></i>
                          Password reset link has been generated!<br><br>
                          <strong>Reset Link:</strong><br>
                          <div class="mt-2">
                              ${data.match(/<a href='([^']+)'[^>]*>([^<]+)<\/a>/)?.[0] || 'Link generated successfully'}
                          </div>
                          <small class="text-muted mt-2 d-block">This link will expire in 1 hour.</small>
                      </div>
                  `;
                  document.getElementById('resetEmail').value = '';
              } else if (data.includes('Email address not found')) {
                  forgotPasswordMessage.innerHTML = `
                      <div class="alert alert-danger">
                          <i class="bi bi-exclamation-triangle-fill me-2"></i>
                          Email address not found in our system.
                      </div>
                  `;
              } else {
                  forgotPasswordMessage.innerHTML = `
                      <div class="alert alert-danger">
                          <i class="bi bi-exclamation-triangle-fill me-2"></i>
                          Error generating reset link. Please try again.
                      </div>
                  `;
              }
          })
          .catch(error => {
              submitBtn.innerHTML = originalText;
              submitBtn.disabled = false;
              forgotPasswordMessage.innerHTML = `
                  <div class="alert alert-danger">
                      <i class="bi bi-exclamation-triangle-fill me-2"></i>
                      Network error. Please try again.
                  </div>
              `;
          });
      });
      
      // Clear message when modal is closed
      const forgotPasswordModal = document.getElementById('forgotPasswordModal');
      forgotPasswordModal.addEventListener('hidden.bs.modal', function() {
          forgotPasswordMessage.innerHTML = '';
          document.getElementById('resetEmail').value = '';
      });
  });
  </script>
 
 </body>
 </html>
 
 <?php mysqli_close($conn); ?>
