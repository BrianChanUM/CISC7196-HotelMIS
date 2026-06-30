<?php
/**
 * 检查库存脚本
 */
require_once __DIR__ . '/config/db_config.php';

echo "<h2>Inventory Check</h2>";

$conn = getDBConnection();

if (!$conn) {
    die("<p style='color:red'>Database connection failed!</p>");
}

echo "<p style='color:green'>Database connection successful</p>";

// 检查车辆库存
echo "<h3>Vehicle Inventory (hotelvehicletype)</h3>";
$stmt = $conn->query("SELECT VehicleType, VehiclePrice, daily_quantity FROM hotelvehicletype ORDER BY VehicleType");
echo "<table border='1'><tr><th>VehicleType</th><th>VehiclePrice</th><th>daily_quantity</th></tr>";
while ($row = $stmt->fetch()) {
    $color = $row['daily_quantity'] > 0 ? 'green' : 'red';
    echo "<tr>";
    echo "<td>" . $row['VehicleType'] . "</td>";
    echo "<td>" . $row['VehiclePrice'] . "</td>";
    echo "<td style='color:$color'>" . $row['daily_quantity'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// 检查酒店房间库存
echo "<h3>Room Inventory (hotelroomtype)</h3>";
$stmt = $conn->query("SELECT HotelRoomtype, HotelRoomPrice, daily_quantity FROM hotelroomtype ORDER BY HotelRoomtype");
echo "<table border='1'><tr><th>HotelRoomtype</th><th>HotelRoomPrice</th><th>daily_quantity</th></tr>";
while ($row = $stmt->fetch()) {
    $color = $row['daily_quantity'] > 0 ? 'green' : 'red';
    echo "<tr>";
    echo "<td>" . $row['HotelRoomtype'] . "</td>";
    echo "<td>" . $row['HotelRoomPrice'] . "</td>";
    echo "<td style='color:$color'>" . $row['daily_quantity'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// 检查餐厅容量
echo "<h3>Outlet Capacity (hoteloutlet)</h3>";
$stmt = $conn->query("SELECT OutletName, Price, capacity FROM hoteloutlet ORDER BY OutletName");
echo "<table border='1'><tr><th>OutletName</th><th>Price</th><th>capacity</th></tr>";
while ($row = $stmt->fetch()) {
    $color = $row['capacity'] > 0 ? 'green' : 'red';
    echo "<tr>";
    echo "<td>" . $row['OutletName'] . "</td>";
    echo "<td>" . ($row['Price'] ?? 'N/A') . "</td>";
    echo "<td style='color:$color'>" . $row['capacity'] . "</td>";
    echo "</tr>";
}
echo "</table>";

closeDBConnection($conn);

echo "<h3>Session pending_order</h3>";
if (isset($_SESSION['pending_order'])) {
    echo "<pre>" . print_r($_SESSION['pending_order'], true) . "</pre>";

    // 分析问题
    $order = $_SESSION['pending_order'];
    echo "<h3>Analysis</h3>";
    echo "<ul>";
    echo "<li>OrderType: " . $order['OrderType'] . "</li>";
    echo "<li>ContactNo: " . $order['ContactNo'] . " (intval: " . intval($order['ContactNo']) . ")</li>";
    echo "<li>Email: " . $order['Email'] . "</li>";

    if ($order['OrderType'] == 'Limo') {
        echo "<li>vehicle_type: " . ($order['vehicle_type'] ?? 'N/A') . "</li>";
        echo "<li><strong style='color:red'>Check if vehicle_type exists in inventory table above!</strong></li>";
    } elseif ($order['OrderType'] == 'Hotel') {
        echo "<li>hotel_type: " . ($order['hotel_type'] ?? 'N/A') . "</li>";
        echo "<li><strong style='color:red'>Check if hotel_type exists in inventory table above!</strong></li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color:orange'>No pending_order in session</p>";
}
?>