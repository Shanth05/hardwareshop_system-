<?php
// Create password_resets table
$conn = mysqli_connect('localhost', 'root', '', 'kn_raam_hardware');
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$sql = "CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    used TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_token (token),
    INDEX idx_user_id (user_id),
    INDEX idx_expires (expires_at),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
)";

if (mysqli_query($conn, $sql)) {
    echo "✅ Password resets table created successfully!";
} else {
    echo "❌ Error creating table: " . mysqli_error($conn);
}

mysqli_close($conn);
?>

