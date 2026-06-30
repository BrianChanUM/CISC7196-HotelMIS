<?php
require_once __DIR__ . '/config/db_config.php';

// Get the vehicle type from the query parameter
$vehicleType = isset($_GET['vehicletype']) ? trim($_GET['vehicletype']) : '';

if (empty($vehicleType)) {
    echo '<tr><td colspan="2">No data available</td></tr>';
    exit;
}

$conn = getDBConnection();

// Query to retrieve data for the specified vehicle type using prepared statement
$sql = "SELECT vehicletype, status FROM hotelvehicletype WHERE vehicletype = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$vehicleType]);

if ($stmt->rowCount() > 0) {
    while ($row = $stmt->fetch()) {
        echo '<tr>';
        echo '<td class="editable" data-vehicletype="' . htmlspecialchars($row['vehicletype']) . '">' . htmlspecialchars($row['vehicletype']) . '</td>';
        echo '<td class="editable" data-status="' . htmlspecialchars($row['status']) . '">' . ($row['status'] ? 'Enabled' : 'Disabled') . '</td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="2">No data available</td></tr>';
}

closeDBConnection($conn);
?>
