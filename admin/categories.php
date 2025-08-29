<?php
$page_title = "Categories";
include('header.php');

// Fetch all categories
$res = mysqli_query($conn, "SELECT * FROM categories ORDER BY category_name ASC");

// Handle messages from Add/Edit/Delete
$msg = $_GET['msg'] ?? '';
$alert = '';
if ($msg == 'added') {
    $alert = '<div id="alert-msg" class="alert alert-success">Category added successfully.</div>';
} elseif ($msg == 'updated') {
    $alert = '<div id="alert-msg" class="alert alert-success">Category updated successfully.</div>';
} elseif ($msg == 'deleted') {
    $alert = '<div id="alert-msg" class="alert alert-success">Category deleted successfully.</div>';
} elseif ($msg == 'delete_error') {
    $alert = '<div id="alert-msg" class="alert alert-danger">Failed to delete category.</div>';
} elseif ($msg == 'invalid') {
    $alert = '<div id="alert-msg" class="alert alert-warning">Invalid category ID.</div>';
}
?>
<h2 class="mb-4">Categories</h2>

<!-- Alert Messages -->
<?= $alert ?>
<script>
  setTimeout(() => {
    const el = document.getElementById('alert-msg');
    if(el) el.style.display = 'none';
  }, 2500);
</script>

<div class="mb-3 text-end">
  <a href="add_category.php" class="btn btn-success"><i class="bi bi-plus-lg"></i> Add New Category</a>
</div>

<div class="table-responsive">
  <table class="table table-striped table-bordered align-middle">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Category Name</th>
        <th>Description</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if ($res && mysqli_num_rows($res) > 0) {
          while ($row = mysqli_fetch_assoc($res)) {
              echo "<tr>";
              echo "<td>" . intval($row['category_id']) . "</td>";
              echo "<td>" . htmlspecialchars($row['category_name']) . "</td>";
              echo "<td>" . htmlspecialchars($row['description'] ?? '') . "</td>";
              echo "<td>";
              echo "<a href='edit_category.php?id=" . urlencode($row['category_id']) . "' class='btn btn-primary btn-sm me-1'><i class='bi bi-pencil'></i></a>";
              echo "<a href='delete_category.php?id=" . urlencode($row['category_id']) . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this category?\");'><i class='bi bi-trash'></i></a>";
              echo "</td>";
              echo "</tr>";
          }
      } else {
          echo '<tr><td colspan="4" class="text-center">No categories found.</td></tr>';
      }
      ?>
    </tbody>
  </table>
</div>

<?php include('footer.php'); ?>
      
