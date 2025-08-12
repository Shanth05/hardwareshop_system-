<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login | K.N. Raam Hardware</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background: linear-gradient(135deg, #3b8d99, #6b6b83, #aa4b6b);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .card {
      animation: fadeIn 1s ease-in-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-5">
        <div class="card shadow-lg rounded-4">
          <div class="card-body p-4">
            <h3 class="text-center text-primary mb-4 fw-bold">Login</h3>
            <form action="" method="POST">
              <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control rounded-pill" required />
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control rounded-pill" required />
              </div>
              <p class="text-center mt-2">
                Not registered yet? <a href="register.php" class="text-decoration-none">Sign up here</a>
              </p>
              <div class="d-flex justify-content-end mt-4">
                <button type="reset" class="btn btn-outline-danger me-2 rounded-pill">Clear</button>
                <button type="submit" name="login" class="btn btn-primary rounded-pill">Login</button>
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
    $password = md5($_POST['password']); // Note: Use password_hash() in production for security

    $conn = mysqli_connect('localhost', 'root', '', 'kn_raam_hardware');
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Sanitize input for security (optional but recommended)
    $username_safe = mysqli_real_escape_string($conn, $username);

    $sql = "SELECT * FROM users WHERE username='$username_safe' AND password='$password'";
    $res = mysqli_query($conn, $sql);

    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        if ($row['user_type'] === 'customer') {
            $_SESSION['customer_id'] = $row['user_id'];
            $_SESSION['username'] = $row['username'];  // Set username in session
            header("Location: index.php");
            exit;
        } else if ($row['user_type'] === 'admin') {
            $_SESSION['admin_id'] = $row['user_id'];
            $_SESSION['username'] = $row['username'];  // Optional: set admin username
            header("Location: admin/dashboard.php");
            exit;
        }
    } else {
        echo "<script>alert('Invalid usern
        ame or password!');</script>";
    }

    mysqli_close($conn);
}
?>
