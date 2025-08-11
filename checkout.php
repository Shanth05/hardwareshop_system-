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

// Fetch logged-in customer details
$user_sql = "SELECT name, mail, contact_no FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $user_sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user_res = mysqli_stmt_get_result($stmt);
if ($user_res && mysqli_num_rows($user_res) > 0) {
    $user = mysqli_fetch_assoc($user_res);
} else {
    die("User not found.");
}

// Fetch cart items with product details
$cart_sql = "
    SELECT c.qty, p.product_id, p.product_name, p.price
    FROM cart c
    INNER JOIN products p ON c.product_id = p.product_id
    WHERE c.user_id = ?
";
$stmt = mysqli_prepare($conn, $cart_sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$cart_res = mysqli_stmt_get_result($stmt);

$total_price = 0;
$cart_items = [];
if ($cart_res && mysqli_num_rows($cart_res) > 0) {
    while ($row = mysqli_fetch_assoc($cart_res)) {
        $line_total = $row['price'] * $row['qty'];
        $total_price += $line_total;
        $cart_items[] = $row + ['line_total' => $line_total];
    }
} else {
    die("Your cart is empty.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - K.N. Raam Hardware</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css?v=<?php echo time(); ?>" rel="stylesheet">
</head>
<body>

<?php include('includes/navbar.php'); ?>

<div class="content checkout-container">
    <h2 class="section-title"><i class="fas fa-shopping-cart me-2"></i>Checkout</h2>
    <div class="about-divider"></div>

    <!-- Error Message Container -->
    <div id="error-message" class="alert alert-danger d-none" role="alert"></div>

    <div class="row equal-height-row align-equal">
        <!-- Cart Summary -->
        <div class="col-lg-6 mb-4">
            <div class="card product-card">
                <div class="card-header"><i class="fas fa-list-ul me-2"></i>Order Summary</div>
                <div class="card-body p-0">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Unit Price (LKR)</th>
                                <th>Total (LKR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_items as $item): ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['product_name']); ?></td>
                                    <td><?= $item['qty']; ?></td>
                                    <td class="product-price"><?= number_format($item['price'], 2); ?></td>
                                    <td class="product-price"><?= number_format($item['line_total'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="grand-total-row">
                                <td colspan="3" class="text-end fw-bold">Grand Total:</td>
                                <td class="product-price"><?= number_format($total_price, 2); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Checkout Form -->
        <div class="col-lg-6 mb-4">
            <div class="card product-card">
                <div class="card-header"><i class="fas fa-address-card me-2"></i>Billing Details</div>
                <div class="card-body">
                    <form id="checkout-form" method="POST" action="process_order.php" class="flex-grow-1">
                        <div class="row align-equal">
                            <div class="col-md-6 mb-3">
                                <label class="required">Name</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" name="name" value="<?= htmlspecialchars($user['name']); ?>" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="required">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" name="mail" value="<?= htmlspecialchars($user['mail']); ?>" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="required">Contact No</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="text" name="contact_no" value="<?= htmlspecialchars($user['contact_no']); ?>" class="form-control" required pattern="\d{10}">
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="required">Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                    <textarea name="address" class="form-control h-100" rows="4" required></textarea>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="required">Payment Method</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-credit-card"></i></span>
                                    <select name="payment_method" id="payment_method" class="form-control" required>
                                        <option value="">Select Payment Method</option>
                                        <option value="cash">Cash on Delivery</option>
                                        <option value="online">Pay Online (Card, Wallet, etc.)</option>
                                    </select>
                                    <span class="input-group-text" data-bs-toggle="tooltip" data-bs-placement="right" title="Pay online using credit/debit cards, PayPal, or other digital payment methods.">
                                        <i class="fas fa-info-circle"></i>
                                    </span>
                                </div>
                            </div>
                            <!-- Card Details Section (Hidden by Default) -->
                            <div class="col-md-12 mb-3 card-details" style="display: none;">
                                <div class="card-details-inner">
                                    <div class="mb-3">
                                        <label class="required">Card Name</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                                            <input type="text" name="card_name" class="form-control" placeholder="Name on Card">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="required">Card Type</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-credit-card"></i></span>
                                            <select name="card_type" class="form-control">
                                                <option value="">Select Card Type</option>
                                                <option value="visa">Visa</option>
                                                <option value="master">MasterCard</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="required">Card Number</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-credit-card"></i></span>
                                            <input type="text" name="card_number" class="form-control" placeholder="1234 5678 9012 3456" maxlength="19" pattern="\d{4}\s?\d{4}\s?\d{4}\s?\d{4}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="required">Expiry Date</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                                <input type="text" name="expiry_date" class="form-control" placeholder="MM/YY" pattern="(0[1-9]|1[0-2])\/[0-9]{2}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="required">CVV</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                                <input type="text" name="cvv" class="form-control" placeholder="123" maxlength="4" pattern="\d{3,4}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-checkout"><i class="fas fa-check me-2"></i>Confirm & Proceed</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Initialize Bootstrap tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // Show/hide card details and toggle required attributes
    const paymentMethodSelect = document.getElementById('payment_method');
    const cardDetails = document.querySelector('.card-details');
    const cardInputs = cardDetails.querySelectorAll('input, select');
    const errorMessage = document.getElementById('error-message');
    const checkoutForm = document.getElementById('checkout-form');

    function toggleCardDetails() {
        const isOnline = paymentMethodSelect.value === 'online';
        cardDetails.style.display = isOnline ? 'block' : 'none';
        cardInputs.forEach(input => {
            input.required = isOnline;
        });
    }

    paymentMethodSelect.addEventListener('change', toggleCardDetails);

    // Initialize card details state on page load
    toggleCardDetails();

    // Form submission validation
    checkoutForm.addEventListener('submit', function(event) {
        errorMessage.classList.add('d-none');
        errorMessage.textContent = '';

        if (paymentMethodSelect.value === 'online') {
            const cardNumber = document.querySelector('input[name="card_number"]').value;
            const expiryDate = document.querySelector('input[name="expiry_date"]').value;
            const cvv = document.querySelector('input[name="cvv"]').value;

            if (!cardNumber.match(/^\d{4}\s?\d{4}\s?\d{4}\s?\d{4}$/)) {
                event.preventDefault();
                errorMessage.textContent = 'Please enter a valid 16-digit card number.';
                errorMessage.classList.remove('d-none');
                return;
            }
            if (!expiryDate.match(/^(0[1-9]|1[0-2])\/[0-9]{2}$/)) {
                event.preventDefault();
                errorMessage.textContent = 'Please enter a valid expiry date (MM/YY).';
                errorMessage.classList.remove('d-none');
                return;
            }
            if (!cvv.match(/^\d{3,4}$/)) {
                event.preventDefault();
                errorMessage.textContent = 'Please enter a valid CVV (3 or 4 digits).';
                errorMessage.classList.remove('d-none');
                return;
            }
        }
    });
</script>
</body>
</html>