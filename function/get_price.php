<?php
// get_price.php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "HMIS";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the selected room type from the query parameter
$selectedRoomType = $_GET['roomType'];

// Query to retrieve the room price
$sql = "SELECT HotelRoomPrice FROM hotelroomtype WHERE HotelRoomtype = '$selectedRoomType'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $price = $row['HotelRoomPrice'];
    echo json_encode(['price' => $price]);
} else {
    echo json_encode(['price' => 'N/A']);
}

$conn->close();
?>
