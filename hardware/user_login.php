<?php
declare(strict_types=1);

include 'components/connect.php';

session_start();

$user_id = $_SESSION['user_id'] ?? '';

if(isset($_POST['submit'])){

   $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
   $pass = filter_input(INPUT_POST, 'pass', FILTER_SANITIZE_STRING);
   $pass_hashed = sha1($pass);

   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ?");
   $select_user->execute([$email, $pass_hashed]);
   $row = $select_user->fetch(PDO::FETCH_ASSOC);

   if($row){
      $_SESSION['user_id'] = $row['id'];
      header('location:home.php');
      exit;
   }else{
      $message[] = 'incorrect username or password!';
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
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="form-container">

   <form action="" method="post">
      <h3>Login Now</h3>
      <input type="email" name="email" required placeholder="enter your email" maxlength="50"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="pass" required placeholder="enter your password" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="login now" class="btn" name="submit">
      <p>Don't have an account?</p>
      <a href="user_register.php" class="option-btn">Register Now.</a>
   </form>

</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>