<?php
session_start();
include('../includes/db.php');
include('../includes/header.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

echo "<h2>All Products</h2>";
echo "<a href='add_product.php'>+ Add New Product</a><br><br>";

$result = $conn->query("SELECT * FROM products");

while ($row = $result->fetch_assoc()) {
    echo "<div style='border:1px solid #ccc; padding:10px;'>";
    echo "<strong>{$row['name']}</strong> - Rs. {$row['price']} - Qty: {$row['quantity']}<br>";
    echo "<a href='edit_product.php?id={$row['id']}'>Edit</a> | ";
    echo "<a href='delete_product.php?id={$row['id']}'>Delete</a>";
    echo "</div>";
}
?>
