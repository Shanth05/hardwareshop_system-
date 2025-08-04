<?php
session_start();
include('../includes/db.php');

$username = $_POST['username'];
$password = $_POST['password'];

$result = $conn->query("SELECT * FROM admin WHERE username='$username'");
$admin = $result->fetch_assoc();

if ($admin && password_verify($password, $admin['password'])) {
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_user'] = $admin['username'];
    header("Location: index.php");
} else {
    echo "Invalid credentials.";
}
?>
