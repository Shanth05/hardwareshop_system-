<?php
include('../includes/db.php');
$id = $_GET['id'];
$conn->query("DELETE FROM products WHERE id=$id");
header("Location: product_list.php");
?>
