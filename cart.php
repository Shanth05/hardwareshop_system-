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

// Handle form submission to update quantities
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['qty'])) {
    foreach ($_POST['qty'] as $cart_id => $qty) {
        // Sanitize quantity
        $qty = max(1, (int)$qty); // Ensure quantity is at least 1
        $update_sql = "UPDATE cart SET qty = $qty WHERE cart_id = $cart_id AND user_id = $user_id";
        mysqli_query($conn, $update_sql);
    }
    // Redirect to checkout.php after updating quantities
    header('Location: checkout.php');
    exit();
}

// Fetch cart items with product details
$sql = "SELECT cart.cart_id, cart.qty, products.product_name, products.price
        FROM cart
        INNER JOIN products ON cart.product_id = products.product_id
        WHERE cart.user_id = $user_id";

$res = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>K.N. Raam Hardware - Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>" />
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="content checkout-container">
        <h2 class="section-title"><i class="fas fa-shopping-cart me-2"></i>Your Cart</h2>
        <div class="about-divider"></div>

        <?php if ($res && mysqli_num_rows($res) > 0) : ?>
            <!-- Form submits to itself to update quantities, then redirects to checkout.php -->
            <form method="POST" action="cart.php" id="cartForm">
                <table class="table table-bordered align-middle" id="cartTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Price (LKR)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sn = 1;
                        while ($row = mysqli_fetch_assoc($res)) :
                            $cart_id = $row['cart_id'];
                            $product_name = htmlspecialchars($row['product_name']);
                            $qty = $row['qty'];
                            $unit_price = $row['price'];
                            $total_price = $unit_price * $qty;
                        ?>
                            <tr data-cartid="<?php echo $cart_id; ?>" data-unit-price="<?php echo $unit_price; ?>">
                                <td><?php echo $sn++; ?></td>
                                <td><?php echo $product_name; ?></td>
                                <td>
                                    <input
                                        type="number"
                                        name="qty[<?php echo $cart_id; ?>]"
                                        value="<?php echo $qty; ?>"
                                        min="1"
                                        class="form-control qty-input"
                                        required
                                    />
                                </td>
                                <td class="product-price price-cell"><?php echo number_format($total_price, 2); ?></td>
                                <td>
                                    <a href="remove_cart.php?cart_id=<?php echo $cart_id; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to remove this item?');">
                                        <i class="fas fa-trash-alt"></i> Remove
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr class="grand-total-row">
                            <td colspan="3" class="text-end fw-bold">Total Price</td>
                            <td colspan="2" class="product-price" id="totalPriceCell"></td>
                        </tr>
                    </tfoot>
                </table>

                <!-- Proceed to checkout button -->
                <div class="d-flex justify-content-end gap-3">
                    <button type="submit" class="btn btn-checkout"><i class="fas fa-check me-2"></i>Proceed to Checkout</button>
                </div>
            </form>
        <?php else : ?>
            <p class="text-center about-text">Your cart is empty.</p>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Format numbers as currency (2 decimals)
        function formatCurrency(num) {
            return num.toFixed(2);
        }

        function updatePrices() {
            const rows = document.querySelectorAll('#cartTable tbody tr');
            let totalPrice = 0;

            rows.forEach(row => {
                const unitPrice = parseFloat(row.getAttribute('data-unit-price'));
                const qtyInput = row.querySelector('.qty-input');
                let qty = parseInt(qtyInput.value);

                if (isNaN(qty) || qty < 1) {
                    qty = 1;
                    qtyInput.value = qty;
                }

                const priceCell = row.querySelector('.price-cell');
                const rowTotal = unitPrice * qty;
                priceCell.textContent = formatCurrency(rowTotal);

                totalPrice += rowTotal;
            });

            // Update total price cell
            document.getElementById('totalPriceCell').textContent = formatCurrency(totalPrice) + ' LKR';
        }

        // Initialize prices on page load
        updatePrices();

        // Update prices live on quantity input change
        document.querySelectorAll('.qty-input').forEach(input => {
            input.addEventListener('input', updatePrices);
        });
    </script>
</body>
</html>