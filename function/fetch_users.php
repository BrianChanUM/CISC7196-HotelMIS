
<?php
// Database connection
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

// Fetch users
$sql = "SELECT id, username FROM user WHERE role = 'staff'";
$result = $conn->query($sql);

// Initialize users array
$users = array();

if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
} else {
    echo "0 results";
}

$conn->close();

// Return users as JSON
echo json_encode($users);
?>