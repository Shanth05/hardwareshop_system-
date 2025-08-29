<?php
session_start();

// If admin is already logged in, redirect to dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Redirect to main login page with admin parameter
header("Location: ../login.php?admin=1");
exit();
?>
