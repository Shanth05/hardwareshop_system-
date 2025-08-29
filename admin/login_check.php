<?php
// login_check.php
// ensures admin is logged in. Adjust session key if you use a different one.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If not logged in, redirect to admin login page
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}
?>