<?php
if (isset($_GET['cart_id'])) {
    // Get cart ID from URL and cast to integer for safety
    $cart_id = (int) $_GET['cart_id'];

    // Connect to your hardware shop database
    $conn = mysqli_connect("localhost", "root", "", "kn_raam_hardware");

    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    // Delete the item from the cart
    $sqlquery = "DELETE FROM cart WHERE cart_id = $cart_id";
    $res = mysqli_query($conn, $sqlquery);

    if ($res) {
        echo '<script>
            alert("Product deleted successfully!");
            window.location.href = "cart.php";
        </script>';
    } else {
        echo '<script>
            alert("Failed to delete product!");
            window.location.href = "cart.php";
        </script>';
    }

    mysqli_close($conn);
} else {
    echo "Delete unsuccessful: No cart ID provided.";
}
?>
