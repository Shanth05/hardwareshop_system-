<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}

// Database connection
$conn = mysqli_connect("localhost", "root", "", "kn_raam_hardware");
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

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

$customer_id = $_SESSION['customer_id'];

// Handle bulk actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['mark_all_read'])) {
        $update_sql = "UPDATE notifications SET is_read = 1 WHERE user_id = ? AND type = 'customer'";
        $stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($stmt, "i", $customer_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $_SESSION['success'] = "All notifications marked as read!";
        header("Location: notifications.php");
        exit;
    }
    
    if (isset($_POST['delete_selected']) && isset($_POST['notification_ids'])) {
        $notification_ids = array_map('intval', $_POST['notification_ids']);
        $ids_string = implode(',', $notification_ids);
        $delete_sql = "DELETE FROM notifications WHERE id IN ($ids_string) AND user_id = ? AND type = 'customer'";
        $stmt = mysqli_prepare($conn, $delete_sql);
        mysqli_stmt_bind_param($stmt, "i", $customer_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $_SESSION['success'] = "Selected notifications deleted!";
        header("Location: notifications.php");
        exit;
    }
}

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Get total count
$total_query = "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND type = 'customer'";
$stmt = mysqli_prepare($conn, $total_query);
mysqli_stmt_bind_param($stmt, "i", $customer_id);
mysqli_stmt_execute($stmt);
$total_result = mysqli_stmt_get_result($stmt);
$total_notifications = mysqli_fetch_assoc($total_result)['count'];
$total_pages = ceil($total_notifications / $per_page);

// Get notifications
$notifications_query = "
    SELECT * FROM notifications 
    WHERE user_id = ? AND type = 'customer' 
    ORDER BY created_at DESC 
    LIMIT ? OFFSET ?
";
$stmt = mysqli_prepare($conn, $notifications_query);
mysqli_stmt_bind_param($stmt, "iii", $customer_id, $per_page, $offset);
mysqli_stmt_execute($stmt);
$notifications_result = mysqli_stmt_get_result($stmt);

// Get unread count
$unread_query = "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND type = 'customer' AND is_read = 0";
$stmt = mysqli_prepare($conn, $unread_query);
mysqli_stmt_bind_param($stmt, "i", $customer_id);
mysqli_stmt_execute($stmt);
$unread_result = mysqli_stmt_get_result($stmt);
$unread_count = mysqli_fetch_assoc($unread_result)['count'];

// Helper function to format time elapsed
function time_elapsed_string($datetime) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    if ($diff->y > 0) {
        return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
    }
    if ($diff->m > 0) {
        return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
    }
    if ($diff->d > 0) {
        return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
    }
    if ($diff->h > 0) {
        return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    }
    if ($diff->i > 0) {
        return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
    }
    return 'Just now';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Notifications | K.N. Raam Hardware</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .notification-item {
            transition: background-color 0.2s;
        }
        .notification-item:hover {
            background-color: #f8f9fa;
        }
        .notification-item.unread {
            background-color: #e3f2fd;
        }
        .notification-time {
            font-size: 0.8rem;
            color: #666;
        }
        
        /* Eye icon styling for order notifications */
        .btn-outline-info {
            border-color: #17a2b8;
            color: #17a2b8;
        }
        .btn-outline-info:hover {
            background-color: #17a2b8;
            border-color: #17a2b8;
            color: white;
        }
        .btn-outline-info:focus {
            box-shadow: 0 0 0 0.2rem rgba(23, 162, 184, 0.25);
        }
        
        /* Tooltip styling */
        .btn[title]:hover::after {
            content: attr(title);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: #333;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            z-index: 1000;
        }
        
        /* Pulse animation for new order notifications */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .table-primary .btn-outline-info {
            animation: pulse 2s infinite;
        }
    </style>
