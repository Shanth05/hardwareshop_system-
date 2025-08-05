<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
include('../includes/header.php');
?>

<h2>Admin Dashboard</h2>
<p>Welcome, <?= $_SESSION['admin_user']; ?> (<?= $_SESSION['admin_role']; ?>)</p>

<ul>
  <li><a href="product_list.php">Manage Products</a></li>
  <li><a href="orders.php">View Orders</a></li>
  <?php if ($_SESSION['admin_role'] === 'superadmin'): ?>
    <li><a href="register.php">Register New Admin</a></li>
  <?php endif; ?>
  <li><a href="logout.php">Logout</a></li>
</ul>
