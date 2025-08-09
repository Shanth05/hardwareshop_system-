<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "kn_raam_hardware");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user_id = isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : null;

// If logged in, fetch user info for display or other uses (optional)
if ($user_id) {
    $sql = "SELECT * FROM users WHERE user_id=$user_id";
    $res = mysqli_query($conn, $sql);
    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        $name = $row['name'];
        // Other fields as needed...
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Products | K.N. Raam Hardware</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>

<?php include('includes/navbar.php'); ?>

<h2 class="mt-5 pt-4 mb-4 text-center fw-bold h-font">OUR PRODUCTS</h2>
<div class="container">
  <form class="row mb-4" method="POST" action="search.php">
    <div class="col-md-4">
      <select class="form-select" name="brand">
        <option selected>Select Brand</option>
        <?php
        $brands = mysqli_query($conn, "SELECT * FROM brands");
        while ($b = mysqli_fetch_assoc($brands)) {
          echo '<option value="' . htmlspecialchars($b['brand_name']) . '">' . htmlspecialchars($b['brand_name']) . '</option>';
        }
        ?>
      </select>
    </div>
    <div class="col-md-4">
      <select class="form-select" name="price">
        <option selected>Select Price Range</option>
        <option value="1">Rs. 1,000 to 5,000</option>
        <option value="2">Rs. 5,000 to 10,000</option>
        <option value="3">Rs. 10,000 to 20,000</option>
        <option value="4">Above Rs. 20,000</option>
      </select>
    </div>
    <div class="col-md-4">
      <button type="submit" name="search" class="btn btn-primary">Search</button>
    </div>
  </form>

  <div class="row">
    <?php
    $products = mysqli_query($conn, "SELECT * FROM products WHERE status='Available'");
    while ($row = mysqli_fetch_assoc($products)) {
      $product_id = $row['product_id'];
      $product_name = $row['product_name'];
      $price = $row['price'];
      $stock = $row['stock'];
      $image = $row['image'];
      $category = $row['category'];
      ?>
      <div class="col-md-3 mb-4">
        <div class="card">
          <img src="images/products/<?php echo htmlspecialchars($image); ?>" class="card-img-top" height="250" />
          <div class="card-body">
            <h5><a href="product_details.php?product_id=<?php echo $product_id; ?>"><?php echo htmlspecialchars($product_name); ?></a></h5>
            <p>Price: LKR <?php echo number_format($price, 2); ?></p>
            <p>Category: <?php echo htmlspecialchars($category); ?></p>
            <?php if ($stock > 0): ?>
              <?php if ($user_id): ?>
              <!-- Show Add to Cart form only if user is logged in -->
              <form method="POST" action="">
                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product_name); ?>">
                <input type="hidden" name="price" value="<?php echo $price; ?>">
                <input type="number" name="qty" value="1" min="1" max="<?php echo $stock; ?>" class="form-control mb-2" required>
                <button type="submit" name="add_to_cart" class="btn btn-outline-dark">Add to Cart</button>
              </form>
              <?php else: ?>
                <p><a href="login.php">Login</a> to add to cart</p>
              <?php endif; ?>
            <?php else: ?>
              <p class="text-danger">Out of Stock</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>
</div>

<?php include('includes/footer.php'); ?>

<?php
// Process add to cart ONLY if user logged in
if (isset($_POST['add_to_cart'])) {
    if (!$user_id) {
        echo '<script>alert("Please login first to add products to your cart."); window.location.href="login.php";</script>';
        exit();
    }
    $product_id = $_POST['product_id'];
    $qty = $_POST['qty'];

    // Check if already in cart
    $check = mysqli_query($conn, "SELECT * FROM cart WHERE user_id=$user_id AND product_id=$product_id");
    if (mysqli_num_rows($check) > 0) {
        echo '<script>alert("Product already in cart!"); window.location.href="products.php";</script>';
    } else {
        $insert = mysqli_query($conn, "INSERT INTO cart (user_id, product_id, qty) VALUES ('$user_id', '$product_id', '$qty')");
        if ($insert) {
            echo '<script>alert("Product added to cart!"); window.location.href="products.php";</script>';
        } else {
            echo '<script>alert("Failed to add product."); window.location.href="products.php";</script>';
        }
    }
}
?>
