<?php include('../includes/db.php'); ?>
<?php include('../includes/header.php'); ?>

<h2>Customer Registration</h2>

<form action="register_process.php" method="POST">
    Name: <input type="text" name="name" required><br><br>
    Email: <input type="email" name="email" required><br><br>
    Password: <input type="password" name="password" required><br><br>
    <button type="submit">Register</button>
</form>
