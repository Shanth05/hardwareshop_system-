<?php
session_start();
include('../includes/db.php');
include('../includes/header.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

echo "<h2>Customer Orders</h2>";

$result = $conn->query("SELECT o.*, c.name FROM orders o JOIN customers c ON o.customer_id = c.id ORDER BY o.order_date DESC");

while ($order = $result->fetch_assoc()) {
    echo "<div style='border:1px solid #ccc; padding:10px;'>";
    echo "<strong>Order ID:</strong> {$order['id']}<br>";
    echo "<strong>Customer:</strong> {$order['name']}<br>";
    echo "<strong>Total:</strong> Rs. {$order['total']}<br>";
    echo "<strong>Date:</strong> {$order['order_date']}<br>";

    $items = $conn->query("SELECT * FROM order_details od 
                           JOIN products p ON od.product_id = p.id 
                           WHERE order_id = {$order['id']}");
    
    while ($item = $items->fetch_assoc()) {
        echo "- {$item['name']} x {$item['quantity']} = Rs. " . ($item['price'] * $item['quantity']) . "<br>";
    }

    echo "</div><br>";
}
?>
