<?php
session_start();
include('../includes/db.php');

$email = $_POST['email'];
$password = $_POST['password'];

$result = $conn->query("SELECT * FROM customers WHERE email='$email'");
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['customer_id'] = $user['id'];
    $_SESSION['customer_name'] = $user['name'];
    header("Location: ../shop.php");
} else {
    echo "Invalid email or password.";
}
?>
