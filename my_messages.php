<?php
session_start();
include('includes/db.php');

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = intval($_SESSION['customer_id']);

// Mark messages as seen when user visits the page
if (isset($_GET['mark_read']) && $_GET['mark_read'] == 'true') {
    $update_sql = "UPDATE contact_messages SET seen_by_user = 1 
                   WHERE user_id = $user_id AND status = 'Replied' AND seen_by_user = 0";
    mysqli_query($conn, $update_sql);
}

// Fetch all messages for this user
$sql = "SELECT * FROM contact_messages WHERE user_id = $user_id ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Messages - K.N. Raam Hardware</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>
<body>

<?php include('includes/navbar.php'); ?>

<div class="container mt-5 pt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">
                    <i class="bi bi-envelope me-2"></i>My Messages
                </h2>
                <a href="/hardware/contact.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Send New Message
                </a>
            </div>

            <?php if (mysqli_num_rows($result) > 0): ?>
                <div class="card shadow">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Your Message</th>
                                        <th>Reply</th>
                                        <th>Status</th>
                                        <th>Sent At</th>
                                        <th>Replied At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                                        <tr class="<?= ($row['status'] == 'Replied' && $row['seen_by_user'] == 0) ? 'table-warning' : ''; ?>" data-message-id="<?= $row['id'] ?>">
                                            <td>
                                                <div class="message-content">
                                                    <strong><?= htmlspecialchars($row['name']) ?></strong>
                                                    <div class="text-muted small mt-1">
                                                        <?= nl2br(htmlspecialchars(substr($row['message'], 0, 100))) ?>
                                                        <?= strlen($row['message']) > 100 ? '...' : '' ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($row['reply']): ?>
                                                    <div class="reply-content">
                                                        <div class="text-muted small mt-1">
                                                            <?= nl2br(htmlspecialchars(substr($row['reply'], 0, 100))) ?>
                                                            <?= strlen($row['reply']) > 100 ? '...' : '' ?>
                                                        </div>
                                                        <?php if ($row['seen_by_user'] == 0): ?>
                                                            <span class="badge bg-warning">New</span>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php else: ?>
                                                    <em class="text-muted">No reply yet</em>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge <?= ($row['status'] == 'Pending') ? 'bg-warning' : 'bg-success'; ?>">
                                                    <?= $row['status']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= date('M j, Y g:i A', strtotime($row['created_at'])); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <?php if ($row['replied_at']): ?>
                                                    <small class="text-muted">
                                                        <?= date('M j, Y g:i A', strtotime($row['replied_at'])); ?>
                                                    </small>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#messageModal<?= $row['id'] ?>">
                                                    <i class="bi bi-eye"></i> View
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Message Detail Modal -->
                                        <div class="modal fade" id="messageModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">
                                                            <i class="bi bi-envelope me-2"></i>Message Details
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <h6 class="text-primary">Your Message</h6>
                                                                <div class="card bg-light">
                                                                    <div class="card-body">
                                                                        <p><strong>From:</strong> <?= htmlspecialchars($row['name']) ?></p>
                                                                        <p><strong>Email:</strong> <?= htmlspecialchars($row['email']) ?></p>
                                                                        <p><strong>Sent:</strong> <?= date('F j, Y g:i A', strtotime($row['created_at'])) ?></p>
                                                                        <hr>
                                                                        <p><?= nl2br(htmlspecialchars($row['message'])) ?></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <h6 class="text-success">Admin Reply</h6>
                                                                <?php if ($row['reply']): ?>
                                                                    <div class="card bg-light">
                                                                        <div class="card-body">
                                                                            <p><strong>Replied:</strong> <?= date('F j, Y g:i A', strtotime($row['replied_at'])) ?></p>
                                                                            <hr>
                                                                            <p><?= nl2br(htmlspecialchars($row['reply'])) ?></p>
                                                                        </div>
                                                                    </div>
                                                                <?php else: ?>
                                                                    <div class="card bg-light">
                                                                        <div class="card-body text-center">
                                                                            <i class="bi bi-clock text-muted" style="font-size: 2rem;"></i>
                                                                            <p class="text-muted mt-2">No reply yet</p>
                                                                            <small class="text-muted">We'll get back to you soon!</small>
                                                                        </div>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <?php if ($row['status'] == 'Replied' && $row['seen_by_user'] == 0): ?>
                                                            <button type="button" class="btn btn-success" onclick="markAsRead(<?= $row['id'] ?>)">
                                                                <i class="bi bi-check-circle me-1"></i>Mark as Read
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card shadow">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-envelope text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">No Messages Yet</h5>
                        <p class="text-muted">You haven't sent any messages yet.</p>
                        <a href="/hardware/contact.php" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>Send Your First Message
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function markAsRead(messageId) {
    // Send AJAX request to mark message as read
    fetch('/hardware/mark_message_read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'message_id=' + messageId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the "New" badge and table-warning class
            const row = document.querySelector(`tr[data-message-id="${messageId}"]`);
            if (row) {
                row.classList.remove('table-warning');
                const badge = row.querySelector('.badge.bg-warning');
                if (badge) badge.remove();
            }
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById(`messageModal${messageId}`));
            if (modal) modal.hide();
            
            // Reload page to update navbar badge
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Auto-mark messages as read when modal is opened
document.addEventListener('DOMContentLoaded', function() {
    const modals = document.querySelectorAll('[id^="messageModal"]');
    modals.forEach(modal => {
        modal.addEventListener('shown.bs.modal', function() {
            const messageId = this.id.replace('messageModal', '');
            const row = document.querySelector(`tr[data-message-id="${messageId}"]`);
            if (row && row.classList.contains('table-warning')) {
                markAsRead(messageId);
            }
        });
    });
});
</script>

</body>
</html>
