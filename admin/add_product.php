<?php include('../includes/header.php'); ?>

<h2>Add Product</h2>

<form action="save_product.php" method="POST" enctype="multipart/form-data">
    Name: <input name="name" required><br><br>
    Category: <input name="category" required><br><br>
    Price: <input name="price" type="number" step="0.01" required><br><br>
    Quantity: <input name="quantity" type="number" required><br><br>
    Image: <input type="file" name="image" required><br><br>
    Description:<br>
    <textarea name="description"></textarea><br><br>
    <button type="submit">Add Product</button>
</form>