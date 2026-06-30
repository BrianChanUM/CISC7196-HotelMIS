<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/language.php';
require_once __DIR__ . '/config/db_config.php';

// 处理支付成功回调
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'process_payment') {
    $pendingOrder = isset($_SESSION['pending_order']) ? $_SESSION['pending_order'] : null;

    // 调试日志：记录 pending_order 数据
    $debugLog = "[" . date('Y-m-d H:i:s') . "] Payment Processing\n";
    $debugLog .= "Pending Order Data: " . print_r($pendingOrder, true) . "\n";
    $debugLog .= "Session Data: " . print_r($_SESSION, true) . "\n";
    file_put_contents(__DIR__ . '/logs/payment_debug.log', $debugLog, FILE_APPEND);

    if (!$pendingOrder) {
        $_SESSION['payment_error'] = t('no_pending_order');
        $_SESSION['payment_order_id'] = null;
        header("Location: payment_simulation.php");
        exit;
    }

    $paymentMethod = isset($_POST['payment_method']) ? $_POST['payment_method'] : 'Cash';
    $paymentStatus = 'Paid';
    $paymentTime = date('Y-m-d H:i:s');

    $conn = getDBConnection();
    $status = "TBC";
    $ordercreateddate = date('Y-m-d H:i:s');
    $ordermodifieddate = date('Y-m-d H:i:s');
    $successCount = 0;
    $last_id = 0;

    try {
        $conn->beginTransaction();

        // 调试日志：判断订单类型
        $isCartOrder = isset($pendingOrder['cart_items']) && is_array($pendingOrder['cart_items']) && !empty($pendingOrder['cart_items']);
        $debugLog = "[" . date('Y-m-d H:i:s') . "] Order Type Determination: isCartOrder=" . ($isCartOrder ? "true" : "false") . ", cart_items count=" . (isset($pendingOrder['cart_items']) ? count($pendingOrder['cart_items']) : 0) . "\n";
        file_put_contents(__DIR__ . '/logs/payment_debug.log', $debugLog, FILE_APPEND);

        // 处理购物车订单（多个项目）
        if ($isCartOrder) {
            foreach ($pendingOrder['cart_items'] as $item) {
                $ordertype = $item['type'];
                $time = $item['date'] . ' ' . $item['time'];
                $orderremark = $item['name'] . ' | ' . $item['details'];
                $noofguest = $item['guests'];
                
                if ($ordertype == 'Hotel') {
                    $checkStock = $conn->prepare("SELECT daily_quantity FROM hotelroomtype WHERE HotelRoomtype = ? FOR UPDATE");
                    $checkStock->execute([$item['name']]);
                    $stockRow = $checkStock->fetch();
                    
                    if (!$stockRow || $stockRow['daily_quantity'] <= 0) {
                        throw new Exception('No rooms available for ' . $item['name']);
                    }
                    
                    $deductStock = $conn->prepare("UPDATE hotelroomtype SET daily_quantity = daily_quantity - 1 WHERE HotelRoomtype = ? AND daily_quantity > 0");
                    $deductStock->execute([$item['name']]);
                    
                    if ($deductStock->rowCount() === 0) {
                        throw new Exception('Failed to deduct stock for ' . $item['name']);
                    }
                } elseif ($ordertype == 'Limo') {
                    $checkStock = $conn->prepare("SELECT daily_quantity FROM hotelvehicletype WHERE VehicleType = ? FOR UPDATE");
                    $checkStock->execute([$item['name']]);
                    $stockRow = $checkStock->fetch();

                    if (!$stockRow || $stockRow['daily_quantity'] <= 0) {
                        throw new Exception('No vehicles available for ' . $item['name']);
                    }

                    $deductStock = $conn->prepare("UPDATE hotelvehicletype SET daily_quantity = daily_quantity - 1 WHERE VehicleType = ? AND daily_quantity > 0");
                    $deductStock->execute([$item['name']]);

                    if ($deductStock->rowCount() === 0) {
                        throw new Exception('Failed to deduct stock for ' . $item['name']);
                    }
                } elseif ($ordertype == 'Dining' || $ordertype == 'IRD') {
                    // 餐厅和客房送餐按客人数量扣减容量
                    $guestsCount = intval($item['guests']);
                    $checkStock = $conn->prepare("SELECT capacity FROM hoteloutlet WHERE OutletName = ? FOR UPDATE");
                    $checkStock->execute([$item['name']]);
                    $stockRow = $checkStock->fetch();

                    if (!$stockRow || $stockRow['capacity'] < $guestsCount) {
                        throw new Exception('Not enough capacity for ' . $item['name'] . '. Available: ' . ($stockRow ? $stockRow['capacity'] : 0) . ', Required: ' . $guestsCount);
                    }

                    $deductStock = $conn->prepare("UPDATE hoteloutlet SET capacity = capacity - ? WHERE OutletName = ? AND capacity >= ?");
                    $deductStock->execute([$guestsCount, $item['name'], $guestsCount]);

                    if ($deductStock->rowCount() === 0) {
                        throw new Exception('Failed to deduct capacity for ' . $item['name']);
                    }
                }
                
                $stmt = $conn->prepare("INSERT INTO orderbookings (OrderType, Time, ContactNo, Email, OrderRemark, Status, OrderCreatedDate, OrderModifiedDate, NoofGuest, isRequired, AssignedTo, PaymentStatus, PaymentMethod, PaymentTime) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                $params = [$ordertype, $time, intval($pendingOrder['ContactNo']), $pendingOrder['Email'], $orderremark, $status, $ordercreateddate, $ordermodifieddate, intval($noofguest), 0, '', $paymentStatus, $paymentMethod, $paymentTime];

                // 调试日志：记录SQL参数
                $debugLog = "[" . date('Y-m-d H:i:s') . "] SQL Insert Parameters: " . print_r($params, true) . "\n";
                file_put_contents(__DIR__ . '/logs/payment_debug.log', $debugLog, FILE_APPEND);

                $stmt->execute($params);

                if ($stmt->rowCount() > 0) {
                    $successCount++;
                    $last_id = $conn->lastInsertId();
                    // 调试日志：记录成功插入
                    $debugLog = "[" . date('Y-m-d H:i:s') . "] Order inserted successfully. OrderID: $last_id\n";
                    file_put_contents(__DIR__ . '/logs/payment_debug.log', $debugLog, FILE_APPEND);
                } else {
                    // 获取PDO错误信息
                    $errorInfo = $stmt->errorInfo();
                    $errorMsg = 'Failed to insert order. SQL Error: ' . $errorInfo[2] . ' | Error Code: ' . $errorInfo[0] . '-' . $errorInfo[1];
                    $errorMsg .= ' | Data: OrderType=' . $ordertype . ', ContactNo=' . $pendingOrder['ContactNo'] . ' (intval: ' . intval($pendingOrder['ContactNo']) . ')';
                    $errorMsg .= ', NoofGuest=' . $noofguest . ' (intval: ' . intval($noofguest) . ')';
                    // 调试日志：记录失败详情
                    file_put_contents(__DIR__ . '/logs/payment_debug.log', "[" . date('Y-m-d H:i:s') . "] " . $errorMsg . "\n", FILE_APPEND);
                    throw new Exception($errorMsg);
                }
            }
        } else {
            // 处理单个订单
            // 调试日志：记录单个订单处理开始
            $debugLog = "[" . date('Y-m-d H:i:s') . "] Processing single order: OrderType=" . $pendingOrder['OrderType'] . "\n";
            file_put_contents(__DIR__ . '/logs/payment_debug.log', $debugLog, FILE_APPEND);

            if ($pendingOrder['OrderType'] == 'Hotel') {
                $hotelType = $pendingOrder['hotel_type'] ?? '';
                // 调试日志：记录酒店类型
                $debugLog = "[" . date('Y-m-d H:i:s') . "] Hotel booking: hotel_type='$hotelType'\n";
                file_put_contents(__DIR__ . '/logs/payment_debug.log', $debugLog, FILE_APPEND);

                $checkStock = $conn->prepare("SELECT daily_quantity FROM hotelroomtype WHERE HotelRoomtype = ? FOR UPDATE");
                $checkStock->execute([$hotelType]);
                $stockRow = $checkStock->fetch();

                // 调试日志：记录库存查询结果
                $debugLog = "[" . date('Y-m-d H:i:s') . "] Hotel stock check: " . ($stockRow ? "Found, daily_quantity=" . $stockRow['daily_quantity'] : "Not found") . "\n";
                file_put_contents(__DIR__ . '/logs/payment_debug.log', $debugLog, FILE_APPEND);

                if (!$stockRow || $stockRow['daily_quantity'] <= 0) {
                    $errorMsg = 'No rooms available for ' . $hotelType . ' (Stock: ' . ($stockRow ? $stockRow['daily_quantity'] : 'N/A') . ')';
                    file_put_contents(__DIR__ . '/logs/payment_debug.log', "[" . date('Y-m-d H:i:s') . "] ERROR: $errorMsg\n", FILE_APPEND);
                    throw new Exception($errorMsg);
                }

                $deductStock = $conn->prepare("UPDATE hotelroomtype SET daily_quantity = daily_quantity - 1 WHERE HotelRoomtype = ? AND daily_quantity > 0");
                $deductStock->execute([$hotelType]);

                if ($deductStock->rowCount() === 0) {
                    $errorMsg = 'Failed to deduct stock for ' . $hotelType;
                    file_put_contents(__DIR__ . '/logs/payment_debug.log', "[" . date('Y-m-d H:i:s') . "] ERROR: $errorMsg\n", FILE_APPEND);
                    throw new Exception($errorMsg);
                }

            } elseif ($pendingOrder['OrderType'] == 'Limo') {
                $vehicleType = $pendingOrder['vehicle_type'] ?? '';
                // 调试日志：记录车辆类型
                $debugLog = "[" . date('Y-m-d H:i:s') . "] Limo booking: vehicle_type='$vehicleType'\n";
                file_put_contents(__DIR__ . '/logs/payment_debug.log', $debugLog, FILE_APPEND);

                $checkStock = $conn->prepare("SELECT daily_quantity FROM hotelvehicletype WHERE VehicleType = ? FOR UPDATE");
                $checkStock->execute([$vehicleType]);
                $stockRow = $checkStock->fetch();

                // 调试日志：记录库存查询结果
                $debugLog = "[" . date('Y-m-d H:i:s') . "] Vehicle stock check: " . ($stockRow ? "Found, daily_quantity=" . $stockRow['daily_quantity'] : "Not found") . "\n";
                file_put_contents(__DIR__ . '/logs/payment_debug.log', $debugLog, FILE_APPEND);

                if (!$stockRow || $stockRow['daily_quantity'] <= 0) {
                    $errorMsg = 'No vehicles available for ' . $vehicleType . ' (Stock: ' . ($stockRow ? $stockRow['daily_quantity'] : 'N/A') . ')';
                    file_put_contents(__DIR__ . '/logs/payment_debug.log', "[" . date('Y-m-d H:i:s') . "] ERROR: $errorMsg\n", FILE_APPEND);
                    throw new Exception($errorMsg);
                }

                $deductStock = $conn->prepare("UPDATE hotelvehicletype SET daily_quantity = daily_quantity - 1 WHERE VehicleType = ? AND daily_quantity > 0");
                $deductStock->execute([$vehicleType]);

                if ($deductStock->rowCount() === 0) {
                    $errorMsg = 'Failed to deduct stock for ' . $vehicleType;
                    file_put_contents(__DIR__ . '/logs/payment_debug.log', "[" . date('Y-m-d H:i:s') . "] ERROR: $errorMsg\n", FILE_APPEND);
                    throw new Exception($errorMsg);
                }
            } elseif ($pendingOrder['OrderType'] == 'Dining' || $pendingOrder['OrderType'] == 'IRD') {
                // 餐厅和客房送餐按客人数量扣减容量
                $guestsCount = intval($pendingOrder['NoofGuest'] ?? 1);
                $outletName = $pendingOrder['outlet_name'] ?? '';
                // 调试日志：记录餐厅信息
                $debugLog = "[" . date('Y-m-d H:i:s') . "] Dining/IRD booking: outlet_name='$outletName', guests=$guestsCount\n";
                file_put_contents(__DIR__ . '/logs/payment_debug.log', $debugLog, FILE_APPEND);

                $checkStock = $conn->prepare("SELECT capacity FROM hoteloutlet WHERE OutletName = ? FOR UPDATE");
                $checkStock->execute([$outletName]);
                $stockRow = $checkStock->fetch();

                // 调试日志：记录容量查询结果
                $debugLog = "[" . date('Y-m-d H:i:s') . "] Outlet capacity check: " . ($stockRow ? "Found, capacity=" . $stockRow['capacity'] : "Not found") . "\n";
                file_put_contents(__DIR__ . '/logs/payment_debug.log', $debugLog, FILE_APPEND);

                if (!$stockRow || $stockRow['capacity'] < $guestsCount) {
                    $errorMsg = 'Not enough capacity for ' . $outletName . '. Available: ' . ($stockRow ? $stockRow['capacity'] : 0) . ', Required: ' . $guestsCount;
                    file_put_contents(__DIR__ . '/logs/payment_debug.log', "[" . date('Y-m-d H:i:s') . "] ERROR: $errorMsg\n", FILE_APPEND);
                    throw new Exception($errorMsg);
                }

                $deductStock = $conn->prepare("UPDATE hoteloutlet SET capacity = capacity - ? WHERE OutletName = ? AND capacity >= ?");
                $deductStock->execute([$guestsCount, $outletName, $guestsCount]);

                if ($deductStock->rowCount() === 0) {
                    $errorMsg = 'Failed to deduct capacity for ' . $outletName;
                    file_put_contents(__DIR__ . '/logs/payment_debug.log', "[" . date('Y-m-d H:i:s') . "] ERROR: $errorMsg\n", FILE_APPEND);
                    throw new Exception($errorMsg);
                }
            }
            
            $stmt = $conn->prepare("INSERT INTO orderbookings (OrderType, Time, ContactNo, Email, OrderRemark, Status, OrderCreatedDate, OrderModifiedDate, NoofGuest, isRequired, AssignedTo, PaymentStatus, PaymentMethod, PaymentTime) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $params = [
                $pendingOrder['OrderType'],
                $pendingOrder['Time'],
                intval($pendingOrder['ContactNo']),
                $pendingOrder['Email'],
                $pendingOrder['OrderRemark'],
                $status,
                $ordercreateddate,
                $ordermodifieddate,
                intval($pendingOrder['NoofGuest']),
                0,
                '',
                $paymentStatus,
                $paymentMethod,
                $paymentTime
            ];

            // 调试日志：记录SQL参数（单个订单）
            $debugLog = "[" . date('Y-m-d H:i:s') . "] Single Order SQL Parameters: " . print_r($params, true) . "\n";
            file_put_contents(__DIR__ . '/logs/payment_debug.log', $debugLog, FILE_APPEND);

            $stmt->execute($params);

            if ($stmt->rowCount() > 0) {
                $successCount = 1;
                $last_id = $conn->lastInsertId();
                // 调试日志：记录成功插入
                $debugLog = "[" . date('Y-m-d H:i:s') . "] Single order inserted successfully. OrderID: $last_id\n";
                file_put_contents(__DIR__ . '/logs/payment_debug.log', $debugLog, FILE_APPEND);
            } else {
                // 获取PDO错误信息
                $errorInfo = $stmt->errorInfo();
                $errorMsg = 'Failed to insert single order. SQL Error: ' . $errorInfo[2] . ' | Error Code: ' . $errorInfo[0] . '-' . $errorInfo[1];
                $errorMsg .= ' | Data: OrderType=' . $pendingOrder['OrderType'] . ', ContactNo=' . $pendingOrder['ContactNo'] . ' (intval: ' . intval($pendingOrder['ContactNo']) . ')';
                $errorMsg .= ', NoofGuest=' . $pendingOrder['NoofGuest'] . ' (intval: ' . intval($pendingOrder['NoofGuest']) . ')';
                // 调试日志：记录失败详情
                file_put_contents(__DIR__ . '/logs/payment_debug.log', "[" . date('Y-m-d H:i:s') . "] " . $errorMsg . "\n", FILE_APPEND);
                throw new Exception($errorMsg);
            }
        }
        
        $conn->commit();
        closeDBConnection($conn);
        
        if ($successCount > 0) {
            // 确保购物车和待支付订单被清空
            $_SESSION['cart'] = [];
            unset($_SESSION['pending_order']);
            
            // 清空Session中可能残留的其他购物车相关变量
            if (isset($_SESSION['booking_token'])) {
                unset($_SESSION['booking_token']);
            }
            if (isset($_SESSION['fnb_booking_token'])) {
                unset($_SESSION['fnb_booking_token']);
            }
            if (isset($_SESSION['limo_booking_token'])) {
                unset($_SESSION['limo_booking_token']);
            }
            if (isset($_SESSION['ird_booking_token'])) {
                unset($_SESSION['ird_booking_token']);
            }
            
            $_SESSION['payment_success'] = t('payment_success') . $last_id;
            $_SESSION['payment_order_id'] = $last_id;
            $_SESSION['payment_order_type'] = $pendingOrder['OrderType'];
            $_SESSION['payment_amount'] = $amount;
            $_SESSION['payment_method'] = $paymentMethod;
            $_SESSION['payment_time'] = $paymentTime;

            header("Location: payment_simulation.php");
            exit;
        } else {
            throw new Exception('Failed to create order');
        }
        
    } catch (Exception $e) {
        try {
            $conn->rollBack();
        } catch (Exception $rollBackEx) {
            // 忽略 rollback 错误，可能是连接问题或没有活跃事务
        }
        if (isset($conn)) {
            closeDBConnection($conn);
        }

        // 调试日志：记录异常详情
        $errorMsg = $e->getMessage();
        $errorTrace = $e->getTraceAsString();
        $debugLog = "[" . date('Y-m-d H:i:s') . "] EXCEPTION CAUGHT: $errorMsg\n";
        $debugLog .= "Stack Trace:\n$errorTrace\n";
        file_put_contents(__DIR__ . '/logs/payment_debug.log', $debugLog, FILE_APPEND);

        $_SESSION['payment_error'] = $errorMsg;
        $_SESSION['payment_order_id'] = null;
        header("Location: payment_simulation.php");
        exit;
    }
}

// 获取待支付订单
$pendingOrder = isset($_SESSION['pending_order']) ? $_SESSION['pending_order'] : null;

// 检查支付状态
$paymentSuccess = isset($_SESSION['payment_success']) ? $_SESSION['payment_success'] : null;
$paymentError = isset($_SESSION['payment_error']) ? $_SESSION['payment_error'] : null;
$paymentOrderId = isset($_SESSION['payment_order_id']) ? $_SESSION['payment_order_id'] : null;
$paymentOrderType = isset($_SESSION['payment_order_type']) ? $_SESSION['payment_order_type'] : null;
$paymentAmount = isset($_SESSION['payment_amount']) ? $_SESSION['payment_amount'] : null;
$paymentMethod = isset($_SESSION['payment_method']) ? $_SESSION['payment_method'] : null;
$paymentTime = isset($_SESSION['payment_time']) ? $_SESSION['payment_time'] : null;

// 如果没有待支付订单，且没有支付结果，则跳转到首页
if (!$pendingOrder && !$paymentSuccess && !$paymentError) {
    header("Location: index.php");
    exit;
}

// 获取订单金额显示
$amount = $pendingOrder ? (isset($pendingOrder['Amount']) ? $pendingOrder['Amount'] : 0) : $paymentAmount;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo t('payment_simulation'); ?> - HotelMIS</title>
    
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    
    <style>
        .payment-container {
            max-width: 600px;
            margin: 100px auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .payment-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
        }
        .order-summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .order-summary h4 {
            margin-bottom: 15px;
            color: #333;
        }
        .order-summary .row {
            margin-bottom: 10px;
        }
        .payment-methods {
            margin-bottom: 30px;
        }
        .payment-method {
            display: flex;
            align-items: center;
            padding: 15px;
            margin-bottom: 10px;
            border: 2px solid #ddd;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .payment-method:hover {
            border-color: #008CBA;
            background: #f0f8ff;
        }
        .payment-method.selected {
            border-color: #008CBA;
            background: #e0f4f8;
        }
        .payment-method input {
            margin-right: 15px;
            transform: scale(1.5);
        }
        .payment-method i {
            font-size: 24px;
            margin-right: 15px;
            color: #666;
            width: 40px;
            text-align: center;
        }
        .payment-method .method-name {
            flex: 1;
            font-weight: 500;
        }
        .payment-method .method-desc {
            color: #999;
            font-size: 12px;
        }
        .btn-pay {
            width: 100%;
            padding: 15px;
            font-size: 18px;
            background: #008CBA;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-pay:hover {
            background: #006f9a;
        }
        .btn-pay:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .amount-display {
            font-size: 36px;
            color: #008CBA;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
        }
        .processing-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        .processing-overlay.active {
            display: flex;
        }
        .spinner {
            width: 60px;
            height: 60px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #008CBA;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .processing-text {
            color: white;
            margin-top: 20px;
            font-size: 18px;
        }
        .result-container {
            text-align: center;
            padding: 40px 20px;
        }
        .result-icon {
            font-size: 80px;
            margin-bottom: 20px;
        }
        .result-icon.success {
            color: #28a745;
        }
        .result-icon.error {
            color: #dc3545;
        }
        .result-title {
            font-size: 28px;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .result-title.success {
            color: #28a745;
        }
        .result-title.error {
            color: #dc3545;
        }
        .result-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: left;
        }
        .result-details .row {
            margin-bottom: 10px;
        }
        .btn-home {
            display: inline-block;
            padding: 12px 30px;
            background: #008CBA;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 20px;
        }
        .btn-home:hover {
            background: #006f9a;
            color: white;
        }
    </style>
</head>
<body>
    <nav id="tf-menu" class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <a class="navbar-brand" href="index.php"><?php echo t('hotel_management_system'); ?></a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="payment-container">
            <?php if ($paymentSuccess): ?>
                <!-- 支付成功页面 -->
                <div class="result-container">
                    <div class="result-icon success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h2 class="result-title success"><?php echo t('payment_successful'); ?></h2>
                    <p class="text-muted"><?php echo $paymentSuccess; ?></p>

                    <div class="result-details">
                        <div class="row">
                            <div class="col-md-4"><strong><?php echo t('order_id'); ?>:</strong></div>
                            <div class="col-md-8">#<?php echo htmlspecialchars($paymentOrderId); ?></div>
                        </div>
                        <?php if ($paymentOrderType): ?>
                        <div class="row">
                            <div class="col-md-4"><strong><?php echo t('order_type'); ?>:</strong></div>
                            <div class="col-md-8"><?php echo htmlspecialchars($paymentOrderType); ?></div>
                        </div>
                        <?php endif; ?>
                        <?php if ($paymentAmount): ?>
                        <div class="row">
                            <div class="col-md-4"><strong><?php echo t('amount'); ?>:</strong></div>
                            <div class="col-md-8"><?php echo t('currency_symbol'); ?><?php echo number_format($paymentAmount, 2); ?></div>
                        </div>
                        <?php endif; ?>
                        <?php if ($paymentMethod): ?>
                        <div class="row">
                            <div class="col-md-4"><strong><?php echo t('payment_method'); ?>:</strong></div>
                            <div class="col-md-8"><?php echo htmlspecialchars($paymentMethod); ?></div>
                        </div>
                        <?php endif; ?>
                        <?php if ($paymentTime): ?>
                        <div class="row">
                            <div class="col-md-4"><strong><?php echo t('payment_time'); ?>:</strong></div>
                            <div class="col-md-8"><?php echo htmlspecialchars($paymentTime); ?></div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <a href="index.php" class="btn-home">
                        <i class="fas fa-home"></i> <?php echo t('back_to_home'); ?>
                    </a>
                </div>
                <?php
                // 清除支付成功的session变量，避免刷新时重复显示
                unset($_SESSION['payment_success']);
                unset($_SESSION['payment_order_id']);
                unset($_SESSION['payment_order_type']);
                unset($_SESSION['payment_amount']);
                unset($_SESSION['payment_method']);
                unset($_SESSION['payment_time']);
                ?>

            <?php elseif ($paymentError): ?>
                <!-- 支付失败页面 -->
                <div class="result-container">
                    <div class="result-icon error">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <h2 class="result-title error"><?php echo t('payment_failed'); ?></h2>
                    <p class="text-muted"><?php echo htmlspecialchars($paymentError); ?></p>

                    <div class="result-details">
                        <p><strong><?php echo t('error_message'); ?>:</strong></p>
                        <p><?php echo htmlspecialchars($paymentError); ?></p>
                    </div>

                    <div style="margin-top: 30px;">
                        <a href="javascript:history.back()" class="btn-home" style="margin-right: 10px;">
                            <i class="fas fa-arrow-left"></i> <?php echo t('back_to_booking'); ?>
                        </a>
                        <a href="index.php" class="btn-home" style="background: #6c757d;">
                            <i class="fas fa-home"></i> <?php echo t('back_to_home'); ?>
                        </a>
                    </div>
                </div>
                <?php
                // 清除支付失败的session变量
                unset($_SESSION['payment_error']);
                ?>

            <?php else: ?>
                <!-- 支付表单 -->
            <div class="payment-header">
                <h2><i class="fas fa-credit-card"></i> <?php echo t('payment_simulation'); ?></h2>
                <p class="text-muted"><?php echo t('confirm_your_order'); ?></p>
            </div>
            
            <div class="order-summary">
                <h4><i class="fas fa-receipt"></i> <?php echo t('order_summary'); ?></h4>
                <div class="row">
                    <div class="col-md-4"><strong><?php echo t('order_type'); ?>:</strong></div>
                    <div class="col-md-8"><?php echo htmlspecialchars($pendingOrder['OrderType']); ?></div>
                </div>
                <div class="row">
                    <div class="col-md-4"><strong><?php echo t('booking_time'); ?>:</strong></div>
                    <div class="col-md-8"><?php echo htmlspecialchars($pendingOrder['Time']); ?></div>
                </div>
                <div class="row">
                    <div class="col-md-4"><strong><?php echo t('guests'); ?>:</strong></div>
                    <div class="col-md-8"><?php echo htmlspecialchars($pendingOrder['NoofGuest']); ?></div>
                </div>
                <div class="row">
                    <div class="col-md-4"><strong><?php echo t('remark'); ?>:</strong></div>
                    <div class="col-md-8"><?php echo htmlspecialchars($pendingOrder['OrderRemark']); ?></div>
                </div>
                <hr>
                <div class="amount-display">
                    <?php echo t('currency_symbol'); ?><?php echo number_format($amount, 2); ?>
                </div>
            </div>
            
            <div class="payment-methods">
                <h4><?php echo t('select_payment_method'); ?></h4>
                
                <form id="paymentForm" method="POST" action="">
                    <input type="hidden" name="action" value="process_payment">
                    
                    <label class="payment-method selected">
                        <input type="radio" name="payment_method" value="Credit Card" checked>
                        <i class="fas fa-credit-card"></i>
                        <span class="method-name"><?php echo t('credit_card'); ?></span>
                        <span class="method-desc"><?php echo t('pay_with_card'); ?></span>
                    </label>
                    
                    <label class="payment-method">
                        <input type="radio" name="payment_method" value="Alipay">
                        <i class="fab fa-alipay" style="color: #1675e0;"></i>
                        <span class="method-name"><?php echo t('alipay'); ?></span>
                        <span class="method-desc"><?php echo t('pay_with_alipay'); ?></span>
                    </label>
                    
                    <label class="payment-method">
                        <input type="radio" name="payment_method" value="WeChat Pay">
                        <i class="fab fa-weixin" style="color: #07c160;"></i>
                        <span class="method-name"><?php echo t('wechat_pay'); ?></span>
                        <span class="method-desc"><?php echo t('pay_with_wechat'); ?></span>
                    </label>
                    
                    <label class="payment-method">
                        <input type="radio" name="payment_method" value="Cash">
                        <i class="fas fa-money-bill-wave" style="color: #28a745;"></i>
                        <span class="method-name"><?php echo t('cash'); ?></span>
                        <span class="method-desc"><?php echo t('pay_with_cash'); ?></span>
                    </label>
                </form>
            </div>
            
            <button type="button" class="btn-pay" onclick="processPayment()">
                <i class="fas fa-lock"></i> <?php echo t('confirm_payment'); ?>
            </button>
            
            <div class="text-center" style="margin-top: 20px;">
                <a href="javascript:history.back()" class="text-muted">
                    <i class="fas fa-arrow-left"></i> <?php echo t('back_to_booking'); ?>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="processing-overlay" id="processingOverlay">
        <div class="spinner"></div>
        <div class="processing-text"><?php echo t('processing_payment'); ?>...</div>
    </div>

    <script>
        // 选择支付方式
        document.querySelectorAll('.payment-method').forEach(function(method) {
            method.addEventListener('click', function() {
                document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('selected'));
                this.classList.add('selected');
                this.querySelector('input').checked = true;
            });
        });
        
        function processPayment() {
            document.getElementById('processingOverlay').classList.add('active');
            document.getElementById('paymentForm').submit();
        }
    </script>
</body>
</html>
