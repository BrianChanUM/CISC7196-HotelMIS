<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/language.php';
require_once __DIR__ . '/config/db_config.php';

$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action == 'add') {
    $itemType = isset($_POST['itemType']) ? $_POST['itemType'] : '';
    $itemName = isset($_POST['itemName']) ? $_POST['itemName'] : '';
    $itemDate = isset($_POST['itemDate']) ? $_POST['itemDate'] : '';
    $itemTime = isset($_POST['itemTime']) ? $_POST['itemTime'] : '';
    $itemGuests = isset($_POST['itemGuests']) ? intval($_POST['itemGuests']) : 1;
    $itemDetails = isset($_POST['itemDetails']) ? $_POST['itemDetails'] : '';
    
    if (empty($itemType) || empty($itemName)) {
        echo json_encode(['success' => false, 'message' => 'Invalid item data']);
        exit;
    }
    
    $realPrice = 0;
    $conn = getDBConnection();
    
    if ($conn !== null) {
        if ($itemType == 'Hotel') {
            $checkPrice = $conn->prepare("SELECT HotelRoomPrice FROM hotelroomtype WHERE HotelRoomtype = ?");
            $checkPrice->execute([$itemName]);
            $priceRow = $checkPrice->fetch();
            if ($priceRow) {
                $realPrice = floatval($priceRow['HotelRoomPrice']);
            }
        } elseif ($itemType == 'Limo') {
            try {
                $checkPrice = $conn->prepare("SELECT VehiclePrice FROM hotelvehicletype WHERE VehicleType = ?");
                $checkPrice->execute([$itemName]);
                $priceRow = $checkPrice->fetch();
                if ($priceRow && isset($priceRow['VehiclePrice'])) {
                    $realPrice = floatval($priceRow['VehiclePrice']);
                }
            } catch (PDOException $e) {
                // VehiclePrice字段可能不存在，使用默认价格
                $realPrice = 50; // 默认价格
            }
        } elseif ($itemType == 'Dining' || $itemType == 'IRD') {
            try {
                $checkPrice = $conn->prepare("SELECT Price FROM hoteloutlet WHERE OutletName = ?");
                $checkPrice->execute([$itemName]);
                $priceRow = $checkPrice->fetch();
                if ($priceRow && isset($priceRow['Price'])) {
                    $realPrice = floatval($priceRow['Price']) * $itemGuests;
                }
            } catch (PDOException $e) {
                // Price字段可能不存在，使用默认价格
                $realPrice = 30 * $itemGuests; // 默认每人$30
            }
        }
        closeDBConnection($conn);
    }
    
    if ($realPrice <= 0) {
        // 对于Dining和IRD，如果没有价格字段，使用默认价格
        if ($itemType == 'Dining' || $itemType == 'IRD') {
            $realPrice = 30 * $itemGuests; // 默认每人$30
        } else {
            echo json_encode(['success' => false, 'message' => 'Price not available for this item']);
            exit;
        }
    }
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    $itemId = uniqid('cart_');
    $cartItem = [
        'id' => $itemId,
        'type' => $itemType,
        'name' => $itemName,
        'price' => $realPrice,
        'date' => $itemDate,
        'time' => $itemTime,
        'guests' => $itemGuests,
        'details' => $itemDetails
    ];
    
    $_SESSION['cart'][] = $cartItem;
    
    echo json_encode(['success' => true, 'cartCount' => count($_SESSION['cart'])]);
    exit;
}

if ($action == 'remove') {
    $itemId = isset($_POST['itemId']) ? $_POST['itemId'] : '';
    
    if (isset($_SESSION['cart']) && !empty($itemId)) {
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['id'] == $itemId) {
                unset($_SESSION['cart'][$key]);
                break;
            }
        }
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
    
    echo json_encode(['success' => true, 'cartCount' => isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0]);
    exit;
}

