<?php
session_start();
include('../includes/db.php');
include('../includes/header.php');

echo "<h2>Your Cart</h2>";

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<p>Your cart is empty.</p>";
    exit;
}

$total = 0;

foreach ($_SESSION['cart'] as $product_id => $qty) {
    $result = $conn->query("SELECT * FROM products WHERE id=$product_id");
    $product = $result->fetch_assoc();

    $subtotal = $product['price'] * $qty;
    $total += $subtotal;

    echo "<div style='border-bottom:1px solid #ccc; padding:10px;'>";
    echo "<strong>{$product['name']}</strong> - Rs. {$product['price']} x $qty = Rs. $subtotal";
    echo "</div>";
}

echo "<h3>Total: Rs. $total</h3>";

echo "<form action='checkout.php' method='POST'>
        <button type='submit'>Proceed to Checkout</button>
      </form>";
?>
