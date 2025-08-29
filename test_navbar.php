<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Navbar Test - K.N. Raam Hardware</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
</head>
<body>

<?php include('includes/navbar.php'); ?>

<div class="container mt-5 pt-4">
    <div class="row">
        <div class="col-12">
            <h1 class="text-center mb-4">Navbar Test Page</h1>
            
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Navbar Features Test</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <strong>Fixed Position:</strong> Navbar should stay at top when scrolling
                        </li>
                        <li class="list-group-item">
                            <strong>Search Bar:</strong> Should be visible and functional
                        </li>
                        <li class="list-group-item">
                            <strong>User Menu:</strong> If logged in, should show dropdown with Profile, Orders, Logout
                        </li>
                        <li class="list-group-item">
                            <strong>Cart Icon:</strong> Should show cart count badge
                        </li>
                        <li class="list-group-item">
                            <strong>Responsive:</strong> Should collapse on mobile devices
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="mt-4">
                <h5>Test Instructions:</h5>
                <ol>
                    <li>Check if navbar is fixed at top</li>
                    <li>Try the search functionality</li>
                    <li>If logged in, click on your username to see dropdown</li>
                    <li>Test responsive behavior on mobile/tablet</li>
                    <li>Navigate to other pages to ensure consistency</li>
                </ol>
            </div>
            
            <div class="mt-4">
                <h5>Current Session Info:</h5>
                <p><strong>Logged In:</strong> <?= isset($_SESSION['customer_id']) ? 'Yes' : 'No' ?></p>
                <?php if (isset($_SESSION['username'])): ?>
                    <p><strong>Username:</strong> <?= htmlspecialchars($_SESSION['username']) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/script.js"></script>
</body>
</html>