if ($action == 'submit') {
    require_once __DIR__ . '/function/create_pending_order.php';
    
    // 如果用户已登录，使用session中的email，否则使用表单提交的email
    $email = isset($_SESSION['email']) && !empty($_SESSION['email']) ? $_SESSION['email'] : (isset($_POST['email']) ? $_POST['email'] : '');
    $phone = isset($_POST['phone']) ? intval($_POST['phone']) : 0;  // 确保是整数
    
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        echo json_encode(['success' => false, 'message' => 'Cart is empty']);
        exit;
    }
    
    if (empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Email is required']);
        exit;
    }
    
    if ($phone == 0) {
        echo json_encode(['success' => false, 'message' => 'Phone number is required']);
        exit;
    }
    
    try {
        // 检查库存并计算总价
        require_once __DIR__ . '/config/db_config.php';
        $conn = getDBConnection();
        
        if ($conn === null) {
            echo json_encode(['success' => false, 'message' => 'Database connection failed']);
            exit;
        }
        
        $totalAmount = 0;
        $errors = [];
        
        foreach ($_SESSION['cart'] as $item) {
            $ordertype = $item['type'];
            $itemPrice = floatval($item['price']);
            $totalAmount += $itemPrice;

            // 检查库存
            if ($ordertype == 'Hotel') {
                $checkStock = $conn->prepare("SELECT daily_quantity, HotelRoomPrice FROM hotelroomtype WHERE HotelRoomtype = ?");
                $checkStock->execute([$item['name']]);
                $stockRow = $checkStock->fetch();
                if (!$stockRow || $stockRow['daily_quantity'] <= 0) {
                    $errors[] = "No rooms available for " . htmlspecialchars($item['name']);
                }
            } elseif ($ordertype == 'Limo') {
                $checkStock = $conn->prepare("SELECT daily_quantity, VehiclePrice FROM hotelvehicletype WHERE VehicleType = ?");
                $checkStock->execute([$item['name']]);
                $stockRow = $checkStock->fetch();
                if (!$stockRow || $stockRow['daily_quantity'] <= 0) {
                    $errors[] = "No vehicles available for " . htmlspecialchars($item['name']);
                }
            } elseif ($ordertype == 'Dining' || $ordertype == 'IRD') {
                // 检查餐厅容量
                $guestsCount = intval($item['guests'] ?? 1);
                $checkStock = $conn->prepare("SELECT capacity FROM hoteloutlet WHERE OutletName = ? AND status = 1");
                $checkStock->execute([$item['name']]);
                $stockRow = $checkStock->fetch();
                if (!$stockRow || $stockRow['capacity'] < $guestsCount) {
                    $errors[] = "Not enough capacity for " . htmlspecialchars($item['name']) . ". Available: " . ($stockRow ? $stockRow['capacity'] : 0) . ", Required: " . $guestsCount;
                }
            }
        }
        
        closeDBConnection($conn);
        
        if (!empty($errors)) {
            echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
            exit;
        }
    } catch (Exception $e) {
        error_log("Submit order error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Failed to process order: ' . $e->getMessage()]);
        exit;
    }
    
    // 创建待支付订单 - 将购物车所有项目合并为一个订单
    $cartItems = $_SESSION['cart'];
    $firstItem = $cartItems[0];
    $time = $firstItem['date'] . ' ' . $firstItem['time'];
    $orderremark = "Cart Order: ";
    foreach ($cartItems as $item) {
        $orderremark .= $item['name'] . ' (' . $item['type'] . '), ';
    }
    $orderremark = rtrim($orderremark, ', ');
    
    // 合并所有项目为一个订单
    $combinedOrder = [
        'OrderType' => $firstItem['type'],
        'Time' => $time,
        'ContactNo' => $phone,
        'Email' => $email,
        'OrderRemark' => $orderremark,
        'NoofGuest' => $firstItem['guests'],
        'Amount' => $totalAmount,
        'cart_items' => $cartItems
    ];
    
    createPendingOrder($combinedOrder);
    
    // 返回需要跳转到支付页面的响应
    echo json_encode([
        'success' => true, 
        'redirect' => 'payment_simulation.php',
        'message' => 'Redirecting to payment...'
    ]);
    exit;
}

