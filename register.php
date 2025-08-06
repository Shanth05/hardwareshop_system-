<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register | K.N. Raam Hardware</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<br><br>
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-8 bg-light p-5 shadow">
      <h2 class="text-center mb-4">Register</h2>
      <form method="POST">
        <div class="row g-3">
          <div class="col-md-12">
            <label>Name *</label>
            <input type="text" name="name" class="form-control" required>
          </div>

          <div class="col-md-6">
            <label>Email *</label>
            <input type="email" name="mail" class="form-control" required>
          </div>

          <div class="col-md-6">
            <label>Contact No *</label>
            <input type="text" name="contact_no" class="form-control" required>
          </div>

          <div class="col-md-12">
            <label>Gender *</label><br>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="gender" value="Male" required>
              <label class="form-check-label">Male</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="gender" value="Female" required>
              <label class="form-check-label">Female</label>
            </div>
          </div>

          <div class="col-md-6">
            <label>Username *</label>
            <input type="text" name="username" class="form-control" required>
          </div>

          <div class="col-md-6">
            <label>Password *</label>
            <input type="password" name="password1" class="form-control" required>
          </div>

          <div class="col-md-6">
            <label>Confirm Password *</label>
            <input type="password" name="password2" class="form-control" required>
          </div>

          <input type="hidden" name="user_type" value="customer">

          <div class="col-12">
            <p>If already registered, <a href="login.php">click here</a> to login.</p>
          </div>

          <div class="col-12 text-end">
            <input type="reset" value="Clear" class="btn btn-danger me-2">
            <input type="submit" name="register" value="Register" class="btn btn-primary">
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>

<?php
if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $mail = $_POST['mail'];
    $contact_no = $_POST['contact_no'];
    $gender = $_POST['gender'];
    $username = $_POST['username'];
    $user_type = $_POST['user_type'];

    // Password validation
    if ($_POST['password1'] !== $_POST['password2']) {
        echo '<script>alert("Passwords do not match!"); window.location.href="register.php";</script>';
        exit;
    }

    $password = password_hash($_POST['password1'], PASSWORD_DEFAULT);

    // DB connection
    $conn = mysqli_connect('localhost', 'root', '', 'kn_raam_hardware');
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Check if username or email already exists
    $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' OR mail='$mail'");
    if (mysqli_num_rows($check) > 0) {
        echo '<script>alert("Username or Email already exists!"); window.location.href="register.php";</script>';
        exit;
    }

    // Insert user
    $sql = "INSERT INTO users (name, mail, contact_no, gender, username, password, user_type) 
            VALUES ('$name', '$mail', '$contact_no', '$gender', '$username', '$password', '$user_type')";

    if (mysqli_query($conn, $sql)) {
        // Optional: send mail to user and admin
        mail($mail, "Welcome to K.N. Raam Hardware", "Hi $name,\n\nYou have successfully registered.", "From: noreply@knraam.lk");
        mail("admin@knraam.lk", "New Registration", "User '$name' just registered.", "From: noreply@knraam.lk");

        echo '<script>alert("Registration successful!"); window.location.href="login.php";</script>';
    } else {
        echo '<script>alert("Registration failed!"); window.location.href="register.php";</script>';
    }
}
?>
