<?php
session_start();

if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['customer_id'];

if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    die("Invalid order ID.");
}

$order_id = intval($_GET['order_id']);

$conn = new mysqli("localhost", "root", "", "kn_raam_hardware");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the order belongs to the logged-in user and its status allows cancel
$stmt = $conn->prepare("SELECT order_status FROM orders WHERE order_id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$stmt->bind_result($order_status);
if (!$stmt->fetch()) {
    $stmt->close();
    $conn->close();
    die("Order not found or you don't have permission to cancel it.");
}
$stmt->close();

// Only allow cancel if status is 'ordered' or 'processing'
if (!in_array($order_status, ['ordered', 'processing'])) {
    $conn->close();
    die("This order cannot be cancelled.");
}

// Update order status to 'cancelled'
$stmt = $conn->prepare("UPDATE orders SET order_status = 'cancelled' WHERE order_id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    echo "<script>alert('Order cancelled successfully.'); window.location.href='profile.php';</script>";
} else {
    $stmt->close();
    $conn->close();
    die("Failed to cancel order. Please try again.");
}
?>
