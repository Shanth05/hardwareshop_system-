<?php
// Quick Admin Reset - Use this for immediate access
// WARNING: Delete this file after use!

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kn_raam_hardware";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set new admin credentials
$admin_username = "admin";
$admin_password = "admin123"; // Change this to your preferred password
$admin_email = "admin@knraamhardware.com";

// Hash password
$hashed_password = password_hash($admin_password, PASSWORD_ARGON2ID);

// Check if admin exists and update, otherwise create new
$check_sql = "SELECT user_id FROM users WHERE user_type = 'admin' LIMIT 1";
$result = $conn->query($check_sql);

if ($result->num_rows > 0) {
    // Update existing admin
    $sql = "UPDATE users SET username = '$admin_username', password = '$hashed_password', mail = '$admin_email' WHERE user_type = 'admin'";
    if ($conn->query($sql) === TRUE) {
        echo "<h2>✅ Admin Password Reset Successfully!</h2>";
        echo "<p><strong>Username:</strong> $admin_username</p>";
        echo "<p><strong>Password:</strong> $admin_password</p>";
        echo "<p><strong>Email:</strong> $admin_email</p>";
        echo "<p><a href='login.php'>Go to Admin Login</a></p>";
    } else {
        echo "Error updating admin: " . $conn->error;
    }
} else {
    // Create new admin
    $sql = "INSERT INTO users (username, password, mail, user_type) VALUES ('$admin_username', '$hashed_password', '$admin_email', 'admin')";
    if ($conn->query($sql) === TRUE) {
        echo "<h2>✅ New Admin Account Created!</h2>";
        echo "<p><strong>Username:</strong> $admin_username</p>";
        echo "<p><strong>Password:</strong> $admin_password</p>";
        echo "<p><strong>Email:</strong> $admin_email</p>";
        echo "<p><a href='login.php'>Go to Admin Login</a></p>";
    } else {
        echo "Error creating admin: " . $conn->error;
    }
}

$conn->close();
?>

