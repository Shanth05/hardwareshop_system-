<?php include('../includes/db.php'); ?>
<?php include('../includes/header.php'); ?>

<h2>Admin Login</h2>

<form action="login_process.php" method="POST">
    Username: <input type="text" name="username" required><br><br>
    Password: <input type="password" name="password" required><br><br>
    <button type="submit">Login</button>
</form>
