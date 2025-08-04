<?php
declare(strict_types=1);

include '../components/connect.php';
session_start();

$message = [];

if (isset($_POST['submit'])) {
    $name = trim($_POST['name'] ?? '');
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $pass = $_POST['pass'] ?? '';
    $pass = filter_var($pass, FILTER_SANITIZE_STRING);

    $select_admin = $conn->prepare("SELECT * FROM `admins` WHERE name = ?");
    $select_admin->execute([$name]);
    $row = $select_admin->fetch(PDO::FETCH_ASSOC);

    if ($row && password_verify($pass, $row['password'])) {
        $_SESSION['admin_id'] = $row['id'];
        header('location:dashboard.php');
        exit();
    } else {
        $message[] = 'Incorrect username or password!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php
if (!empty($message)) {
    foreach ($message as $msg) {
        echo '
        <div class="message">
            <span>' . htmlspecialchars($msg) . '</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
        </div>
        ';
    }
}
?>

<section class="form-container">
   <form action="" method="post">
      <h3>Login now</h3>
      <p>Default username = <span>admin</span> & password = <span>111</span></p>
      <input type="text" name="name" required placeholder="Enter your username" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="pass" required placeholder="Enter your password" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="Login now" class="btn" name="submit">
   </form>
</section>

</body>
</html>
