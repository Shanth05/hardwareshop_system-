<?php
session_start();
include('includes/db.php');

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = intval($_SESSION['customer_id']);

// Fetch all messages of this customer
$sql = "SELECT * FROM contact_messages WHERE user_id=$user_id ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

// Mark replies as seen
$update_seen = "UPDATE contact_messages SET seen_by_user=1 
                WHERE user_id=$user_id AND status='Replied'";
mysqli_query($conn, $update_seen);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Messages - K.N. Raam Hardware</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5">
  <h3 class="mb-4">My Messages</h3>
  <div class="card shadow">
    <div class="card-body p-0">
      <table class="table table-bordered mb-0">
        <thead>
          <tr>
            <th>Your Message</th>
            <th>Reply</th>
            <th>Status</th>
            <th>Sent At</th>
            <th>Replied At</th>
          </tr>
        </thead>
        <tbody>
          <?php if(mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
              <tr>
                <td><?= nl2br(htmlspecialchars($row['message'])); ?></td>
                <td><?= $row['reply'] ? nl2br(htmlspecialchars($row['reply'])) : '<em class="text-muted">No reply yet</em>'; ?></td>
                <td>
                  <span class="badge <?= ($row['status']=='Pending') ? 'bg-warning' : 'bg-success'; ?>">
                    <?= $row['status']; ?>
                  </span>
                </td>
                <td><?= $row['created_at']; ?></td>
                <td><?= $row['replied_at'] ?? '-'; ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="text-center">No messages found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
