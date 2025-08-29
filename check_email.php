<?php
// Check email availability via AJAX
header('Content-Type: application/json');

// Database connection
$conn = mysqli_connect('localhost', 'root', '', 'kn_raam_hardware');
if (!$conn) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

// Get email from POST request
$email = isset($_POST['email']) ? trim($_POST['email']) : '';

if (empty($email)) {
    echo json_encode(['error' => 'Email is required']);
    exit();
}

// Basic email validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['error' => 'Invalid email format']);
    exit();
}

// Check if email already exists
$stmt = $conn->prepare("SELECT user_id FROM users WHERE mail = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

$available = ($result->num_rows === 0);

echo json_encode([
    'available' => $available,
    'email' => $email
]);

$stmt->close();
mysqli_close($conn);
?>

