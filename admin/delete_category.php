<?php
include('login_check.php');

$conn = mysqli_connect("localhost", "root", "", "kn_raam_hardware");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$category_id = intval($_GET['id'] ?? 0);
if ($category_id <= 0) {
    header("Location: categories.php");
    exit();
}

// Delete category
$stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
$stmt->bind_param("i", $category_id);

if ($stmt->execute()) {
    // Redirect to categories.php with success message
    header("Location: categories.php?msg=deleted");
    exit();
} else {
    // Redirect with error message (optional)
    header("Location: categories.php?msg=delete_error");
    exit();
}

$stmt->close();
mysqli_close($conn);
?>
