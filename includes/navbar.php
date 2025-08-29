<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Connect to database (only if not already connected)
if (!isset($conn) || !$conn) {
    $conn = mysqli_connect("localhost", "root", "", "kn_raam_hardware");
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }
}

// Get cart count if logged in
$cart_count = 0;
$unread_messages = 0;
if (isset($_SESSION['customer_id'])) {
    $uid = $_SESSION['customer_id'];
    $cart_res = mysqli_query($conn, "SELECT SUM(qty) AS total_qty FROM cart WHERE user_id = $uid");
    $cart_row = mysqli_fetch_assoc($cart_res);
    $cart_count = $cart_row['total_qty'] ?? 0;
    
    // Get unread messages count
    $messages_res = mysqli_query($conn, "SELECT COUNT(*) AS count FROM contact_messages WHERE user_id = $uid AND status = 'Replied' AND seen_by_user = 0");
    $messages_row = mysqli_fetch_assoc($messages_res);
    $unread_messages = $messages_row['count'] ?? 0;
}
?>

<!-- Bootstrap Icons CSS for search icon -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow fixed-top">
  <div class="container">
    <!-- Brand -->
    <a class="navbar-brand fw-bold" href="/hardware/index.php">K.N. Raam Hardware</a>

    <!-- Toggle Button -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Navbar Content -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <!-- Main Navigation Links -->
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>" href="/hardware/index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : '' ?>" href="/hardware/products.php">Products</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : '' ?>" href="/hardware/about.php">About</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : '' ?>" href="/hardware/contact.php">Contact</a>
        </li>
      </ul>

      <!-- Search Bar - Centered -->
      <form class="d-flex mx-auto" method="GET" action="/hardware/search.php" style="max-width: 300px;">
        <input class="form-control me-2" type="search" name="search" placeholder="Search products..." 
               aria-label="Search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        <button class="btn btn-outline-light" type="submit">
          <i class="bi bi-search"></i>
        </button>
      </form>

             <!-- User Menu -->
       <ul class="navbar-nav ms-auto">
         <?php if (isset($_SESSION['customer_id'])): ?>
           <!-- Messages -->
           <li class="nav-item">
             <a class="nav-link" href="/hardware/my_messages.php" title="My Messages">
               <i class="bi bi-envelope"></i>
               <?php if ($unread_messages > 0): ?>
                 <span class="badge bg-warning"><?= $unread_messages ?></span>
               <?php endif; ?>
             </a>
           </li>
           <!-- Cart -->
           <li class="nav-item">
             <a class="nav-link" href="/hardware/cart.php">
               <i class="bi bi-cart3"></i>
               <span class="badge bg-danger"><?= $cart_count ?></span>
             </a>
           </li>
                     <!-- User Dropdown -->
           <li class="nav-item dropdown">
             <a class="nav-link dropdown-toggle user-dropdown" href="#" id="userDropdown" role="button" onclick="toggleUserDropdown(event)" aria-expanded="false">
               <i class="bi bi-person-circle me-1"></i>
               <span class="user-name"><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></span>
             </a>
                           <div class="dropdown-menu dropdown-menu-end shadow" id="userDropdownMenu" style="display: none; position: absolute; top: 100%; right: 0; background: white; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); min-width: 200px; z-index: 1000;">
                <a class="dropdown-item" href="/hardware/profile.php" style="display: block; padding: 12px 16px; color: #333; text-decoration: none; border-bottom: 1px solid #eee;">
                  <i class="bi bi-person me-2"></i>Profile
                </a>
                <a class="dropdown-item" href="/hardware/orders/my_orders.php" style="display: block; padding: 12px 16px; color: #333; text-decoration: none; border-bottom: 1px solid #eee;">
                  <i class="bi bi-box me-2"></i>My Orders
                </a>
                <a class="dropdown-item" href="/hardware/my_messages.php" style="display: block; padding: 12px 16px; color: #333; text-decoration: none; border-bottom: 1px solid #eee;">
                  <i class="bi bi-envelope me-2"></i>My Messages
                  <?php if ($unread_messages > 0): ?>
                    <span class="badge bg-warning ms-2"><?= $unread_messages ?></span>
                  <?php endif; ?>
                </a>
                <a class="dropdown-item text-danger" href="/hardware/logout.php" style="display: block; padding: 12px 16px; color: #dc3545; text-decoration: none;">
                  <i class="bi bi-box-arrow-right me-2"></i>Logout
                </a>
              </div>
           </li>
          
          <script>
          function toggleUserDropdown(event) {
              event.preventDefault();
              event.stopPropagation();
              
              const dropdownMenu = document.getElementById('userDropdownMenu');
              const dropdownToggle = document.getElementById('userDropdown');
              
              console.log('Dropdown toggle clicked');
              
              if (dropdownMenu && dropdownToggle) {
                  const isVisible = dropdownMenu.style.display === 'block';
                  
                  if (isVisible) {
                      // Hide dropdown
                      dropdownMenu.style.display = 'none';
                      dropdownToggle.setAttribute('aria-expanded', 'false');
                      console.log('Dropdown hidden');
                  } else {
                      // Show dropdown
                      dropdownMenu.style.display = 'block';
                      dropdownToggle.setAttribute('aria-expanded', 'true');
                      console.log('Dropdown shown');
                      
                      // Log all dropdown items
                      const dropdownItems = dropdownMenu.querySelectorAll('.dropdown-item');
                      console.log('Dropdown items found:', dropdownItems.length);
                      dropdownItems.forEach((item, index) => {
                          console.log(`Item ${index + 1}:`, item.textContent.trim());
                      });
                  }
              }
          }
          
          // Close dropdown when clicking outside
          document.addEventListener('click', function(event) {
              const dropdownMenu = document.getElementById('userDropdownMenu');
              const dropdownToggle = document.getElementById('userDropdown');
              
              if (dropdownMenu && !dropdownToggle.contains(event.target) && !dropdownMenu.contains(event.target)) {
                  dropdownMenu.style.display = 'none';
                  dropdownToggle.setAttribute('aria-expanded', 'false');
              }
          });
          
          // Ensure dropdown works on page load
          document.addEventListener('DOMContentLoaded', function() {
              const dropdownMenu = document.getElementById('userDropdownMenu');
              const dropdownToggle = document.getElementById('userDropdown');
              
              if (dropdownMenu && dropdownToggle) {
                  console.log('Dropdown elements found and ready');
              }
          });
          </script>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link" href="/hardware/login.php">
              <i class="bi bi-box-arrow-in-right me-1"></i>Login
            </a>
          </li>
          <li class="nav-item">
            <a class="btn btn-outline-light btn-sm" href="/hardware/register.php">
              <i class="bi bi-person-plus me-1"></i>Sign Up
            </a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
