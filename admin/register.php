<?php
session_start();
include('../includes/db.php');
include('../includes/header.php');

// Only superadmin can register new admins
if (!isset($_SESSION['admin_id']) || $_SESSION['admin_role'] !== 'superadmin') {
    echo "<h3>Access Denied: Only Superadmin Can Register Admins</h3>";
    exit;
}
?>

<h2>Register New Admin User</h2>

<form method="POST" action="register_process.php">
    Username: <input name="username" required><br><br>
    Password: <input name="password" type="password" required><br><br>
    Role:
    <select name="role">
        <option value="admin">Admin</option>
        <option value="seller">Seller</option>
        <option value="storekeeper">Storekeeper</option>
        <option value="cashier">Cashier</option>
    </select><br><br>
    <button type="submit">Create Admin</button>
</form>
