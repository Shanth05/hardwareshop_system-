<?php
session_start();

// If admin is logged in, redirect to dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
} else {
    // If not logged in, redirect to admin login page
    header("Location: login.php");
    exit();
}
?>

