<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>About Us - K.N. Raam Hardware</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>

<!-- Navbar -->
<?php include 'includes/navbar.php'; ?>

<!-- Page Content -->
<div class="my-5 px-4">
    <h2 class="text-center fw-bold h-font">ABOUT US</h2>
    <div class="h-line bg-dark"></div>
</div>

<div class="container">
    <div class="row">
        <div class="col-lg-6 col-md-6 mb-5 px-4">
            <img src="images/about.jpg" class="w-100" alt="K.N. Raam Hardware Store">
        </div>

        <div class="col-lg-6 col-md-6 mb-5 px-4">
            <p>
                K.N. Raam Hardware is your trusted destination for premium construction materials, tools, fittings, and all your hardware essentials. We’ve proudly served our community for years, offering dependable products and personalized service that our customers value.
            </p>
            <p>
                Whether you're building, renovating, or repairing, we offer a full range of items — from electricals, plumbing, and sanitary fittings to power tools, paints, and cement — all sourced from top brands at competitive prices.
            </p>
        </div>

        <div class="col-lg-12 col-md-6 mb-5 px-4">
            At K.N. Raam Hardware, we put people first. Our team is committed to helping you find exactly what you need, offering expert advice and fast service. Shop confidently in-store or online, and experience quality and trust that’s built to last.
        </div>
    </div>
</div>

<!-- Footer -->
<?php include 'includes/footer.php'; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
