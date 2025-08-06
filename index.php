<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>K.N. Raam Hardware</title>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white px-lg-3 py-lg-2 shadow-sm sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand me-5 fw-bold fs-3 h-font" href="index.php">K.N. Raam Hardware</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarScroll">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link me-2" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link me-2" href="products.php">Products</a></li>
        <li class="nav-item"><a class="nav-link me-2" href="brands.php">Brands</a></li>
        <li class="nav-item"><a class="nav-link me-2" href="contactUs.php">Contact</a></li>
        <li class="nav-item"><a class="nav-link me-2" href="about.php">About</a></li>
        <?php if (!isset($_SESSION['customer_id'])): ?>
            <li class="nav-item"><a class="nav-link me-2" href="login.php">Login</a></li>
            <li class="nav-item"><a class="nav-link me-2" href="register.php">Sign Up</a></li>
        <?php else:
            $user_id = $_SESSION['customer_id'];
            $conn = mysqli_connect('localhost','root','','kn_raam_hardware');
            $qry = "SELECT * FROM cart WHERE user_id=$user_id";
            $result = mysqli_query($conn, $qry);
            $count_rows = mysqli_num_rows($result);
            ?>
            <li class="nav-item"><a class="nav-link me-2" href="cart.php">Cart(<?php echo $count_rows;?>)</a></li>
            <li class="nav-item">
              <a class="nav-link me-2" href="my_profile.php">
                <?php
                $sql = "SELECT name FROM users WHERE user_id=$user_id";
                $res = mysqli_query($conn, $sql);
                if ($row = mysqli_fetch_assoc($res)) echo $row['name'];
                ?>
              </a>
            </li>
            <li class="nav-item"><a class="nav-link me-2" href="logout.php">Logout</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- Carousal -->
<div class="container-fluid px-lg-4 mt-4">
  <div class="swiper swiper-container">
    <div class="swiper-wrapper">
      <?php 
      $conn = mysqli_connect("localhost","root","","kn_raam_hardware");
      $res = mysqli_query($conn, "SELECT * FROM ads");
      while ($row = mysqli_fetch_assoc($res)):
      ?>
        <div class="swiper-slide">
          <img src="images/ads/<?php echo $row['image']; ?>" class="w-100 d-block" height="600px" />
        </div>
      <?php endwhile; ?>
    </div>
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
    <div class="swiper-pagination"></div>
  </div>
</div>

<!-- Offers -->
<h2 class="my-5 text-center fw-bold h-font">MEGA OFFERS</h2>
<div class="container px-4">
  <div class="swiper mySwiper">
    <div class="swiper-wrapper mb-5">
      <?php
      $res = mysqli_query($conn, "SELECT * FROM ads");
      while ($row = mysqli_fetch_assoc($res)):
      ?>
        <div class="swiper-slide bg-white text-center overflow-hidden rounded">
          <img src="images/offers/<?php echo $row['image']; ?>" class="img-fluid" alt="Offer">
        </div>
      <?php endwhile; ?>
    </div>
    <div class="swiper-pagination"></div>
  </div>
</div>

<!-- Products -->
<h2 class="mt-5 pt-4 mb-4 text-center fw-bold h-font">OUR PRODUCTS</h2>
<div class="container">
  <div class="row">
    <?php
    $res = mysqli_query($conn, "SELECT * FROM products WHERE status='Available' LIMIT 4");
    while ($row = mysqli_fetch_assoc($res)):
    ?>
    <div class="col-lg-3 my-3">
      <div class="card border-0 shadow" style="max-width:250px; margin:auto">
        <img src="images/products/<?php echo $row['image']; ?>" height="250px" class="card-img-top">
        <div class="card-body">
          <h5><a href="product_details.php?product_id=<?php echo $row['product_id']; ?>"><?php echo $row['product_name']; ?></a></h5>
          <h6 class="mb-4">Price: LKR <?php echo $row['price']; ?></h6>
          <h6 class="mb-1">Category</h6>
          <span class="badge bg-secondary"> <?php echo $row['category']; ?> </span>
          <?php if (isset($_SESSION['customer_id'])): ?>
          <div class="d-flex justify-content-evenly mt-3">
            <form action="products.php" method="POST">
              <input type="number" name="qty" value="1" required class="form-control mb-2">
              <input type="hidden" name="product_name" value="<?php echo $row['product_name']; ?>">
              <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
              <input type="hidden" name="price" value="<?php echo $row['price']; ?>">
              <input type="submit" value="Add to Cart" name="add_to_cart" class="btn btn-sm btn-outline-dark">
            </form>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endwhile; ?>
    <div class="col-lg-12 text-center mt-5 mb-5">
      <a href="products.php" class="btn btn-sm btn-outline-dark rounded-0 fw-bold shadow-none">Find More >>></a>
    </div>
  </div>
</div>

<?php include('includes/footer.php'); ?>
