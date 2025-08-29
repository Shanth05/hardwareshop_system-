<?php
include 'includes/db.php';

echo "<h3>Users Table Structure:</h3>";
$result = mysqli_query($conn, "DESCRIBE users");
if ($result) {
    while($row = mysqli_fetch_assoc($result)) {
        echo $row['Field'] . ' - ' . $row['Type'] . '<br>';
    }
} else {
    echo "Error: " . mysqli_error($conn);
}

echo "<br><h3>Sample Admin Data:</h3>";
$sample = mysqli_query($conn, "SELECT * FROM users WHERE user_type = 'admin' LIMIT 1");
if ($sample && mysqli_num_rows($sample) > 0) {
    $row = mysqli_fetch_assoc($sample);
    foreach($row as $key => $value) {
        echo $key . ': ' . $value . '<br>';
    }
} else {
    echo "No admin data found or error: " . mysqli_error($conn);
}
?>

