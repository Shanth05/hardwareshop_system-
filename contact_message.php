<?php
session_start();
include('includes/db.php'); // your DB connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $name    = mysqli_real_escape_string($conn, $_POST['name']);
    $email   = mysqli_real_escape_string($conn, $_POST['email']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    // If user logged in, get user_id; else NULL
    $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 'NULL';

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
