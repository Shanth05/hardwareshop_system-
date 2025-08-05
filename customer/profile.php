<?php
session_start();
include('../includes/db.php');
include('../includes/header.php');

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}

echo "<h2>Welcome, " . $_SESSION['customer_name'] . "</h2>";
echo "<p>Email: (not shown here)</p>";
?>
