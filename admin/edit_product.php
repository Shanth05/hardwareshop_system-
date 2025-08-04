<?php
include('../includes/db.php');
include('../includes/header.php');

$id = $_GET['id'];
$product = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();
?>

<h2>Edit Product</h2>

<form action="update_product.php" method="POST">
    <input type="hidden" name="id" value="<?= $id ?>">
    Name: <input name="name" value="<?= $product['name'] ?>"><br><br>
    Category: <input name="category" value="<?= $product['category'] ?>"><br><br>
    Price: <input name="price" value="<?= $product['price'] ?>"><br><br>
    Quantity: <input name="quantity" value="<?= $product['quantity'] ?>"><br><br>
    Description:<br>
    <textarea name="description"><?= $product['description'] ?></textarea><br><br>
    <button type="submit">Update Product</button>
</form>
