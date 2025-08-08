<?php
// Authorization and access control

// ✅ Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ Check whether the user is logged in
if (!isset($_SESSION['customer_id'])) {
    echo '<script>
        alert("Please login first before visiting the page!");
        window.location.href = "login.php";
    </script>';
    exit(); // ❗ important: stop further execution
}
?>
