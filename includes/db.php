<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'kn_raam_hardware'; // Ensure this matches the actual database name

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
