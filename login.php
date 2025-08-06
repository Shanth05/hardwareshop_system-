<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | K.N. Raam Hardware</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow">
        <div class="card-body">
          <h2 class="text-center mb-4">Login</h2>
          <form action="" method="POST">
            <div class="mb-3">
              <label for="username" class="form-label">Username:</label>
              <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password:</label>
              <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <p>If you are not registered, <a href="register.php">click here</a> to register.</p>
            <div class="d-flex justify-content-end">
              <input type="reset" value="Clear" class="btn btn-danger me-2">
              <input type="submit" name="login" value="Login" class="btn btn-primary">
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>

<?php
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Consider using password_hash() in production

    $conn = mysqli_connect('localhost', 'root', '', 'kn_raam_hardware');
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $res = mysqli_query($conn, $sql);

    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        if ($row['user_type'] === "customer") {
            $_SESSION['customer_id'] = $row['user_id'];
            header("Location: index.php");
            exit;
        } elseif ($row['user_type'] === "admin") {
            $_SESSION['admin_id'] = $row['user_id'];
            header("Location: admin/dashboard.php");
            exit;
        }
    } else {
        echo '<script>alert("Login failed. Invalid username or password!"); window.location.href="login.php";</script>';
    }
}
?>
