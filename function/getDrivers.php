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

$sql = "SELECT DriverName FROM hoteldriver";
$result = $conn->query($sql);

$drivers = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $drivers[] = $row;
    }
}

echo json_encode($drivers);

$conn->close();
?>