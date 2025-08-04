<?php
include('../includes/db.php');

$id = $_POST['id'];

$sql = "UPDATE products SET
        name='{$_POST['name']}',
        category='{$_POST['category']}',
        price={$_POST['price']},
        quantity={$_POST['quantity']},
        description='{$_POST['description']}'
        WHERE id=$id";

$conn->query($sql);
header("Location: product_list.php");
?>