</head>
<body>
    <?php include('includes/header.php'); ?>
    <?php include('includes/navbar.php'); ?>

    <div class="container mt-5 pt-5">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">
                        <i class="bi bi-bell me-2"></i>My Notifications
                        <?php if ($unread_count > 0): ?>
                            <span class="badge bg-danger ms-2"><?= $unread_count ?> new</span>
                        <?php endif; ?>
                    </h2>
                    <div>
                        <a href="profile.php" class="btn btn-outline-secondary me-2">
                            <i class="bi bi-arrow-left me-1"></i>Back to Profile
                        </a>
                        <form method="POST" class="d-inline">
                            <button type="submit" name="mark_all_read" class="btn btn-success">
                                <i class="bi bi-check-all me-1"></i>Mark All Read
                            </button>
                        </form>
                    </div>
                </div>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $_SESSION['success']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <form method="POST" id="notificationsForm">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="50">
                                                <input type="checkbox" id="selectAll" class="form-check-input">
                                            </th>
                                            <th>Message</th>
                                            <th>Type</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th width="150">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (mysqli_num_rows($notifications_result) > 0): ?>
                                            <?php while ($notification = mysqli_fetch_assoc($notifications_result)): ?>
                                                <tr class="<?= $notification['is_read'] == 0 ? 'table-primary' : '' ?>">
                                                    <td>
                                                        <input type="checkbox" name="notification_ids[]" value="<?= $notification['id'] ?>" class="form-check-input notification-checkbox">
                                                    </td>
                                                    <td>
                                                        <div class="fw-bold"><?= htmlspecialchars($notification['message']) ?></div>
                                                        <?php if ($notification['reference_id']): ?>
                                                            <small class="text-muted">Reference ID: <?= $notification['reference_id'] ?></small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-<?= $notification['action'] == 'order_status_update' ? 'info' : 'secondary' ?>">
                                                            <?= ucfirst(str_replace('_', ' ', $notification['action'])) ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div><?= date('M j, Y', strtotime($notification['created_at'])) ?></div>
                                                        <small class="text-muted"><?= time_elapsed_string($notification['created_at']) ?></small>
                                                    </td>
                                                    <td>
                                                        <?php if ($notification['is_read'] == 0): ?>
                                                            <span class="badge bg-warning">Unread</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-success">Read</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <?php if ($notification['action'] == 'order_status_update' && $notification['reference_id']): ?>
                                                                <a href="orders/my_orders.php?order_id=<?= $notification['reference_id'] ?>" class="btn btn-outline-info" title="View Order Details">
                                                                    <i class="bi bi-eye"></i>
                                                                </a>
                                                            <?php endif; ?>
                                                            <?php if ($notification['is_read'] == 0): ?>
                                                                <button type="button" class="btn btn-outline-primary" onclick="markNotificationRead(<?= $notification['id'] ?>)">
                                                                    <i class="bi bi-check"></i>
                                                                </button>
                                                            <?php endif; ?>
                                                            <button type="button" class="btn btn-outline-danger" onclick="deleteNotification(<?= $notification['id'] ?>)">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center py-4">
                                                    <i class="bi bi-bell text-muted" style="font-size: 2rem;"></i>
                                                    <p class="text-muted mt-2">No notifications found</p>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <?php if (mysqli_num_rows($notifications_result) > 0): ?>
                                <div class="card-footer d-flex justify-content-between align-items-center">
                                    <button type="submit" name="delete_selected" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete selected notifications?')">
                                        <i class="bi bi-trash"></i> Delete Selected
                                    </button>
                                    
                                    <!-- Pagination -->
                                    <?php if ($total_pages > 1): ?>
                                        <nav>
                                            <ul class="pagination pagination-sm mb-0">
                                                <?php if ($page > 1): ?>
                                                    <li class="page-item">
                                                        <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                                                    </li>
                                                <?php endif; ?>
                                                
                                                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                                    </li>
                                                <?php endfor; ?>
                                                
                                                <?php if ($page < $total_pages): ?>
                                                    <li class="page-item">
                                                        <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                                                    </li>
                                                <?php endif; ?>
                                            </ul>
                                        </nav>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('includes/footer.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    // Select all functionality
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.notification-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Update select all when individual checkboxes change
    document.querySelectorAll('.notification-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allCheckboxes = document.querySelectorAll('.notification-checkbox');
            const checkedCheckboxes = document.querySelectorAll('.notification-checkbox:checked');
            document.getElementById('selectAll').checked = allCheckboxes.length === checkedCheckboxes.length;
        });
    });

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

    function deleteNotification(notificationId) {
        if (confirm('Are you sure you want to delete this notification?')) {
            const form = document.getElementById('notificationsForm');
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'notification_ids[]';
            input.value = notificationId;
            form.appendChild(input);
            
            const deleteInput = document.createElement('input');
            deleteInput.type = 'hidden';
            deleteInput.name = 'delete_selected';
            deleteInput.value = '1';
            form.appendChild(deleteInput);
            
            form.submit();
        }
    }
    </script>
</body>
</html>
