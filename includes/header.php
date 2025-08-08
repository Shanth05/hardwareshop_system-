<?php
//session_start();
include('login_check.php');

$user_id = $_SESSION['customer_id'];


$conn = mysqli_connect("localhost","root","","mobile_shop");


$sql ="SELECT * FROM users WHERE user_id=$user_id";

$res = mysqli_query($conn, $sql);

if($res==true)
{

  $count_rows= mysqli_num_rows($res);
  $sn=1;
  //whether there is any data in database or not
  if($count_rows>0)
  {
         while($row=mysqli_fetch_assoc($res))
         {
            $user_id=$row['user_id'];
            $name=$row['name'];
            $mail=$row['mail'];
            $contact_no=$row['contact_no'];
            $gender=$row['gender'];
            $username=$row['username'];
            $user_type=$row['user_type'];
         }
    
      }    
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
  .messages{
    height:400px;
  }
</style>
    <title>Mobile store</title>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white px-lg-3 py-lg-2 shadow-sm sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand me-5 fw-bold fs-3 h-font" href="index.php">Thikal Electronics</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarScroll">
      <ul class="navbar-nav ms-auto my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
        <li class="nav-item">
          <a class="nav-link   me-2" aria-current="page" href="index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link me-2" href="products.php">Our products</a>
        </li>

        <li class="nav-item">
          <a class="nav-link  me-2" href="brands.php">Brands</a>
        </li>

        <li class="nav-item">
          <a class="nav-link me-2"    href="contactUs.php">Contact Us</a>
        </li>

        <li class="nav-item">
          <a class="nav-link me-2" href="about.php">About</a>
        </li>
        <?php
            if(!isset($_SESSION['customer_id'])){
                ?>
                    
                            <li class="nav-item">
                                <a class="nav-link me-2" href="login.php">Login</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link me-2" href="register.php">Sign up</a>
                            </li>
                           
                        
                <?php
            }
            else{
            ?>
                            <li class="nav-item">
                              <?php
                              $conn= mysqli_connect('localhost','root','') or die(mysqli_error());

        
                              //select db
                               $db_select = mysqli_select_db($conn,'mobile_shop') or die(mysqli_error());
                            
                               $qry="SELECT * FROM cart WHERE user_id=$user_id";
                              
                               $result =mysqli_query($conn, $qry);
                               if($result==true){
                                  
                                $count_rows= mysqli_num_rows($result);
                                ?>
                                  <a class="nav-link me-2" href="cart.php">Cart(<?php echo $count_rows;?>)</a>
                                <?php
                               }
                              ?>
                                
                            </li>
                        <li class="nav-item">
                                <a class="nav-link me-2" href="my_profile.php"><?php echo $name;?></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link me-2" href="logout.php">Logout</a>
                            </li>
                           
                      
                <?php
            }

        ?>
      </ul>
      
    </div>
  </div>
</nav>


        
