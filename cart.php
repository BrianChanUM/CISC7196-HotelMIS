<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/language.php';

$servername = "localhost";
$username = "root";
$password = "123456";
$dbname = "hmis";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action == 'add') {
    $itemType = isset($_POST['itemType']) ? $_POST['itemType'] : '';
    $itemName = isset($_POST['itemName']) ? $_POST['itemName'] : '';
    $itemPrice = isset($_POST['itemPrice']) ? floatval($_POST['itemPrice']) : 0;
    $itemDate = isset($_POST['itemDate']) ? $_POST['itemDate'] : '';
    $itemTime = isset($_POST['itemTime']) ? $_POST['itemTime'] : '';
    $itemGuests = isset($_POST['itemGuests']) ? intval($_POST['itemGuests']) : 1;
    $itemDetails = isset($_POST['itemDetails']) ? $_POST['itemDetails'] : '';
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    $itemId = count($_SESSION['cart']) + 1;
    $cartItem = [
        'id' => $itemId,
        'type' => $itemType,
        'name' => $itemName,
        'price' => $itemPrice,
        'date' => $itemDate,
        'time' => $itemTime,
        'guests' => $itemGuests,
        'details' => $itemDetails
    ];
    
    $_SESSION['cart'][] = $cartItem;
    
    echo json_encode(['success' => true, 'cartCount' => count($_SESSION['cart'])]);
    $conn->close();
    exit;
}

if ($action == 'remove') {
    $itemId = isset($_POST['itemId']) ? intval($_POST['itemId']) : 0;
    
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['id'] == $itemId) {
                unset($_SESSION['cart'][$key]);
                break;
            }
        }
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
    
    echo json_encode(['success' => true, 'cartCount' => isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0]);
    $conn->close();
    exit;
}

if ($action == 'submit') {
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    
    $errors = [];
    $successCount = 0;
    
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        echo json_encode(['success' => false, 'message' => 'Cart is empty']);
        $conn->close();
        exit;
    }
    
    if (empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Email is required']);
        $conn->close();
        exit;
    }
    
    $status = "TBC";
    $ordercreateddate = date('Y-m-d H:i:s');
    $ordermodifieddate = date('Y-m-d H:i:s');
    
    foreach ($_SESSION['cart'] as $item) {
        $ordertype = $item['type'];
        $time = $item['date'] . ' ' . $item['time'];
        $orderremark = $item['name'] . ' | ' . $item['details'];
        $noofguest = $item['guests'];
        
        if ($ordertype == 'Hotel') {
            $checkStock = $conn->prepare("SELECT daily_quantity FROM hotelroomtype WHERE HotelRoomtype = ?");
            $checkStock->bind_param("s", $item['name']);
            $checkStock->execute();
            $stockResult = $checkStock->get_result();
            if ($stockResult->num_rows > 0) {
                $stockRow = $stockResult->fetch_assoc();
                if ($stockRow['daily_quantity'] > 0) {
                    $deductStock = $conn->prepare("UPDATE hotelroomtype SET daily_quantity = daily_quantity - 1 WHERE HotelRoomtype = ?");
                    $deductStock->bind_param("s", $item['name']);
                    $deductStock->execute();
                    $deductStock->close();
                } else {
                    $errors[] = "No rooms available for " . $item['name'];
                    $checkStock->close();
                    continue;
                }
            }
            $checkStock->close();
        } elseif ($ordertype == 'Limo') {
            $checkStock = $conn->prepare("SELECT daily_quantity FROM hotelvehicletype WHERE VehicleType = ?");
            $checkStock->bind_param("s", $item['name']);
            $checkStock->execute();
            $stockResult = $checkStock->get_result();
            if ($stockResult->num_rows > 0) {
                $stockRow = $stockResult->fetch_assoc();
                if ($stockRow['daily_quantity'] > 0) {
                    $deductStock = $conn->prepare("UPDATE hotelvehicletype SET daily_quantity = daily_quantity - 1 WHERE VehicleType = ?");
                    $deductStock->bind_param("s", $item['name']);
                    $deductStock->execute();
                    $deductStock->close();
                } else {
                    $errors[] = "No vehicles available for " . $item['name'];
                    $checkStock->close();
                    continue;
                }
            }
            $checkStock->close();
        }
        
        $stmt = $conn->prepare("INSERT INTO orderbookings (OrderType, Time, ContactNo, Email, OrderRemark, Status, OrderCreatedDate, OrderModifiedDate, NoofGuest) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssi", $ordertype, $time, $phone, $email, $orderremark, $status, $ordercreateddate, $ordermodifieddate, $noofguest);
        
        if ($stmt->execute()) {
            $successCount++;
        } else {
            $errors[] = "Failed to add order for " . $item['name'];
        }
        $stmt->close();
    }
    
    if ($successCount > 0) {
        $_SESSION['cart'] = [];
        echo json_encode(['success' => true, 'message' => "Successfully submitted $successCount order(s)"]);
    } else {
        echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    }
    
    $conn->close();
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
    $conn->close();
    exit;
}

$conn->close();
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
</body>
</html>
