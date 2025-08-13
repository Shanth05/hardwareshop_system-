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

            <?php
            if (isset($_POST['login'])) {
                $conn = mysqli_connect('localhost', 'root', '', 'kn_raam_hardware');
                if (!$conn) {
                    die("Database connection failed: " . mysqli_connect_error());
                }

                $username = trim($_POST['username']);
                $password = $_POST['password'];

                $stmt = $conn->prepare("SELECT user_id, username, password, user_type FROM users WHERE username = ?");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $res = $stmt->get_result();

                if ($res && $res->num_rows > 0) {
                    $row = $res->fetch_assoc();

                    if (password_verify($password, $row['password'])) {
                        $_SESSION['username'] = $row['username'];

                        if ($row['user_type'] === 'customer') {
                            $_SESSION['customer_id'] = $row['user_id'];
                            header("Location: index.php");
                            exit;
                        } elseif ($row['user_type'] === 'admin') {
                            $_SESSION['admin_id'] = $row['user_id'];
                            header("Location: admin/dashboard.php");
                            exit;
                        }
                    } else {
                        echo '<div class="alert alert-danger mt-3">Invalid password!</div>';
                    }
                } else {
                    echo '<div class="alert alert-danger mt-3">Invalid username!</div>';
                }

                $stmt->close();
                $conn->close();
            }
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
