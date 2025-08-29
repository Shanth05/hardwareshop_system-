<?php
$page_title = "Products";
include('header.php');

$sql = "
SELECT p.product_id, p.product_name, p.price, p.stock, p.image, 
       c.category_name, b.brand_name, p.description, p.status
FROM products p
LEFT JOIN categories c ON p.category_id = c.category_id
LEFT JOIN brands b ON p.brand_id = b.brand_id
ORDER BY p.product_id DESC;
";

$res = mysqli_query($conn, $sql);
?>
      <h2 class="mb-4">Products</h2>

      <!-- Alert Message -->
      <?php
      if (isset($_GET['msg'])) {
          $message = '';
          $alertClass = 'alert-success';
          switch ($_GET['msg']) {
              case 'added': $message = 'Product added successfully.'; break;
              case 'updated': $message = 'Product updated successfully.'; break;
              case 'deleted': $message = 'Product deleted successfully.'; break;
          }
          if ($message) {
              echo "<div id='alert-msg' class='alert $alertClass'>$message</div>";
          }
      }
      ?>

      <div class="mb-3 text-end">
        <a href="add_product.php" class="btn btn-success"><i class="bi bi-plus-lg"></i> Add New Product</a>
      </div>

      <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle">
          <thead class="table-dark">
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Brand</th>
              <th>Category</th>
              <th>Price (LKR)</th>
              <th>Stock</th>
              <th>Image</th>
              <th>Description</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if ($res && mysqli_num_rows($res) > 0) {
                while ($row = mysqli_fetch_assoc($res)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['product_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['brand_name'] ?? 'N/A') . "</td>";
                    echo "<td>" . htmlspecialchars($row['category_name'] ?? 'N/A') . "</td>";
                    echo "<td>" . number_format($row['price'], 2) . "</td>";
                    echo "<td>" . intval($row['stock']) . "</td>";
                    echo "<td>";
                    if (!empty($row['image'])) {
                        echo "<img src='../uploads/" . htmlspecialchars($row['image']) . "' alt='Product Image' class='product-image' />";
                    } else { echo "No Image"; }
                    echo "</td>";
                    echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                    echo "<td>";
                    echo "<a href='edit_product.php?id=" . urlencode($row['product_id']) . "' class='btn btn-primary btn-sm me-1'><i class='bi bi-pencil'></i></a>";
                    echo "<a href='delete_product.php?id=" . urlencode($row['product_id']) . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this product?\");'><i class='bi bi-trash'></i></a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo '<tr><td colspan="10" class="text-center">No products found.</td></tr>';
            }
            ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</div>

<script>
  // Auto-hide alert after 2.5 seconds
  const alertMsg = document.getElementById('alert-msg');
  if (alertMsg) {
      setTimeout(() => {
          alertMsg.style.transition = 'opacity 0.5s';
          alertMsg.style.opacity = '0';
          setTimeout(() => alertMsg.remove(), 500);
      }, 2500);
  }
</script>

<?php include('footer.php'); ?>
