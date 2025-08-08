<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sign Up | K.N. Raam Hardware</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #134e5e, #71b280);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .card {
      animation: fadeIn 1s ease-in-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-lg rounded-4">
          <div class="card-body p-4">
            <h3 class="text-center text-success fw-bold mb-4">Sign Up</h3>
            <form method="POST" action="">
              <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control rounded-pill" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control rounded-pill" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control rounded-pill" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control rounded-pill" required>
              </div>
              <div class="d-flex justify-content-end mt-4">
                <button type="reset" class="btn btn-outline-danger me-2 rounded-pill">Clear</button>
                <button type="submit" name="register" class="btn btn-success rounded-pill">Sign Up</button>
              </div>
              <p class="text-center mt-3">Already have an account? <a href="login.php" class="text-decoration-none">Login here</a></p>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>

<?php
if (isset($_POST['register'])) {
    $conn = mysqli_connect("localhost", "root", "", "kn_raam_hardware");
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $confirm = md5($_POST['confirm_password']);

    if ($password !== $confirm) {
        echo "<script>alert('Passwords do not match');</script>";
    } else {
        $sql = "INSERT INTO users (username, mail, password, user_type) VALUES ('$username', '$email', '$password', 'customer')";
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Registered successfully! Please log in.'); window.location='login.php';</script>";
        } else {
            echo "<script>alert('Registration failed');</script>";
        }
    }
}
?>
