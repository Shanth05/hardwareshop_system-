<?php
session_start();
include('../includes/db.php');
include('../includes/header.php');

if (!isset($_SESSION['customer_id'])) {
    header("Location: ../customer/login.php");
    exit;
}

$customer_id = $_SESSION['customer_id'];

$result = $conn->query("SELECT * FROM orders WHERE customer_id=$customer_id ORDER BY order_date DESC");

echo "<h2>My Orders</h2>";

while ($order = $result->fetch_assoc()) {
    echo "<div style='border:1px solid #ccc; margin:10px; padding:10px;'>";
    echo "<strong>Order ID:</strong> {$order['id']}<br>";
    echo "<strong>Date:</strong> {$order['order_date']}<br>";
    echo "<strong>Total:</strong> Rs. {$order['total']}<br>";

    $details = $conn->query("SELECT * FROM order_details od 
                             JOIN products p ON od.product_id = p.id 
                             WHERE order_id = {$order['id']}");
    
    while ($item = $details->fetch_assoc()) {
        echo "- {$item['name']} x {$item['quantity']} = Rs. " . ($item['price'] * $item['quantity']) . "<br>";
    }

    echo "</div>";
}
?>
