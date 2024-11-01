<?php
// update_vehicle_data.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $vehicleType = $_POST['editVehicleType'];
    $status = $_POST['status']; // 'enabled' or 'disabled'
// Replace with your actual database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "HMIS";

// Create connection
  $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }

    $sql = "UPDATE hotelvehicletype SET status = '$status' WHERE vehicletype = '$vehicleType'";
    if ($conn->query($sql) === true) {
        echo 'Database updated successfully';
    } else {
        echo 'Error updating database: ' . $conn->error;
    }

    $conn->close();
}
?>