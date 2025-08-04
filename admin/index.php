<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
include('../includes/header.php');
?>

<h2>Admin Dashboard</h2>
<p>Welcome, <?= $_SESSION['admin_user']; ?></p>

<ul>
  <li><a href="product_list.php">Manage Products</a></li>
  <li><a href="orders.php">View Orders</a></li>
  <li><a href="logout.php">Logout</a></li>
</ul>
