<?php
// Handle reply submission BEFORE including header.php to avoid "headers already sent" error
if (isset($_POST['reply_msg_id'])) {
    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Database connection for this operation
    $conn = mysqli_connect("localhost", "root", "", "kn_raam_hardware");
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }
    
    $msg_id = intval($_POST['reply_msg_id']);
    $reply  = mysqli_real_escape_string($conn, $_POST['reply']);

    $update_sql = "UPDATE contact_messages 
                   SET reply='$reply', status='Replied', replied_at=NOW() 
                   WHERE id=$msg_id";

    if ($conn->query($update_sql)) {
        // Include email notifications for this operation
        include('../includes/email_notifications.php');
        
        // Send email notification to customer (logged for local development)
        $email_notifications = new EmailNotifications($conn);
        $email_notifications->sendMessageReplyNotification($msg_id);
        
        $_SESSION['success'] = "Reply sent successfully!";
    } else {
        $_SESSION['error'] = "Error: " . $conn->error;
    }
    header("Location: messages.php");
    exit();
}

$page_title = "Contact Messages";
include('header.php'); // includes login_check.php, DB connection, navbar + sidebar
include('../includes/email_notifications.php');

// Fetch all messages
$messages_query = "SELECT * FROM contact_messages ORDER BY created_at DESC";
$messages = mysqli_query($conn, $messages_query);
?>

<h2 class="mb-4">Contact Messages</h2>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success" id="successAlert"><?= $_SESSION['success']; ?></div>
    <script>
        setTimeout(() => { document.getElementById('successAlert').style.display = 'none'; }, 3000);
    </script>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger" id="errorAlert"><?= $_SESSION['error']; ?></div>
    <script>
        setTimeout(() => { document.getElementById('errorAlert').style.display = 'none'; }, 3000);
    </script>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<div class="card shadow">
    <div class="card-body p-0">
        <table class="table table-bordered mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Reply</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($messages) > 0): ?>
                    <?php while($msg = mysqli_fetch_assoc($messages)): ?>
                        <tr>
                            <td><?= $msg['id'] ?? ''; ?></td>
                            <td><?= htmlspecialchars($msg['name'] ?? ''); ?></td>
                            <td><?= htmlspecialchars($msg['email'] ?? ''); ?></td>
                            <td><?= nl2br(htmlspecialchars($msg['message'] ?? '')); ?></td>
                            <td><?= nl2br(htmlspecialchars($msg['reply'] ?? '')); ?></td>
                            <td>
                                <span class="badge <?= ($msg['status']=='Pending') ? 'bg-warning' : 'bg-success'; ?>">
                                    <?= $msg['status'] ?? ''; ?>
                                </span>
                            </td>
                            <td>
                                <?php if(($msg['status'] ?? '') == 'Pending'): ?>
                                    <!-- Reply Button -->
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#replyModal<?= $msg['id'] ?? ''; ?>">Reply</button>

                                    <!-- Reply Modal -->
                                    <div class="modal fade" id="replyModal<?= $msg['id'] ?? ''; ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <form method="post" class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Reply to <?= htmlspecialchars($msg['name'] ?? ''); ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <textarea name="reply" class="form-control" rows="5" required></textarea>
                                                    <input type="hidden" name="reply_msg_id" value="<?= $msg['id'] ?? ''; ?>">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-success">Send Reply</button>
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-secondary" disabled>Replied</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No messages found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Bootstrap JS (required for modal) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
