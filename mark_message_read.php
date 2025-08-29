<?php
session_start();
include('includes/db.php');

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Get message ID
$message_id = intval($_POST['message_id'] ?? 0);
$user_id = intval($_SESSION['customer_id']);

if ($message_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid message ID']);
    exit();
}

// Verify the message belongs to this user and has a reply
$check_sql = "SELECT id FROM contact_messages 
              WHERE id = $message_id AND user_id = $user_id 
              AND status = 'Replied' AND seen_by_user = 0";

$check_result = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($check_result) == 0) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Message not found or already read']);
    exit();
}

// Mark message as read
$update_sql = "UPDATE contact_messages SET seen_by_user = 1 WHERE id = $message_id";

if (mysqli_query($conn, $update_sql)) {
    echo json_encode(['success' => true, 'message' => 'Message marked as read']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
}
?>

