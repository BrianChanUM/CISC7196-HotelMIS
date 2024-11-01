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

    $sql = "SELECT OrderType, Time, Status FROM orderbookings";
    $result = $conn->query($sql);

    $data = array(); // Array to hold your data

    // Process the results
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            // Add each row of data to the $data array
            $data[] = $row;
        }
    }

    // Convert the $data array into JSON format
    echo json_encode($data);

    $conn->close();
?>