if ($action == 'getCart') {
    $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    $total = 0;
    foreach ($cart as $item) {
        $total += $item['price'];
    }
    echo json_encode([
        'success' => true, 
        'cart' => $cart, 
        'total' => $total,
        'cartCount' => count($cart)
    ]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - HotelMIS</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .cart-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
        }
        .cart-item {
            background: #fff;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .cart-item-info {
            flex: 1;
        }
        .cart-item-price {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin: 0 20px;
        }
        .cart-total {
            font-size: 24px;
            font-weight: bold;
            text-align: right;
            margin: 20px 0;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        .btn-remove {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 3px;
            cursor: pointer;
        }
        .btn-remove:hover {
            background: #c82333;
        }
        .empty-cart {
            text-align: center;
            padding: 50px;
            color: #666;
        }
        .checkout-form {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
        }
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
                <a class="navbar-brand" href="index.php"><?php echo t('hotel_management_system'); ?></a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <?php include(__DIR__ . '/layout/header.php');?>
                <ul class="nav navbar-nav navbar-right" id="navbar"></ul>
                <?php include(__DIR__ . '/layout/language_switcher.php');?>
                <?php include(__DIR__ . '/layout/navbar.php');?>
            </div>
        </div>
    </nav>
    <div class="cart-container">
        <div class="section-title">
            <h3>Shopping Cart</h3>
            <div class="clearfix"></div>
        </div>
        <div id="stockRefreshInfo" class="help-block" style="color: #008CBA; font-size: 12px; font-style: italic; margin-bottom: 15px;">
            Last refreshed: --
        </div>
        <div id="cart-items">
            <div class="empty-cart">
                <p>Your cart is empty. Browse our services to add items.</p>
                <a href="index.php" class="btn btn-primary">Browse Services</a>
            </div>
        </div>
        <div id="cart-summary" style="display: none;">
            <div class="cart-total">
                Total: $<span id="cart-total-amount">0.00</span>
            </div>
            <div class="checkout-form">
                <h4>Checkout Information</h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="checkoutEmail">Email</label>
                            <input type="email" class="form-control" id="checkoutEmail" placeholder="Your email" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="checkoutPhone">Phone</label>
                            <input type="text" class="form-control" id="checkoutPhone" placeholder="Your phone number">
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-success btn-lg" id="submitOrderBtn">Submit Order</button>
            </div>
        </div>
    </div>
    <?php include(__DIR__ . '/layout/footer.php');?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery.1.11.1.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script src="js/cart.js"></script>
    <script>
        $(document).ready(function() {
            setInterval(refreshCartStock, 120000);
            refreshCartStock();
        });
        
        function refreshCartStock() {
            $.ajax({
                url: 'function/get_available_stock.php',
                type: 'GET',
                data: { action: 'get_stock' },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#stockRefreshInfo').text('Last refreshed: ' + response.timestamp);
                        
                        $('.cart-item').each(function() {
                            var itemName = $(this).find('.cart-item-info p:contains(Service:)').text().replace('Service: ', '');
                            var itemType = $(this).find('.cart-item-info h4').text();
                            
                            var stockData = itemType === 'Hotel' ? response.hotel_stock : response.vehicle_stock;
                            var available = stockData[itemName] !== undefined ? stockData[itemName] : 0;
                            
                            if (available <= 0) {
                                $(this).addClass('out-of-stock');
                                if (!$(this).find('.stock-warning').length) {
                                    $(this).prepend('<div class="stock-warning alert alert-danger" style="padding: 5px 10px; margin-bottom: 10px;">Warning: This item is no longer available!</div>');
                                }
                            } else {
                                $(this).removeClass('out-of-stock');
                                $(this).find('.stock-warning').remove();
                            }
                        });
                    }
                },
                error: function() {
                    console.error('Failed to refresh stock');
                }
            });
        }
    </script>
    <style>
        .cart-item.out-of-stock {
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        .stock-warning {
            font-size: 12px;
        }
    </style>
</body>
</html>