<?php
// get_vehicle_data.php

// Replace with your actual database credentials
$servername = "localhost";
$username = "root";
$password = "123456";
$dbname = "hmis";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the vehicle type from the query parameter (you can sanitize this input)
$vehicleType = $_GET['vehicletype'] ?? null;

// Query to retrieve data for the specified vehicle type
$sql = "SELECT vehicletype, status FROM hotelvehicletype WHERE vehicletype = '$vehicleType'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td class="editable" data-vehicletype="' . $row['vehicletype'] . '">' . $row['vehicletype'] . '</td>';
        echo '<td class="editable" data-status="' . $row['status'] . '">' . ($row['status'] ? 'Enabled' : 'Disabled') . '</td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="2">No data available</td></tr>';
}

$conn->close();
?>
