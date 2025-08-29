<?php
$page_title = "Notifications";
include('header.php');
?>

<style>
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
        
        /* Facebook-style notification clickable styling */
        .notification-row {
            transition: background-color 0.2s ease;
        }
        
        .notification-row:hover {
            background-color: #f8f9fa !important;
        }
        
        .notification-row td[onclick] {
            cursor: pointer;
        }
        
        .notification-row td[onclick]:hover {
            background-color: #e9ecef;
        }
</style>

<?php
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

// Handle bulk actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['mark_all_read'])) {
        $update_sql = "UPDATE notifications SET is_read = 1 WHERE type = 'admin'";
        mysqli_query($conn, $update_sql);
        $_SESSION['success'] = "All notifications marked as read!";
        header("Location: notifications.php");
        exit;
    }
    
    if (isset($_POST['delete_selected']) && isset($_POST['notification_ids'])) {
        $notification_ids = array_map('intval', $_POST['notification_ids']);
        $ids_string = implode(',', $notification_ids);
        $delete_sql = "DELETE FROM notifications WHERE id IN ($ids_string) AND type = 'admin'";
        mysqli_query($conn, $delete_sql);
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
$total_query = "SELECT COUNT(*) as count FROM notifications WHERE type = 'admin'";
$total_result = mysqli_query($conn, $total_query);
$total_notifications = mysqli_fetch_assoc($total_result)['count'];
$total_pages = ceil($total_notifications / $per_page);

// Get notifications
$notifications_query = "
    SELECT * FROM notifications 
    WHERE type = 'admin' 
    ORDER BY created_at DESC 
    LIMIT $per_page OFFSET $offset
";
$notifications_result = mysqli_query($conn, $notifications_query);
?>

<h2 class="mb-4">Notifications</h2>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $_SESSION['success']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Notifications</h5>
        <div>
            <form method="POST" class="d-inline">
                <button type="submit" name="mark_all_read" class="btn btn-success btn-sm">
                    <i class="bi bi-check-all"></i> Mark All Read
                </button>
            </form>
        </div>
    </div>
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
                                <tr class="<?= $notification['is_read'] == 0 ? 'table-primary' : '' ?> notification-row" data-notification-id="<?= $notification['id'] ?>" style="cursor: pointer;">
                                    <td>
                                        <input type="checkbox" name="notification_ids[]" value="<?= $notification['id'] ?>" class="form-check-input notification-checkbox" onclick="event.stopPropagation();">
                                    </td>
                                    <td onclick="handleNotificationClick(<?= $notification['id'] ?>, '<?= $notification['action'] ?>', <?= $notification['reference_id'] ?: 'null' ?>)">
                                        <div class="fw-bold"><?= htmlspecialchars($notification['message']) ?></div>
                                        <?php if ($notification['reference_id']): ?>
                                            <small class="text-muted">Reference ID: <?= $notification['reference_id'] ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $notification['action'] == 'new_order' ? 'success' : ($notification['action'] == 'new_message' ? 'info' : 'secondary') ?>">
                                            <?= ucfirst(str_replace('_', ' ', $notification['action'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div><?= date('M j, Y', strtotime($notification['created_at'])) ?></div>
                                        <small class="text-muted"><?= date('g:i A', strtotime($notification['created_at'])) ?></small>
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
                                            <?php if ($notification['action'] == 'new_order' && $notification['reference_id']): ?>
                                                <a href="edit_order_status.php?order_id=<?= $notification['reference_id'] ?>" class="btn btn-outline-info" title="View Order Details">
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

// Facebook-style notification click handler
function handleNotificationClick(notificationId, action, referenceId) {
    // First mark as read if unread
    const notificationRow = document.querySelector(`[data-notification-id="${notificationId}"]`);
    const isUnread = notificationRow.classList.contains('table-primary');
    
    if (isUnread) {
        // Mark as read immediately for better UX
        markNotificationRead(notificationId);
    }
    
    // Handle different notification types
    if (action === 'new_order' && referenceId) {
        // Navigate to order details
        window.location.href = `edit_order_status.php?order_id=${referenceId}`;
    } else if (action === 'new_message' && referenceId) {
        // Navigate to messages
        window.location.href = `messages.php`;
    } else {
        // For other types, just mark as read
        if (isUnread) {
            markNotificationRead(notificationId);
        }
    }
}

function markNotificationRead(notificationId) {
    fetch('mark_notification_read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'notification_id=' + notificationId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI immediately without page reload
            const notificationRow = document.querySelector(`[data-notification-id="${notificationId}"]`);
            if (notificationRow) {
                notificationRow.classList.remove('table-primary');
                
                // Update status badge
                const statusCell = notificationRow.querySelector('td:nth-child(5)');
                if (statusCell) {
                    statusCell.innerHTML = '<span class="badge bg-success">Read</span>';
                }
                
                // Remove mark as read button
                const markReadBtn = notificationRow.querySelector('.btn-outline-primary');
                if (markReadBtn) {
                    markReadBtn.remove();
                }
            }
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

<?php include('footer.php'); ?>
