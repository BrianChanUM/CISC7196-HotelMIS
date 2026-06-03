<?php
$conn = new mysqli('localhost', 'root', '123456', 'hmis');
if($conn->connect_error) {
    echo 'Error: ' . $conn->connect_error;
} else {
    $result = $conn->query('SELECT OrderType, COUNT(*) as Total FROM orderbookings GROUP BY OrderType ORDER BY Total DESC LIMIT 10');
    while($row = $result->fetch_assoc()) {
        echo $row['OrderType'] . ': ' . $row['Total'] . "\n";
    }
    $conn->close();
}
