<?php
include('../includes/db.php');

$imgPath = '../uploads/' . basename($_FILES['image']['name']);
move_uploaded_file($_FILES['image']['tmp_name'], $imgPath);

$sql = "INSERT INTO products (name, category, price, quantity, image, description)
        VALUES (
            '{$_POST['name']}',
            '{$_POST['category']}',
            {$_POST['price']},
            {$_POST['quantity']},
            '$imgPath',
            '{$_POST['description']}'
        )";

$conn->query($sql);
header("Location: product_list.php");
?>
