<?php
// Database connection info
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kn_raam_hardware";

// Connect to MySQL
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Admin user info
$admin_username = "admin";
$admin_password = "admin";  // You can change this before running
$admin_mail = "admin@example.com";
$contact_no = "";
$gender = "";
$user_type = "admin";

// Hash the password using password_hash (bcrypt)
$hashed_password = password_hash($admin_password, PASSWORD_BCRYPT);

// Delete existing users (optional, be careful!)
$conn->query("DELETE FROM users");

// Insert the new admin user
$stmt = $conn->prepare("INSERT INTO users (username, password, mail, contact_no, gender, user_type) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $admin_username, $hashed_password, $admin_mail, $contact_no, $gender, $user_type);

if ($stmt->execute()) {
    echo "Admin user created successfully.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
