<?php
require_once __DIR__ . '/config/db_config.php';

$conn = getDBConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['task_name'], $_POST['task_priority'], $_POST['task_date'], $_POST['task_time'])) {
    $task_name = trim($_POST['task_name']);
    $task_priority = trim($_POST['task_priority']);
    $task_date = trim($_POST['task_date']);
    $task_time = trim($_POST['task_time']);

    if (!empty($task_name) && !empty($task_date) && !empty($task_time)) {
        $stmt = $conn->prepare("INSERT INTO tasks (task_name, task_priority, task_date, task_time) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$task_name, $task_priority, $task_date, $task_time])) {
            echo "New task created successfully";
        } else {
            echo "Error creating task";
        }
    } else {
        echo "Form data is missing or invalid.";
    }
} else {
    echo "Form data is missing.";
}

closeDBConnection($conn);
?>