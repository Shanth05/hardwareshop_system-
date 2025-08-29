<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About Us - K.N. Raam Hardware</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>
<body>

<?php include('includes/navbar.php'); ?>

<div class="container mt-5 pt-4">
    <h2 class="text-center fw-bold h-font about-title">ABOUT US</h2>
    <div class="h-line about-divider"></div>

    <div class="row align-items-center">
        <div class="col-lg-6 col-md-6 mb-4">
            <img src="images/about.jpg" alt="K.N. Raam Hardware Store" class="about-img" />
        </div>

        <div class="col-lg-6 col-md-6 about-text">
            <p>
                K.N. Raam Hardware is your trusted destination for premium construction materials, tools, fittings, and all your hardware essentials. We’ve proudly served our community for years, offering dependable products and personalized service that our customers value.
            </p>
            <p>
                Whether you're building, renovating, or repairing, we offer a full range of items — from electricals, plumbing, and sanitary fittings to power tools, paints, and cement — all sourced from top brands at competitive prices.
            </p>
        </div>
    </div>

    <p class="about-highlight">
        At K.N. Raam Hardware, we put people first. Our team is committed to helping you find exactly what you need, offering expert advice and fast service. Shop confidently in-store or online, and experience quality and trust that’s built to last.
    </p>
</div>

<?php include('includes/footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/script.js"></script>
</body>
</html>
