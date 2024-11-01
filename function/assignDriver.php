<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hmis";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the order ID and driver from the POST data
$orderId = $_POST["orderId"];
$driver = $_POST["driver"];

// Update the AssignedTo field in the orderbookings table
$sql = "UPDATE orderbookings SET AssignedTo='$driver' WHERE OrderID=$orderId";
if ($conn->query($sql) === TRUE) {
    echo "Driver assigned successfully";
} else {
    echo "Error assigning driver: " . $conn->error;
}

$conn->close();
?>