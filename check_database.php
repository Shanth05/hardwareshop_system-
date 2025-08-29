<?php
// Check Database Structure
$conn = mysqli_connect('localhost', 'root', '', 'kn_raam_hardware');
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

echo "<h2>Database Structure Check</h2>";

// Check if database exists
$db_check = mysqli_query($conn, "SHOW DATABASES LIKE 'kn_raam_hardware'");
if (mysqli_num_rows($db_check) == 0) {
    echo "❌ Database 'kn_raam_hardware' does not exist!<br>";
    echo "Please create the database first.<br>";
    exit();
}

echo "✅ Database 'kn_raam_hardware' exists<br><br>";

// Get all tables
$tables_result = mysqli_query($conn, "SHOW TABLES");
$tables = [];
while ($row = mysqli_fetch_array($tables_result)) {
    $tables[] = $row[0];
}

echo "<h3>Current Tables:</h3>";
if (empty($tables)) {
    echo "❌ No tables found in the database!<br>";
} else {
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>✅ $table</li>";
    }
    echo "</ul>";
}

// Check required tables for the hardware store
$required_tables = [
    'users',
    'categories', 
    'brands',
    'products',
    'cart',
    'orders',
    'order_items',
    'contact_messages'
];

echo "<h3>Required Tables Check:</h3>";
foreach ($required_tables as $table) {
    if (in_array($table, $tables)) {
        echo "✅ $table table exists<br>";
    } else {
        echo "❌ $table table missing<br>";
    }
}

// Check table structures
echo "<h3>Table Structures:</h3>";

if (in_array('categories', $tables)) {
    echo "<h4>Categories Table:</h4>";
    $result = mysqli_query($conn, "DESCRIBE categories");
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>{$row['Field']}</td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>{$row['Key']}</td>";
        echo "<td>{$row['Default']}</td>";
        echo "</tr>";
    }
    echo "</table><br>";
}

if (in_array('brands', $tables)) {
    echo "<h4>Brands Table:</h4>";
    $result = mysqli_query($conn, "DESCRIBE brands");
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>{$row['Field']}</td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>{$row['Key']}</td>";
        echo "<td>{$row['Default']}</td>";
        echo "</tr>";
    }
    echo "</table><br>";
}

if (in_array('products', $tables)) {
    echo "<h4>Products Table:</h4>";
    $result = mysqli_query($conn, "DESCRIBE products");
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>{$row['Field']}</td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>{$row['Key']}</td>";
        echo "<td>{$row['Default']}</td>";
        echo "</tr>";
    }
    echo "</table><br>";
}

// Check current data
echo "<h3>Current Data Count:</h3>";
if (in_array('categories', $tables)) {
    $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM categories");
    $row = mysqli_fetch_assoc($result);
    echo "Categories: {$row['count']}<br>";
}

if (in_array('brands', $tables)) {
    $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM brands");
    $row = mysqli_fetch_assoc($result);
    echo "Brands: {$row['count']}<br>";
}

if (in_array('products', $tables)) {
    $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM products");
    $row = mysqli_fetch_assoc($result);
    echo "Products: {$row['count']}<br>";
}

if (in_array('users', $tables)) {
    $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM users");
    $row = mysqli_fetch_assoc($result);
    echo "Users: {$row['count']}<br>";
}

echo "<br><h3>Next Steps:</h3>";
echo "<p><a href='add_sample_data.php'>Add Sample Hardware Data</a></p>";
echo "<p><a href='search.php'>Test Search Functionality</a></p>";
echo "<p><a href='products.php'>View Products Page</a></p>";

mysqli_close($conn);
?>

