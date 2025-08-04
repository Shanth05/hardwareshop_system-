<?php
declare(strict_types=1);

include 'components/connect.php';

session_start();

$user_id = $_SESSION['user_id'] ?? '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Orders</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="orders">

   <h1 class="heading">Placed Orders.</h1>

   <div class="box-container">

   <?php
      if($user_id === ''){
         echo '<p class="empty">please login to see your orders</p>';
      }else{
         $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ?");
         $select_orders->execute([$user_id]);
         if($select_orders->rowCount() > 0){
            while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
   ?>
   <div class="box">
      <p>Placed on : <span><?= htmlspecialchars($fetch_orders['placed_on']); ?></span></p>
      <p>Name : <span><?= htmlspecialchars($fetch_orders['name']); ?></span></p>
      <p>Email : <span><?= htmlspecialchars($fetch_orders['email']); ?></span></p>
      <p>Phone Number : <span><?= htmlspecialchars($fetch_orders['number']); ?></span></p>
      <p>Address : <span><?= htmlspecialchars($fetch_orders['address']); ?></span></p>
      <p>Payment Method : <span><?= htmlspecialchars($fetch_orders['method']); ?></span></p>
      <p>Your orders : <span><?= htmlspecialchars($fetch_orders['total_products']); ?></span></p>
      <p>Total price : <span>Nrs.<?= htmlspecialchars($fetch_orders['total_price']); ?>/-</span></p>
      <p> Payment status : <span style="color:<?= $fetch_orders['payment_status'] === 'pending' ? 'red' : 'green'; ?>">
         <?= htmlspecialchars($fetch_orders['payment_status']); ?></span> </p>
   </div>
   <?php
      }
      }else{
         echo '<p class="empty">no orders placed yet!</p>';
      }
      }
   ?>

   </div>

</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>