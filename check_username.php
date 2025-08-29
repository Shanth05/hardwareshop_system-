<?php
// Check username availability via AJAX
header('Content-Type: application/json');

// Database connection
$conn = mysqli_connect('localhost', 'root', '', 'kn_raam_hardware');
if (!$conn) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

// Get username from POST request
$username = isset($_POST['username']) ? trim($_POST['username']) : '';

if (empty($username)) {
    echo json_encode(['error' => 'Username is required']);
    exit();
}

// Check if username already exists
$stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$available = ($result->num_rows === 0);

echo json_encode([
    'available' => $available,
    'username' => $username
]);

$stmt->close();
mysqli_close($conn);
?>

