<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/db_config.php';

$message = '';
$orderId = isset($_GET['orderId']) ? intval($_GET['orderId']) : 0;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["cancel_order"])) {
    $orderId = intval($_POST["order_id"]);
    $reason = $_POST["reason"];
    
    $conn = getDBConnection();
    
    // 检查订单状态，只能取消非确认的订单
    $getOrderStmt = $conn->prepare("SELECT Status, OrderType, OrderRemark FROM orderbookings WHERE OrderID = ?");
    $getOrderStmt->execute([$orderId]);
    $order = $getOrderStmt->fetch();
    
    if ($order) {
        if ($order['Status'] == 'Cancelled') {
            $message = "Order already cancelled";
        } elseif ($order['Status'] == 'Completed') {
            $message = "Completed orders cannot be cancelled";
        } elseif ($order['Status'] == 'Confirmed') {
            $message = "Confirmed orders cannot be cancelled";
        } else {
            // Update order status to cancelled
            $updateStmt = $conn->prepare("UPDATE orderbookings SET Status = 'Cancelled', OrderRemark = CONCAT(OrderRemark, ' - Cancelled: ', ?), OrderModifiedDate = NOW() WHERE OrderID = ?");
            if ($updateStmt->execute([$reason, $orderId])) {
                // Restore inventory for Hotel or Limo orders
                if ($order['OrderType'] == 'Hotel') {
                    preg_match('/^([A-Za-z0-9\s]+)\s\|/', $order['OrderRemark'], $matches);
                    if (isset($matches[1])) {
                        $roomType = trim($matches[1]);
                        $restoreStmt = $conn->prepare("UPDATE hotelroomtype SET daily_quantity = daily_quantity + 1 WHERE HotelRoomtype = ?");
                        $restoreStmt->execute([$roomType]);
                    }
                } elseif ($order['OrderType'] == 'Limo') {
                    preg_match('/^([A-Za-z0-9\s]+)\s\|/', $order['OrderRemark'], $matches);
                    if (isset($matches[1])) {
                        $vehicleType = trim($matches[1]);
                        $restoreStmt = $conn->prepare("UPDATE hotelvehicletype SET daily_quantity = daily_quantity + 1 WHERE VehicleType = ?");
                        $restoreStmt->execute([$vehicleType]);
                    }
                }
                $message = "Order cancelled successfully";
            } else {
                $message = "Failed to cancel order";
            }
        }
    } else {
        $message = "Order does not exist";
    }
    
    closeDBConnection($conn);
}

// 获取用户可取消的订单列表
$conn = getDBConnection();
$sql = "SELECT OrderID, OrderType, Time, OrderRemark, Status FROM orderbookings WHERE Status NOT IN ('Completed', 'Cancelled', 'Confirmed') ORDER BY OrderCreatedDate DESC";
$result = $conn->query($sql);
$pendingOrders = $result->fetchAll(PDO::FETCH_ASSOC);
closeDBConnection($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancel Order - HotelMIS</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { padding-top: 80px; background-color: #f8f9fa; }
        .container { max-width: 800px; }
        .card { background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); padding: 20px; margin-bottom: 20px; }
        .card-header { font-size: 18px; font-weight: 600; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #eee; }
        .order-item { padding: 15px; border: 1px solid #eee; border-radius: 8px; margin-bottom: 15px; }
        .order-item:hover { background-color: #f8f9fa; }
        .btn-cancel { background-color: #dc3545; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; }
        .btn-cancel:hover { background-color: #c82333; }
        .message { padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <nav id="tf-menu" class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php">HotelMIS</a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <?php include(__DIR__ . '/layout/header.php');?>
                <ul class="nav navbar-nav navbar-right" id="navbar"></ul>
                <?php include(__DIR__ . '/layout/language_switcher.php');?>
                <?php include(__DIR__ . '/layout/navbar.php');?>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2>Cancel Order</h2>
        <p>You can only cancel orders with Pending (TBC) status</p>
        
        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <!-- Cancel Order Form -->
        <?php if ($orderId): ?>
        <div class="card">
            <div class="card-header">Confirm Cancel Order #<?php echo $orderId; ?></div>
            <form method="post">
                <input type="hidden" name="order_id" value="<?php echo $orderId; ?>">
                <div class="form-group">
                    <label>Cancel Reason:</label>
                    <select name="reason" class="form-control" required>
                        <option value="Customer changed mind">Customer changed mind</option>
                        <option value="Double booking">Double booking</option>
                        <option value="Price issue">Price issue</option>
                        <option value="Service unavailable">Service unavailable</option>
                        <option value="Other">Other reason</option>
                    </select>
                </div>
                <button type="submit" name="cancel_order" class="btn btn-danger">Confirm Cancel</button>
                <a href="cancel_order.php" class="btn btn-secondary ml-2">Back</a>
            </form>
        </div>
        <?php endif; ?>

        <!-- Pending Orders List -->
        <div class="card">
            <div class="card-header">Pending Orders</div>
            <?php if (empty($pendingOrders)): ?>
                <p>No pending orders</p>
            <?php else: ?>
                <?php foreach ($pendingOrders as $order): ?>
                    <div class="order-item">
                        <div class="row">
                            <div class="col-md-8">
                                <strong>Order #<?php echo $order['OrderID']; ?></strong>
                                <br>Type: <?php echo $order['OrderType']; ?>
                                <br>Time: <?php echo $order['Time']; ?>
                                <br>Remark: <?php echo $order['OrderRemark']; ?>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="cancel_order.php?orderId=<?php echo $order['OrderID']; ?>" class="btn-cancel">Cancel Order</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php include(__DIR__ . '/layout/footer.php');?>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
</body>
</html>