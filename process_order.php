<?php
session_start();

if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['customer_id'];

$conn = mysqli_connect("localhost", "root", "", "kn_raam_hardware");
if (!$conn) {
    error_log("Database connection failed: " . mysqli_connect_error());
    die("An error occurred. Please try again later.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request method.");
}

// Process form data
$name = filter_var($_POST['name'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
$mail = filter_var($_POST['mail'] ?? '', FILTER_SANITIZE_EMAIL);
$contact_no = filter_var($_POST['contact_no'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
$address = filter_var($_POST['address'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
$payment_method = filter_var($_POST['payment_method'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

// Validate form data
if (empty($name) || empty($mail) || empty($contact_no) || empty($address) || empty($payment_method)) {
    error_log("Missing required fields: name=$name, mail=$mail, contact_no=$contact_no, address=$address, payment_method=$payment_method");
    die("All fields are required.");
}
if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
    error_log("Invalid email format: $mail");
    die("Invalid email format.");
}
if (!preg_match('/^\d{10}$/', $contact_no)) {
    error_log("Invalid contact number: $contact_no");
    die("Contact number must be 10 digits.");
}

// Card details (only if payment_method is 'online')
$card_name = $card_type = $card_number = $expiry_date = $cvv = null;
if ($payment_method === 'online') {
    $card_name = filter_var($_POST['card_name'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
    $card_type = filter_var($_POST['card_type'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
    $card_number = filter_var($_POST['card_number'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
    $expiry_date = filter_var($_POST['expiry_date'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
    $cvv = filter_var($_POST['cvv'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

    if (empty($card_name) || empty($card_type) || empty($card_number) || empty($expiry_date) || empty($cvv)) {
        error_log("Missing card details: card_name=$card_name, card_type=$card_type, card_number=$card_number, expiry_date=$expiry_date, cvv=$cvv");
        die("All card details are required.");
    }
    if (!preg_match('/^\d{4}\s?\d{4}\s?\d{4}\s?\d{4}$/', $card_number)) {
        error_log("Invalid card number: $card_number");
        die("Invalid card number.");
    }
    if (!preg_match('/^(0[1-9]|1[0-2])\/[0-9]{2}$/', $expiry_date)) {
        error_log("Invalid expiry date: $expiry_date");
        die("Invalid expiry date.");
    }
    if (!preg_match('/^\d{3,4}$/', $cvv)) {
        error_log("Invalid CVV: $cvv");
        die("Invalid CVV.");
    }
    // Note: Card details should be sent to a payment gateway (e.g., Stripe) here, not stored.
}

// Fetch cart items
$cart_sql = "
    SELECT c.qty, c.product_id, p.product_name, p.price
    FROM cart c
    INNER JOIN products p ON c.product_id = p.product_id
    WHERE c.user_id = ?
";
$stmt = mysqli_prepare($conn, $cart_sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$cart_res = mysqli_stmt_get_result($stmt);

$total_price = 0;
$total_items = 0;
$cart_items = [];
if ($cart_res && mysqli_num_rows($cart_res) > 0) {
    while ($row = mysqli_fetch_assoc($cart_res)) {
        $line_total = $row['price'] * $row['qty'];
        $total_price += $line_total;
        $total_items += $row['qty'];
        $cart_items[] = $row + ['line_total' => $line_total];
    }
} else {
    error_log("Cart is empty for user_id: $user_id");
    die("Your cart is empty.");
}

// Save order to database
$order_status = 'Ordered';
$order_sql = "INSERT INTO orders (user_id, name, mail, contact_no, address, total_items, total_price, payment_method, order_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $order_sql);
if (!$stmt) {
    error_log("Failed to prepare order insert: " . mysqli_error($conn));
    die("An error occurred while saving the order.");
}
mysqli_stmt_bind_param($stmt, "issssids", $user_id, $name, $mail, $contact_no, $address, $total_items, $total_price, $payment_method, $order_status);
if (!mysqli_stmt_execute($stmt)) {
    error_log("Failed to execute order insert: " . mysqli_stmt_error($stmt));
    die("An error occurred while saving the order.");
}
$order_id = mysqli_insert_id($conn);

// Save order items
$order_item_sql = "INSERT INTO order_items (order_id, product_id, qty, price) VALUES (?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $order_item_sql);
if (!$stmt) {
    error_log("Failed to prepare order items insert: " . mysqli_error($conn));
    die("An error occurred while saving order items.");
}
foreach ($cart_items as $item) {
    mysqli_stmt_bind_param($stmt, "iiid", $order_id, $item['product_id'], $item['qty'], $item['price']);
    if (!mysqli_stmt_execute($stmt)) {
        error_log("Failed to execute order items insert: " . mysqli_stmt_error($stmt));
        die("An error occurred while saving order items.");
    }
}

// Clear cart
$clear_cart_sql = "DELETE FROM cart WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $clear_cart_sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

// Generate LaTeX bill
$latex_content = "
\\documentclass[a4paper,12pt]{article}
\\usepackage[utf8]{inputenc}
\\usepackage{geometry}
\\geometry{margin=1in}
\\usepackage{booktabs}
\\usepackage{xcolor}
\\usepackage{colortbl}
\\usepackage{parskip}
\\usepackage{dejavu}
\\definecolor{brandblue}{HTML}{0D6EFD}
\\definecolor{brandorange}{HTML}{FF8800}

\\begin{document}
\\sffamily
\\begin{center}
    {\\LARGE \\textbf{K.N. Raam Hardware -- Invoice}} \\\\[0.5cm]
    {\\large Order ID: $order_id} \\\\
    {\\normalsize Order Date: " . date('F j, Y') . "}
\\end{center}

\\vspace{0.5cm}
\\noindent
\\textbf{Billing Details} \\\\
Name: " . str_replace("&", "\\&", htmlspecialchars($name)) . " \\\\
Email: " . str_replace("&", "\\&", htmlspecialchars($mail)) . " \\\\
Contact No: " . str_replace("&", "\\&", htmlspecialchars($contact_no)) . " \\\\
Address: " . str_replace("&", "\\&", htmlspecialchars($address)) . " \\\\
Payment Method: " . ($payment_method === 'online' ? 'Pay Online (Card)' : 'Cash on Delivery') . " \\\\
" . ($payment_method === 'online' ? "
Card Type: " . str_replace("&", "\\&", htmlspecialchars($card_type ?? '')) . " \\\\
Card Number: **** **** **** " . substr(str_replace(" ", "", $card_number ?? ''), -4) . " \\\\
" : "") . "

\\vspace{0.5cm}
\\noindent
\\textbf{Order Summary} \\\\
\\begin{tabular}{llrr}
    \\toprule
    \\rowcolor{brandblue!10} \\textbf{Product} & \\textbf{Qty} & \\textbf{Unit Price (LKR)} & \\textbf{Total (LKR)} \\\\
    \\midrule
";

foreach ($cart_items as $item) {
    $product_name = str_replace("&", "\\&", htmlspecialchars($item['product_name']));
    $latex_content .= "    $product_name & {$item['qty']} & " . number_format($item['price'], 2) . " & " . number_format($item['line_total'], 2) . " \\\\ \n";
}

$latex_content .= "
    \\bottomrule
    \\rowcolor{brandorange!10} \\textbf{Grand Total} & \\textbf{$total_items} & & \\textbf{" . number_format($total_price, 2) . "} \\\\
    \\bottomrule
\\end{tabular}

\\vspace{1cm}
\\noindent
{\\footnotesize Thank you for shopping with K.N. Raam Hardware! For inquiries, contact us at support@knraamhardware.com.}
\\end{document}
";

// Save LaTeX file
$latex_file = "bill_$order_id.tex";
if (!file_put_contents($latex_file, $latex_content)) {
    error_log("Failed to write LaTeX file: bill_$order_id.tex");
    die("An error occurred while generating the bill.");
}

// Compile LaTeX to PDF
exec("latexmk -pdf " . escapeshellarg($latex_file) . " 2>&1", $output, $return_var);
if ($return_var !== 0) {
    error_log("LaTeX compilation failed: " . implode("\n", $output));
    die("Failed to generate PDF bill. Please contact support.");
}

// Serve PDF for download
$pdf_file = "bill_$order_id.pdf";
if (file_exists($pdf_file)) {
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="invoice_' . $order_id . '.pdf"');
    readfile($pdf_file);
    
    // Clean up files
    unlink($latex_file);
    unlink($pdf_file);
    foreach (glob("bill_$order_id.*") as $temp_file) {
        unlink($temp_file);
    }
} else {
    error_log("PDF file not found: $pdf_file");
    die("PDF file not found. Please contact support.");
}

// Redirect to success page
header("Location: order_success.php?order_id=" . $order_id);
exit();
?>