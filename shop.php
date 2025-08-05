<?php include('includes/db.php'); ?>
<?php include('includes/header.php'); ?>

<h2>Our Products</h2>

<?php
$result = $conn->query("SELECT * FROM products");

while ($row = $result->fetch_assoc()) {
    echo "<div style='border:1px solid #ccc; padding:10px; margin:10px; display:inline-block;'>";
    echo "<img src='{$row['image']}' width='100' height='100'><br>";
    echo "<strong>{$row['name']}</strong><br>";
    echo "Category: {$row['category']}<br>";
    echo "Price: Rs. {$row['price']}<br>";
    echo "<form action='orders/add_to_cart.php' method='POST'>";
    echo "<input type='hidden' name='product_id' value='{$row['id']}'>";
    echo "<input type='number' name='quantity' value='1' min='1' max='{$row['quantity']}'><br>";
    echo "<button type='submit'>Add to Cart</button>";
    echo "</form></div>";
}
?>
