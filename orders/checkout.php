<?php
session_start();
include('../includes/db.php');

if (!isset($_SESSION['customer_id']) || !isset($_SESSION['cart'])) {
    header("Location: ../customer/login.php");
    exit;
}

$customer_id = $_SESSION['customer_id'];
$total = 0;

foreach ($_SESSION['cart'] as $product_id => $qty) {
    $result = $conn->query("SELECT price FROM products WHERE id=$product_id");
    $product = $result->fetch_assoc();
    $total += $product['price'] * $qty;
}

// Insert into orders
$conn->query("INSERT INTO orders (customer_id, total) VALUES ($customer_id, $total)");
$order_id = $conn->insert_id;

// Insert order details
foreach ($_SESSION['cart'] as $product_id => $qty) {
    $result = $conn->query("SELECT price FROM products WHERE id=$product_id");
    $product = $result->fetch_assoc();
    $price = $product['price'];

    $conn->query("INSERT INTO order_details (order_id, product_id, quantity, price) 
                  VALUES ($order_id, $product_id, $qty, $price)");

    // Decrease stock
    $conn->query("UPDATE products SET quantity = quantity - $qty WHERE id=$product_id");
}

// Clear cart
unset($_SESSION['cart']);

header("Location: order_success.php");
?>
