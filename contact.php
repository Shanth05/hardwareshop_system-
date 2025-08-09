<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Contact Us - K.N. Raam Hardware</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />

  <!-- Custom CSS -->
  <link rel="stylesheet" href="includes/css/style.css?v=<?php echo time(); ?>" />
</head>
<body>

<!-- Navbar -->
<?php include 'includes/navbar.php'; ?>

<!-- Page Heading -->
<div class="my-5 px-4">
  <h2 class="text-center fw-bold h-font">CONTACT US</h2>
  <div class="h-line bg-dark"></div>
  <p class="text-center mt-3">
    Have questions? Need assistance? We’re here to help.
    Get in touch with us using the form below or reach us via phone or email.
  </p>
</div>

<div class="container">
  <div class="row align-equal">
    <!-- Contact Form -->
    <div class="col-lg-6 col-md-6 mb-4 px-4 d-flex">
      <form method="post" action="send_message.php" class="p-4 border rounded bg-white shadow-sm flex-grow-1 d-flex flex-column">
        <div class="mb-3">
          <label class="form-label fw-bold">Your Name</label>
          <input type="text" name="name" class="form-control" placeholder="Enter your full name" required />
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold">Email Address</label>
          <input type="email" name="email" class="form-control" placeholder="Enter your email address" required />
        </div>
        <div class="mb-3 flex-grow-1">
          <label class="form-label fw-bold">Message</label>
          <textarea name="message" rows="5" class="form-control h-100" placeholder="Type your message here..." required></textarea>
        </div>
        <button type="submit" class="btn btn-dark w-100 mt-auto">Send Message</button>
      </form>
    </div>

    <!-- Contact Info -->
    <div class="col-lg-6 col-md-6 mb-4 px-4 d-flex">
      <div class="bg-white p-4 border rounded shadow-sm flex-grow-1 d-flex flex-column">
        <h5 class="fw-bold mb-3">Store Information</h5>
        <p class="mb-2"><i class="bi bi-geo-alt-fill"></i> 123 Main Street, Jaffna, Sri Lanka</p>
        <p class="mb-2"><i class="bi bi-telephone-fill"></i> +94 77 123 4567</p>
        <p class="mb-4"><i class="bi bi-envelope-fill"></i> info@knraamhardware.com</p>

        <h5 class="fw-bold mb-3">Find Us on Map</h5>
        <iframe
          src="https://www.google.com/maps/embed?pb=!1m18..."
          width="100%"
          height="250"
          style="border:0;"
          allowfullscreen=""
          loading="lazy"
        ></iframe>
      </div>
    </div>
  </div>
</div>

<!-- Footer -->
<?php include 'includes/footer.php'; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
