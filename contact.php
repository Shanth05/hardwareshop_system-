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
  <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>" />
</head>
<body>

<!-- Navbar -->
<?php include 'includes/navbar.php'; ?>

<!-- Page Heading -->
<div class="my-5 px-3 px-md-5">
  <h2 class="text-center fw-bold h-font about-title">CONTACT US</h2>
  <div class="h-line about-divider"></div>
  <p class="text-center mt-3 contact-intro">
    Have questions? Need assistance? We’re here to help.<br />
    Get in touch with us using the form below or reach us via phone or email.
  </p>
</div>

<div class="container px-3 px-md-5">

  <!-- Success / Error Messages -->
  <?php if (isset($_SESSION['success'])): ?>
      <div id="successAlert" class="alert alert-success">
          <?= $_SESSION['success']; ?>
      </div>
      <script>
          setTimeout(function() {
              var alertBox = document.getElementById('successAlert');
              if (alertBox) { alertBox.style.display = 'none'; }
          }, 3000);
      </script>
      <?php unset($_SESSION['success']); ?>
  <?php endif; ?>

  <?php if (isset($_SESSION['error'])): ?>
      <div id="errorAlert" class="alert alert-danger">
          <?= $_SESSION['error']; ?>
      </div>
      <script>
          setTimeout(function() {
              var alertBox = document.getElementById('errorAlert');
              if (alertBox) { alertBox.style.display = 'none'; }
          }, 3000);
      </script>
      <?php unset($_SESSION['error']); ?>
  <?php endif; ?>

  <div class="row align-equal gy-4">

  <!-- Contact Form -->
  <div class="col-lg-6 col-md-6 d-flex">
    <?php if (isset($_SESSION['customer_id'])): ?>
      <!-- Show form for logged-in users -->
      <form method="post" action="contact_message.php" class="contact-form p-4 border rounded bg-white shadow-sm flex-grow-1 d-flex flex-column">
        <div class="mb-3">
          <label for="name" class="form-label fw-bold">Your Name</label>
          <input type="text" id="name" name="name" class="form-control" placeholder="Enter your full name" required />
        </div>
        <div class="mb-3">
          <label for="email" class="form-label fw-bold">Email Address</label>
          <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email address" required />
        </div>
        <div class="mb-3 flex-grow-1">
          <label for="message" class="form-label fw-bold">Message</label>
          <textarea id="message" name="message" rows="5" class="form-control h-100" placeholder="Type your message here..." required></textarea>
        </div>
        <button type="submit" class="btn btn-primary w-100 mt-auto">Send Message</button>
      </form>
    <?php else: ?>
      <!-- Message for public users -->
      <div class="p-4 border rounded bg-white shadow-sm flex-grow-1 d-flex flex-column justify-content-center text-center">
        <h5 class="fw-bold mb-3">Want to send us a message?</h5>
        <p class="mb-3 text-muted">Please <a href="login.php" class="fw-bold">log in</a> or <a href="register.php" class="fw-bold">create an account</a> to contact us.</p>
        <i class="bi bi-lock-fill text-secondary" style="font-size: 2rem;"></i>
      </div>
    <?php endif; ?>
  </div>

    <!-- Contact Info -->
    <div class="col-lg-6 col-md-6 d-flex">
      <div class="contact-info bg-white p-4 border rounded shadow-sm flex-grow-1 d-flex flex-column">
        <h5 class="fw-bold mb-3">Store Information</h5>
        <p class="mb-2"><i class="bi bi-geo-alt-fill me-2 text-primary"></i> 123 Main Street, Jaffna, Sri Lanka</p>
        <p class="mb-2"><i class="bi bi-telephone-fill me-2 text-primary"></i> +94 77 123 4567</p>
        <p class="mb-4"><i class="bi bi-envelope-fill me-2 text-primary"></i> info@knraamhardware.com</p>

        <h5 class="fw-bold mb-3">Find Us on Map</h5>
        <div class="map-responsive rounded overflow-hidden shadow-sm">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18..."
            width="100%"
            height="250"
            style="border:0;"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            title="K.N. Raam Hardware Location"
          ></iframe>
        </div>
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
