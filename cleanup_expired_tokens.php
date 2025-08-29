<?php
// Cleanup expired password reset tokens
// This script can be run periodically to clean up expired tokens

$conn = mysqli_connect('localhost', 'root', '', 'kn_raam_hardware');
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Delete expired tokens
$sql = "DELETE FROM password_resets WHERE expires_at < NOW() OR used = 1";

if (mysqli_query($conn, $sql)) {
    $affected_rows = mysqli_affected_rows($conn);
    echo "✅ Cleanup completed! Removed $affected_rows expired/used tokens.";
} else {
    echo "❌ Error during cleanup: " . mysqli_error($conn);
}

mysqli_close($conn);
?>

