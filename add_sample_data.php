<?php
// Add Sample Hardware Data for Testing
$conn = mysqli_connect('localhost', 'root', '', 'kn_raam_hardware');
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

echo "<h2>Adding Sample Hardware Data...</h2>";

// First, let's add some hardware categories
$categories = [
    ['Hand Tools', 'Essential hand tools for construction and repair work'],
    ['Power Tools', 'Electric and battery-powered tools for professional use'],
    ['Plumbing', 'Pipes, fittings, and plumbing accessories'],
    ['Electrical', 'Wires, switches, and electrical components'],
    ['Building Materials', 'Construction materials and supplies'],
    ['Garden Tools', 'Tools for gardening and outdoor maintenance'],
    ['Safety Equipment', 'Personal protective equipment and safety gear'],
    ['Fasteners', 'Screws, nails, bolts, and other fastening materials'],
    ['Paint & Finishing', 'Paints, varnishes, and finishing materials'],
    ['Hardware Accessories', 'Miscellaneous hardware items and accessories']
];

echo "<h3>Adding Categories...</h3>";
foreach ($categories as $category) {
    $name = $category[0];
    $description = $category[1];
    
    // Check if category already exists
    $check = mysqli_query($conn, "SELECT category_id FROM categories WHERE category_name = '$name'");
    if (mysqli_num_rows($check) == 0) {
        $sql = "INSERT INTO categories (category_name, description) VALUES ('$name', '$description')";
        if (mysqli_query($conn, $sql)) {
            echo "✅ Added category: $name<br>";
        } else {
            echo "❌ Failed to add category: $name - " . mysqli_error($conn) . "<br>";
        }
    } else {
        echo "ℹ️ Category already exists: $name<br>";
    }
}

// Add some brands
$brands = [
    'Bosch',
    'Makita',
    'DeWalt',
    'Stanley',
    'Black & Decker',
    'Hitachi',
    'Milwaukee',
    'Ryobi',
    'Craftsman',
    'Husky'
];

echo "<h3>Adding Brands...</h3>";
foreach ($brands as $brand) {
    // Check if brand already exists
    $check = mysqli_query($conn, "SELECT brand_id FROM brands WHERE brand_name = '$brand'");
    if (mysqli_num_rows($check) == 0) {
        $sql = "INSERT INTO brands (brand_name) VALUES ('$brand')";
        if (mysqli_query($conn, $sql)) {
            echo "✅ Added brand: $brand<br>";
        } else {
            echo "❌ Failed to add brand: $brand - " . mysqli_error($conn) . "<br>";
        }
    } else {
        echo "ℹ️ Brand already exists: $brand<br>";
    }
}

// Get category and brand IDs for products
$category_result = mysqli_query($conn, "SELECT category_id, category_name FROM categories");
$categories_data = [];
while ($row = mysqli_fetch_assoc($category_result)) {
    $categories_data[$row['category_name']] = $row['category_id'];
}

$brand_result = mysqli_query($conn, "SELECT brand_id, brand_name FROM brands");
$brands_data = [];
while ($row = mysqli_fetch_assoc($brand_result)) {
    $brands_data[$row['brand_name']] = $row['brand_id'];
}

