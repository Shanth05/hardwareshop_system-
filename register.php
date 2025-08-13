<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sign Up | K.N. Raam Hardware</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
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
                <label class="form-label">Full Name</label>
                <input type="text" name="name" class="form-control rounded-pill" required />
              </div>
              <div class="mb-3">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-control rounded-pill" required>
                  <option value="" selected disabled>Select Gender</option>
                  <option value="male">Male</option>
                  <option value="female">Female</option>
                  <option value="other">Other</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">Contact No</label>
                <input type="tel" name="contact_no" class="form-control rounded-pill" pattern="\d{10}" placeholder="e.g. 0771234567" required />
              </div>
              <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control rounded-pill" required />
              </div>
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control rounded-pill" required />
              </div>
              <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control rounded-pill" required />
              </div>
              <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control rounded-pill" required />
              </div>
              <div class="d-flex justify-content-end mt-4">
                <button type="reset" class="btn btn-outline-danger me-2 rounded-pill">Clear</button>
                <button type="submit" name="register" class="btn btn-success rounded-pill">Sign Up</button>
              </div>
              <p class="text-center mt-3">Already have an account? <a href="login.php" class="text-decoration-none">Login here</a></p>
            </form>

            <?php
            if (isset($_POST['register'])) {
                $conn = mysqli_connect("localhost", "root", "", "kn_raam_hardware");
                if (!$conn) {
                    die("Connection failed: " . mysqli_connect_error());
                }

                $name = trim($_POST['name']);
                $gender = $_POST['gender'];
                $contact_no = trim($_POST['contact_no']);
                $username = trim($_POST['username']);
                $email = trim($_POST['email']);
                $password = $_POST['password'];
                $confirm_password = $_POST['confirm_password'];

                if ($password !== $confirm_password) {
                    echo '<div class="alert alert-danger mt-3">Passwords do not match.</div>';
                } else {
                    // Check if username or email already exists
                    $stmt_check = $conn->prepare("SELECT user_id FROM users WHERE username = ? OR mail = ?");
                    $stmt_check->bind_param("ss", $username, $email);
                    $stmt_check->execute();
                    $stmt_check->store_result();

                    if ($stmt_check->num_rows > 0) {
                        echo '<div class="alert alert-danger mt-3">Username or email already exists.</div>';
                    } else {
                        // Hash the password securely
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                        // Insert user including gender and contact_no
                        $stmt = $conn->prepare("INSERT INTO users (name, gender, contact_no, username, mail, password, user_type) VALUES (?, ?, ?, ?, ?, ?, 'customer')");
                        $stmt->bind_param("ssssss", $name, $gender, $contact_no, $username, $email, $hashed_password);
                        if ($stmt->execute()) {
                            echo '<div class="alert alert-success mt-3">Registered successfully! Redirecting to login...</div>';
                            echo '<script>setTimeout(() => { window.location = "login.php"; }, 2000);</script>';
                        } else {
                            echo '<div class="alert alert-danger mt-3">Registration failed: ' . htmlspecialchars($stmt->error) . '</div>';
                        }
                        $stmt->close();
                    }
                    $stmt_check->close();
                }

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
