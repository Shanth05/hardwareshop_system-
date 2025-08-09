<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>K.N. Raam Hardware</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  <!-- Custom CSS with Cache Busting -->
  <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>

<!-- Navbar -->
<?php include 'includes/navbar.php'; ?>

<!-- Hero Section -->
<div class="container hero">
  <h1>Welcome to K.N. Raam Hardware</h1>
  <p>Your trusted destination for quality hardware tools and building materials.</p>
  <a href="products.php" class="btn btn-dark">Shop Now</a>
</div>

<!-- Footer -->
<?php include('includes/footer.php'); ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
