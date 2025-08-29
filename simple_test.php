<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Simple Dropdown Test</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        body { padding-top: 80px; }
        .test-dropdown {
            position: relative;
            display: inline-block;
        }
        .test-dropdown-content {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 200px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .test-dropdown-content a {
            color: #333;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            border-bottom: 1px solid #eee;
        }
        .test-dropdown-content a:hover {
            background-color: #f1f1f1;
        }
        .test-dropdown-content a:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>

<?php include('includes/navbar.php'); ?>

<div class="container">
    <h2>Simple Dropdown Test</h2>
    
    <?php if (isset($_SESSION['customer_id'])): ?>
        <div class="alert alert-success">
            <strong>✅ Logged In:</strong> You are logged in as <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?>
        </div>
        
        <div class="card">
            <div class="card-body">
                <h5>Test Instructions:</h5>
                <ol>
                    <li>Click on your username in the navbar (top-right corner)</li>
                    <li>You should see a dropdown with: Profile, My Orders, Logout</li>
                    <li>Click outside to close the dropdown</li>
                </ol>
                
                <h5>Expected Dropdown Items:</h5>
                <ul>
                    <li><i class="bi bi-person"></i> Profile</li>
                    <li><i class="bi bi-box"></i> My Orders</li>
                    <li><i class="bi bi-box-arrow-right"></i> Logout</li>
                </ul>
            </div>
        </div>
        
        <!-- Simple test dropdown -->
        <div class="mt-4">
            <h5>Alternative Test Dropdown:</h5>
            <div class="test-dropdown">
                <button class="btn btn-primary" onclick="toggleTestDropdown()">
                    <i class="bi bi-person-circle me-1"></i>Test Dropdown
                </button>
                <div id="testDropdownContent" class="test-dropdown-content">
                    <a href="/hardware/profile.php"><i class="bi bi-person me-2"></i>Profile</a>
                    <a href="/hardware/orders/my_orders.php"><i class="bi bi-box me-2"></i>My Orders</a>
                    <a href="/hardware/logout.php" style="color: #dc3545;"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
                </div>
            </div>
        </div>
        
    <?php else: ?>
        <div class="alert alert-warning">
            <strong>⚠️ Not Logged In:</strong> Please log in to test the dropdown
        </div>
        <a href="login.php" class="btn btn-primary">Go to Login</a>
    <?php endif; ?>
</div>

<script>
function toggleTestDropdown() {
    const content = document.getElementById('testDropdownContent');
    if (content.style.display === 'block') {
        content.style.display = 'none';
    } else {
        content.style.display = 'block';
    }
}

// Close test dropdown when clicking outside
window.onclick = function(event) {
    const content = document.getElementById('testDropdownContent');
    if (!event.target.matches('.btn')) {
        if (content.style.display === 'block') {
            content.style.display = 'none';
        }
    }
}
</script>

</body>
</html>

