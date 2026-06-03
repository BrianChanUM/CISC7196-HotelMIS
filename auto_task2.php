<?php
// Set your connection variables
$servername = "localhost";
$username = "root";
$password = "123456";
$dbname = "hmis";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted and if form data is set
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['task_name'], $_POST['task_priority'], $_POST['task_date'], $_POST['task_time'])) {
    // Get form data
    $task_name = $_POST['task_name'];
    $task_priority = $_POST['task_priority'];
    $task_date = $_POST['task_date'];
    $task_time = $_POST['task_time'];

    // Insert task into the database
    $sql = "INSERT INTO tasks (task_name, task_priority, task_date, task_time) VALUES ('$task_name', '$task_priority', '$task_date', '$task_time')";

    if ($conn->query($sql) === TRUE) {
        echo "New task created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Form data is missing.";
}

$conn->close();
?>


