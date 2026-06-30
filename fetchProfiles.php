<?php
require_once __DIR__ . '/config/db_config.php';

// Get the role from the request
$role = isset($_GET['role']) ? trim($_GET['role']) : '';

if (empty($role)) {
    echo json_encode([]);
    exit;
}

$conn = getDBConnection();

// Fetch all the user details with the specified role using prepared statement
$sql = "SELECT * FROM user WHERE Role = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$role]);
$data = $stmt->fetchAll();

closeDBConnection($conn);

// Convert the $data array into JSON format
echo json_encode($data);
?>
