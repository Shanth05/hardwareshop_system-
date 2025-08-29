<?php
// Simple script to create the notifications table
$conn = mysqli_connect("localhost", "root", "", "kn_raam_hardware");
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Check if table already exists
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'notifications'");
if (mysqli_num_rows($table_check) > 0) {
    echo "Notifications table already exists!";
    exit;
}

// Create the notifications table
$create_table = "
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('admin', 'customer') NOT NULL,
    action VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    reference_id INT,
    user_id INT,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_type (type),
    INDEX idx_user_id (user_id),
    INDEX idx_is_read (is_read)
)";

if (mysqli_query($conn, $create_table)) {
    echo "Notifications table created successfully!";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
