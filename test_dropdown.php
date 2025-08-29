<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dropdown Test - K.N. Raam Hardware</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include('includes/navbar.php'); ?>

<div class="container mt-5 pt-4">
    <h2>Dropdown Test Page</h2>
    <p>This page is to test the user dropdown functionality.</p>
    
    <div class="alert alert-info">
        <h5>Instructions:</h5>
        <ol>
            <li>Click on your username in the top-right corner</li>
            <li>Check if all three items are visible: Profile, My Orders, Logout</li>
            <li>Open browser console (F12) to see debug messages</li>
        </ol>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h5>Expected Dropdown Items:</h5>
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><i class="bi bi-person me-2"></i>Profile</li>
                <li class="list-group-item"><i class="bi bi-box me-2"></i>My Orders</li>
                <li class="list-group-item"><i class="bi bi-box-arrow-right me-2"></i>Logout</li>
            </ul>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/script.js"></script>
</body>
</html>
