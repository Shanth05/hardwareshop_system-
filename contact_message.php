<?php
session_start();
include('includes/db.php'); // DB connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ✅ Ensure user is logged in
    if (!isset($_SESSION['customer_id'])) {
        $_SESSION['error'] = "You must be logged in to send a message.";
        header("Location: contact.php");
        exit;
    }

    // Get logged-in user ID
    $user_id = intval($_SESSION['customer_id']);

    // Sanitize inputs
    $name    = mysqli_real_escape_string($conn, $_POST['name']);
    $email   = mysqli_real_escape_string($conn, $_POST['email']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    // Insert into database
    $sql = "INSERT INTO contact_messages (user_id, name, email, message, status, created_at)
            VALUES ($user_id, '$name', '$email', '$message', 'Pending', NOW())";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['success'] = "Your message has been sent successfully!";
    } else {
        $_SESSION['error'] = "Error: " . mysqli_error($conn);
    }

    // Redirect back to contact page
    header("Location: contact.php");
    exit;
}
?>
