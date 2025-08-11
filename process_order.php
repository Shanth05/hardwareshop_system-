<?php
session_start();

if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['customer_id'];

$conn = mysqli_connect("localhost", "root", "", "kn_raam_hardware");
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Helper function to sanitize input
function sanitize($conn, $data) {
    return mysqli_real_escape_string($conn, trim($data));
}

// Validate POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($conn, $_POST['name'] ?? '');
    $mail = sanitize($conn, $_POST['mail'] ?? '');
    $contact_no = sanitize($conn, $_POST['contact_no'] ?? '');
    $address = sanitize($conn, $_POST['address'] ?? '');
    $payment_method = sanitize($conn, $_POST['payment_method'] ?? '');

    // Simple server-side validation (expand as needed)
    if (empty($name) || empty($mail) || empty($contact_no) || empty($address) || empty($payment_method)) {
        die("Please fill in all required fields.");
    }

    // If payment method is online, validate card details
    if ($payment_method === 'online') {
        $card_name = sanitize($conn, $_POST['card_name'] ?? '');
        $card_type = sanitize($conn, $_POST['card_type'] ?? '');
        $card_number = sanitize($conn, str_replace(' ', '', $_POST['card_number'] ?? ''));
        $expiry_date = sanitize($conn, $_POST['expiry_date'] ?? '');
        $cvv = sanitize($conn, $_POST['cvv'] ?? '');

        if (empty($card_name) || empty($card_type) || empty($card_number) || empty($expiry_date) || empty($cvv)) {
            die("Please fill in all card details.");
        }

        if (!preg_match('/^\d{16}$/', $card_number)) {
            die("Invalid card number.");
        }
        if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $expiry_date)) {
            die("Invalid expiry date.");
        }
        if (!preg_match('/^\d{3,4}$/', $cvv)) {
            die("Invalid CVV.");
        }
    } else {
        // For cash on delivery, clear card details variables
        $card_name = $card_type = $card_number = $expiry_date = $cvv = null;
    }

    // Fetch cart items for this user
    $cart_sql = "
        SELECT c.qty, p.product_id, p.price
        FROM cart c
        INNER JOIN products p ON c.product_id = p.product_id
        WHERE c.user_id = ?
    ";
    $stmt = mysqli_prepare($conn, $cart_sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $cart_res = mysqli_stmt_get_result($stmt);

    if (!$cart_res || mysqli_num_rows($cart_res) === 0) {
        die("Your cart is empty.");
    }

    // Calculate total price
    $total_price = 0;
    $order_items = [];
    while ($row = mysqli_fetch_assoc($cart_res)) {
        $line_total = $row['price'] * $row['qty'];
        $total_price += $line_total;
        $order_items[] = [
            'product_id' => $row['product_id'],
            'qty' => $row['qty'],
            'price' => $row['price'],
            'line_total' => $line_total,
        ];
    }

    // Insert order into orders table
    $order_sql = "INSERT INTO orders (user_id, name, mail, contact_no, address, payment_method, total_price, order_date) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = mysqli_prepare($conn, $order_sql);
    mysqli_stmt_bind_param($stmt, "isssssd", $user_id, $name, $mail, $contact_no, $address, $payment_method, $total_price);
    $exec = mysqli_stmt_execute($stmt);

    if (!$exec) {
        die("Failed to create order: " . mysqli_error($conn));
    }

    $order_id = mysqli_insert_id($conn);

    // Insert order items into order_items table
    $item_sql = "INSERT INTO order_items (order_id, product_id, qty, price) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $item_sql);

    foreach ($order_items as $item) {
        mysqli_stmt_bind_param($stmt, "iiid", $order_id, $item['product_id'], $item['qty'], $item['price']);
        mysqli_stmt_execute($stmt);
    }

    // Optional: store payment details if needed
    if ($payment_method === 'online') {
        $payment_sql = "INSERT INTO payments (order_id, card_name, card_type, card_number_masked, expiry_date, cvv_masked, payment_date) VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmt = mysqli_prepare($conn, $payment_sql);

        // Mask sensitive info before storing
        $card_number_masked = str_repeat('*', 12) . substr($card_number, -4);
        $cvv_masked = str_repeat('*', strlen($cvv));

        mysqli_stmt_bind_param($stmt, "isssss", $order_id, $card_name, $card_type, $card_number_masked, $expiry_date, $cvv_masked);
        mysqli_stmt_execute($stmt);
    }

    // Clear cart after order
    $delete_cart_sql = "DELETE FROM cart WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $delete_cart_sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    // Redirect to thank you / order confirmation page
    header("Location: order_success.php?order_id=" . $order_id);
    exit();

} else {
    die("Invalid request method.");
}
