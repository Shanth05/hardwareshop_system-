<?php
session_start();
include('../includes/db.php');

// Only superadmin can proceed
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'superadmin') {
    echo "Access Denied.";
    exit;
}

$username = $_POST['username'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$role = $_POST['role'];

// Check if username already exists
$check = $conn->query("SELECT * FROM admin WHERE username='$username'");
if ($check->num_rows > 0) {
    echo "Username already exists. Try another.";
    exit;
}

$conn->query("INSERT INTO admin (username, password, role) VALUES ('$username', '$password', '$role')");

echo "Admin registered successfully. <a href='index.php'>Back to Dashboard</a>";
?>
