<?php
    // Get the role from the request
    $role = $_GET['role'];

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch the profile details of the users with the specified role
    $sql = "SELECT * FROM user WHERE Role = '" . $role . "'";
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