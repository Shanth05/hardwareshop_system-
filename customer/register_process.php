<?php
include('../includes/db.php');

$name = $_POST['name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// Check if email already exists
$check = $conn->query("SELECT * FROM customers WHERE email='$email'");
if ($check->num_rows > 0) {
    echo "Email already registered.";
    exit;
}

$sql = "INSERT INTO customers (name, email, password) VALUES ('$name', '$email', '$password')";
if ($conn->query($sql)) {
    header("Location: login.php");
} else {
    echo "Registration failed: " . $conn->error;
}
?>
