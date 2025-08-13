<?php
include('login_check.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$product_id = intval($_GET['id']);
$conn = mysqli_connect("localhost", "root", "", "kn_raam_hardware");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Optionally, delete the image file first
$res = mysqli_query($conn, "SELECT image FROM products WHERE product_id=$product_id LIMIT 1");
if ($res && mysqli_num_rows($res) > 0) {
    $row = mysqli_fetch_assoc($res);
    if ($row['image'] && file_exists(__DIR__ . '/../uploads/' . $row['image'])) {
        unlink(__DIR__ . '/../uploads/' . $row['image']);
    }
}

// Delete product from DB
mysqli_query($conn, "DELETE FROM products WHERE product_id=$product_id");

// Redirect with success message
header("Location: products.php?msg=deleted");
exit();
?>