// Add sample products
$products = [
    // Hand Tools
    ['Stanley Hammer 16oz', 'Hand Tools', 'Stanley', 2500.00, 50, 'Professional grade claw hammer with fiberglass handle', 'Available'],
    ['Adjustable Wrench Set', 'Hand Tools', 'Craftsman', 3500.00, 30, 'Set of 3 adjustable wrenches in different sizes', 'Available'],
    ['Screwdriver Set', 'Hand Tools', 'Stanley', 1800.00, 75, 'Complete screwdriver set with various head types', 'Available'],
    ['Pliers Set', 'Hand Tools', 'Husky', 2200.00, 40, 'Professional pliers set including needle nose and cutting pliers', 'Available'],
    
    // Power Tools
    ['Bosch Drill Driver', 'Power Tools', 'Bosch', 45000.00, 25, '18V cordless drill driver with 2 batteries', 'Available'],
    ['Makita Circular Saw', 'Power Tools', 'Makita', 35000.00, 15, '7-1/4 inch circular saw with laser guide', 'Available'],
    ['DeWalt Impact Driver', 'Power Tools', 'DeWalt', 28000.00, 30, '20V MAX impact driver for heavy-duty applications', 'Available'],
    ['Black & Decker Jigsaw', 'Power Tools', 'Black & Decker', 12000.00, 20, 'Variable speed jigsaw with orbital action', 'Available'],
    
    // Plumbing
    ['PVC Pipe 1 inch', 'Plumbing', 'Generic', 850.00, 100, '1 inch PVC pipe, 10 feet length', 'Available'],
    ['Copper Fittings Set', 'Plumbing', 'Generic', 1500.00, 50, 'Assorted copper fittings for plumbing work', 'Available'],
    ['Pipe Wrench 14 inch', 'Plumbing', 'Stanley', 3200.00, 25, 'Heavy-duty pipe wrench for plumbing applications', 'Available'],
    ['Teflon Tape Roll', 'Plumbing', 'Generic', 250.00, 200, 'PTFE thread seal tape for pipe connections', 'Available'],
    
    // Electrical
    ['Electrical Wire 2.5mm', 'Electrical', 'Generic', 1200.00, 80, '2.5mm electrical wire, 100 meters', 'Available'],
    ['Circuit Breaker 32A', 'Electrical', 'Generic', 1800.00, 40, '32A circuit breaker for electrical panels', 'Available'],
    ['LED Bulb Pack', 'Electrical', 'Generic', 1200.00, 60, 'Pack of 10 LED bulbs, 9W each', 'Available'],
    ['Switch Socket Set', 'Electrical', 'Generic', 800.00, 100, 'Set of electrical switches and sockets', 'Available'],
    
    // Building Materials
    ['Cement Bag 50kg', 'Building Materials', 'Generic', 1200.00, 200, 'Portland cement, 50kg bag', 'Available'],
    ['Steel Rebar 12mm', 'Building Materials', 'Generic', 850.00, 150, '12mm steel reinforcement bar, 6m length', 'Available'],
    ['Sand Bag', 'Building Materials', 'Generic', 450.00, 300, 'Construction sand, 50kg bag', 'Available'],
    ['Bricks Pack', 'Building Materials', 'Generic', 1800.00, 100, 'Pack of 50 standard bricks', 'Available'],
    
    // Garden Tools
    ['Garden Shovel', 'Garden Tools', 'Generic', 1200.00, 30, 'Heavy-duty garden shovel with wooden handle', 'Available'],
    ['Pruning Shears', 'Garden Tools', 'Generic', 800.00, 45, 'Professional pruning shears for garden maintenance', 'Available'],
    ['Garden Hose 50ft', 'Garden Tools', 'Generic', 2500.00, 25, '50 feet garden hose with spray nozzle', 'Available'],
    ['Rake Set', 'Garden Tools', 'Generic', 1500.00, 20, 'Set of garden rakes in different sizes', 'Available'],
    
    // Safety Equipment
    ['Safety Helmet', 'Safety Equipment', 'Generic', 1200.00, 50, 'Hard hat safety helmet for construction', 'Available'],
    ['Safety Gloves', 'Safety Equipment', 'Generic', 450.00, 100, 'Heavy-duty work gloves for protection', 'Available'],
    ['Safety Goggles', 'Safety Equipment', 'Generic', 350.00, 75, 'Clear safety goggles for eye protection', 'Available'],
    ['Safety Vest', 'Safety Equipment', 'Generic', 800.00, 40, 'High-visibility safety vest', 'Available'],
    
    // Fasteners
    ['Screw Pack 100pcs', 'Fasteners', 'Generic', 450.00, 200, 'Pack of 100 assorted screws', 'Available'],
    ['Nail Pack 1kg', 'Fasteners', 'Generic', 350.00, 150, '1kg pack of construction nails', 'Available'],
    ['Bolt Set', 'Fasteners', 'Generic', 650.00, 80, 'Assorted bolts and nuts set', 'Available'],
    ['Wall Plugs', 'Fasteners', 'Generic', 250.00, 300, 'Pack of wall plugs for mounting', 'Available'],
    
    // Paint & Finishing
    ['Interior Paint 5L', 'Paint & Finishing', 'Generic', 3500.00, 30, '5L interior wall paint, white', 'Available'],
    ['Paint Brush Set', 'Paint & Finishing', 'Generic', 800.00, 50, 'Set of professional paint brushes', 'Available'],
    ['Varnish 1L', 'Paint & Finishing', 'Generic', 1200.00, 25, 'Clear wood varnish, 1 liter', 'Available'],
    ['Paint Roller Set', 'Paint & Finishing', 'Generic', 650.00, 40, 'Paint roller set with tray', 'Available'],
    
    // Hardware Accessories
    ['Door Handle Set', 'Hardware Accessories', 'Generic', 1800.00, 35, 'Complete door handle and lock set', 'Available'],
    ['Hinges Pack', 'Hardware Accessories', 'Generic', 450.00, 100, 'Pack of door hinges', 'Available'],
    ['Drawer Slides', 'Hardware Accessories', 'Generic', 650.00, 60, 'Heavy-duty drawer slides', 'Available'],
    ['Cabinet Knobs', 'Hardware Accessories', 'Generic', 350.00, 150, 'Pack of cabinet door knobs', 'Available']
];

echo "<h3>Adding Products...</h3>";
foreach ($products as $product) {
    $name = $product[0];
    $category_name = $product[1];
    $brand_name = $product[2];
    $price = $product[3];
    $stock = $product[4];
    $description = $product[5];
    $status = $product[6];
    
    $category_id = $categories_data[$category_name] ?? 1;
    $brand_id = $brands_data[$brand_name] ?? 1;
    
    // Check if product already exists
    $check = mysqli_query($conn, "SELECT product_id FROM products WHERE product_name = '$name'");
    if (mysqli_num_rows($check) == 0) {
        $sql = "INSERT INTO products (product_name, category_id, brand_id, price, stock, description, status) 
                VALUES ('$name', $category_id, $brand_id, $price, $stock, '$description', '$status')";
        if (mysqli_query($conn, $sql)) {
            echo "✅ Added product: $name (LKR $price)<br>";
        } else {
            echo "❌ Failed to add product: $name - " . mysqli_error($conn) . "<br>";
        }
    } else {
        echo "ℹ️ Product already exists: $name<br>";
    }
}

echo "<h3>✅ Sample Data Addition Complete!</h3>";
echo "<p>You can now test the search functionality with these hardware products.</p>";
echo "<p><a href='search.php'>Go to Search Page</a> | <a href='products.php'>Go to Products Page</a></p>";

mysqli_close($conn);
?>

