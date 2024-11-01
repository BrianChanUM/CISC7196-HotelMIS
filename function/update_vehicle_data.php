<?php
// update_vehicle_data.php

// Replace with your actual database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "HMIS";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get updated data from the request (assuming it's sent as JSON)
$data = json_decode(file_get_contents('php://input'), true);

// Extract values
$updatedVehicleType = $data['vehicletype'];
$updatedStatus = $data['status'] ? 1 : 0; // Convert to 1 or 0

// Update the database (replace with your actual update query)
$sql = "UPDATE hotelvehicletype SET status = $updatedStatus WHERE vehicletype = '$updatedVehicleType'";

if ($conn->query($sql) === TRUE) {
    // Handle success (e.g., return a success message)
    echo json_encode(['message' => 'Data updated successfully']);
} else {
    // Handle error (e.g., return an error message)
    echo json_encode(['error' => 'Error updating data']);
}

$conn->close();
?>
