<?php
declare(strict_types=1);

include 'components/connect.php';

session_start();

$user_id = $_SESSION['user_id'] ?? '';

if ($user_id) {
   $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
   $select_profile->execute([$user_id]);
   $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
} else {
   $fetch_profile = [];
}

if(isset($_POST['submit'])){

   $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
   $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

   $update_profile = $conn->prepare("UPDATE `users` SET name = ?, email = ? WHERE id = ?");
   $update_profile->execute([$name, $email, $user_id]);

   $empty_pass = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';
   $prev_pass = $_POST['prev_pass'] ?? '';
   $old_pass = sha1($_POST['old_pass'] ?? '');
   $new_pass = sha1($_POST['new_pass'] ?? '');
   $cpass = sha1($_POST['cpass'] ?? '');

   if($old_pass === $empty_pass){
      $message[] = 'please enter old password!';
   }elseif($old_pass !== $prev_pass){
      $message[] = 'old password not matched!';
   }elseif($new_pass !== $cpass){
      $message[] = 'confirm password not matched!';
   }else{
      if($new_pass !== $empty_pass){
         $update_admin_pass = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
         $update_admin_pass->execute([$cpass, $user_id]);
         $message[] = 'password updated successfully!';
      }else{
         $message[] = 'please enter a new password!';
      }
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Update Profile</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="form-container">

   <form action="" method="post">
      <h3>Update now</h3>
      <input type="hidden" name="prev_pass" value="<?= htmlspecialchars($fetch_profile["password"] ?? ''); ?>">
      <input type="text" name="name" required placeholder="enter your username" maxlength="20" class="box" value="<?= htmlspecialchars($fetch_profile["name"] ?? ''); ?>">
      <input type="email" name="email" required placeholder="enter your email" maxlength="50" class="box" oninput="this.value = this.value.replace(/\s/g, '')" value="<?= htmlspecialchars($fetch_profile["email"] ?? ''); ?>">
      <input type="password" name="old_pass" placeholder="enter your old password" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="new_pass" placeholder="enter your new password" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="cpass" placeholder="confirm your new password" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="update now" class="btn" name="submit">
   </form>

</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>