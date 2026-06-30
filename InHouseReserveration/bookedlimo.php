<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/session_check.php';
require_once __DIR__ . '/config/language.php';
require_once __DIR__ . '/config/db_config.php';

function checkPermission($module, $permissionType) {
    if (!isset($_SESSION['permissions'])) {
        return false;
    }
    $key = $module . '_' . $permissionType;
    return isset($_SESSION['permissions'][$key]) && $_SESSION['permissions'][$key];
}

$showLimoForm = true;
$limoAccessMessage = "";

if (!isset($_SESSION['username'])) {
    $showLimoForm = false;
    $limoAccessMessage = "Please login first.";
} else {
    $userRole = $_SESSION['role'];
    if ($userRole != 'admin') {
        if (!checkPermission('limo_service', 'create')) {
            $showLimoForm = false;
            $limoAccessMessage = "You do not have permission to book limo service.";
        }
    }
}

$limoImages = glob(__DIR__ . '/img/limo/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
$limoImagePaths = [];
foreach ($limoImages as $img) {
    $limoImagePaths[] = 'img/limo/' . basename($img);
}
$limoImagePathsJSON = json_encode($limoImagePaths);

// 处理支付成功回调
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'limo_payment_return') {
    require_once __DIR__ . '/config/db_config.php';
    
    $conn = getDBConnection();
    $status = "TBC";
    $ordercreateddate = date('Y-m-d H:i:s');
    $ordermodifieddate = date('Y-m-d H:i:s');
    $paymentStatus = 'Paid';
    $paymentMethod = isset($_POST['payment_method']) ? $_POST['payment_method'] : 'Cash';
    $paymentTime = date('Y-m-d H:i:s');
    
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $vehicleType = $_POST['vehicleType'];
    $orderremark = $vehicleType . ' | ' . (isset($_POST['comment']) ? $_POST['comment'] : '');
    $noofguest = isset($_POST['guests']) ? $_POST['guests'] : '';
    $bookingDate = isset($_POST['bookingDate']) ? $_POST['bookingDate'] : '';
    $bookingTime = isset($_POST['bookingTime']) ? $_POST['bookingTime'] : '';
    $datetime = new DateTime($bookingDate . ' ' . $bookingTime);
    $time = $datetime->format('Y-m-d H:i:s');
    
    $stockRow = null;
    try {
        $checkStock = $conn->prepare("SELECT daily_quantity, VehiclePrice FROM hotelvehicletype WHERE VehicleType = ?");
        $checkStock->execute([$vehicleType]);
        $stockRow = $checkStock->fetch();
    } catch (PDOException $e) {
        $checkStock = $conn->prepare("SELECT daily_quantity FROM hotelvehicletype WHERE VehicleType = ?");
        $checkStock->execute([$vehicleType]);
        $stockRow = $checkStock->fetch();
    }
    
    if ($stockRow && $stockRow['daily_quantity'] > 0) {
        $deductStock = $conn->prepare("UPDATE hotelvehicletype SET daily_quantity = daily_quantity - 1 WHERE VehicleType = ? AND daily_quantity > 0");
        $deductStock->execute([$vehicleType]);
        
        $stmt = $conn->prepare("INSERT INTO orderbookings (OrderType, Time, ContactNo, Email, OrderRemark, Status, OrderCreatedDate, OrderModifiedDate, NoofGuest, isRequired, AssignedTo, PaymentStatus, PaymentMethod, PaymentTime) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(['Limo', $time, $phone, $email, $orderremark, $status, $ordercreateddate, $ordermodifieddate, $noofguest, 0, '', $paymentStatus, $paymentMethod, $paymentTime]);
        
        if ($stmt->rowCount() > 0) {
            echo "<script type='text/javascript'>alert('New Limo Order created successfully');</script>";
        }
    } else {
        echo "<script type='text/javascript'>alert('Sorry, no vehicles available');</script>";
    }
    
    closeDBConnection($conn);
    header("Location: bookedlimo.php?success=1");
    exit();
}

// 创建待支付订单并跳转到支付页面
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['action'])) {
    require_once __DIR__ . '/function/create_pending_order.php';
    
    // 如果用户已登录，使用session中的email，否则使用表单提交的email
    $email = isset($_SESSION['email']) && !empty($_SESSION['email']) ? $_SESSION['email'] : (isset($_POST['email']) ? $_POST['email'] : '');
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $vehicleType = isset($_POST['vehicleType']) ? $_POST['vehicleType'] : '';
    $orderremark = $vehicleType . ' | ' . (isset($_POST['comment']) ? $_POST['comment'] : '');
    $noofguest = isset($_POST['guests']) ? $_POST['guests'] : '';
    $bookingDate = isset($_POST['bookingDate']) ? $_POST['bookingDate'] : '';
    $bookingTime = isset($_POST['bookingTime']) ? $_POST['bookingTime'] : '';
    $datetime = new DateTime($bookingDate . ' ' . $bookingTime);
    $time = $datetime->format('Y-m-d H:i:s');
    
    // 检查库存
    require_once __DIR__ . '/config/db_config.php';
    $conn = getDBConnection();
    $stockRow = null;
    try {
        $checkStock = $conn->prepare("SELECT daily_quantity, VehiclePrice FROM hotelvehicletype WHERE VehicleType = ?");
        $checkStock->execute([$vehicleType]);
        $stockRow = $checkStock->fetch();
    } catch (PDOException $e) {
        // VehiclePrice字段可能不存在，尝试只查询daily_quantity
        $checkStock = $conn->prepare("SELECT daily_quantity FROM hotelvehicletype WHERE VehicleType = ?");
        $checkStock->execute([$vehicleType]);
        $stockRow = $checkStock->fetch();
    }
    closeDBConnection($conn);
    
    if ($stockRow && $stockRow['daily_quantity'] <= 0) {
        echo "<script type='text/javascript'>alert('Sorry, no vehicles available for " . htmlspecialchars($vehicleType) . "');</script>";
        exit();
    }
    
    $price = $stockRow && isset($stockRow['VehiclePrice']) && $stockRow['VehiclePrice'] ? floatval($stockRow['VehiclePrice']) : 0;
    
    // 创建待支付订单
    createPendingOrder([
        'OrderType' => 'Limo',
        'Time' => $time,
        'ContactNo' => $phone,
        'Email' => $email,
        'OrderRemark' => $orderremark,
        'NoofGuest' => $noofguest,
        'Amount' => $price,
        'vehicle_type' => $vehicleType
    ]);
    
    header("Location: payment_simulation.php");
    exit();
}
?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CISC7196-HotelMIS-2023OCT18</title>
    
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="img/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="img/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="img/apple-touch-icon-114x114.png">

    <link rel="stylesheet" type="text/css"  href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome/css/font-awesome.css">

    <link href="css/owl.carousel.css" rel="stylesheet" media="screen">
    <link href="css/owl.theme.css" rel="stylesheet" media="screen">

    <link rel="stylesheet" type="text/css"  href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/responsive.css">

    <link href='http://fonts.googleapis.com/css?family=Lato:100,300,400,700,900,100italic,300italic,400italic,700italic,900italic' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,700,300,600,800,400' rel='stylesheet' type='text/css'>

    <script type="text/javascript" src="js/modernizr.custom.js"></script>
<style>
#bookingTime option {
    height: 50px;
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

    <div id="tf-home" class="text-center">
	    <a href="#tf-contact" ></a>
    </div>
	
	<div id="tf-about">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                   <div id="slideshow-wrap">
						<input type="radio" id="button-1" name="controls" checked="checked"/>
						<label for="button-1"></label>
						<input type="radio" id="button-2" name="controls"/>
						<label for="button-2"></label>
						<input type="radio" id="button-3" name="controls"/>
						<label for="button-3"></label>
						<input type="radio" id="button-4" name="controls"/>
						<label for="button-4"></label>
						<input type="radio" id="button-5" name="controls"/>
						<label for="button-5"></label>
						
						<div id="slideshow-inner">
							<ul>
								<li id="slide1">
									<img src="" class="limo-slideshow-img" data-index="0" />
									<div class="description">
										<input type="checkbox" id="show-description-1"/>
										<label for="show-description-1" class="show-description-label">I</label>
										<div class="description-text">
											<h3>Rolls-Royce Motor Cars</h3>
											<p>Rolls-Royce Motor Cars Limited is a British luxury automobile maker which has operated as a wholly owned subsidiary of BMW AG since 2003</p>
										</div>
									</div>
								</li>
								<li id="slide2">
									<img src="" class="limo-slideshow-img" data-index="1" />
									<div class="description">
										<input type="checkbox" id="show-description-2"/>
										<label for="show-description-2" class="show-description-label">1</label>
										<div class="description-text">
											<h3>Bentley</h3>
											<p>Bentley Motors Limited is a British designer, manufacturer and marketer of luxury cars and SUVs. Headquartered in Crewe, England,</p>
										</div>
									</div>
								</li>
								<li id="slide3">
									<img src="" class="limo-slideshow-img" data-index="2" />
									<div class="description">
										<input type="checkbox" id="show-description-3"/>
										<label for="show-description-3" class="show-description-label">2</label>
										<div class="description-text">
											<h3>Toyota and Lexus</h3>
											<p>Toyota owns the Lexus brand, but Toyota has its headquarters in Toyota City, Japan, while Lexus operations are headquartered in Nagoya</p>
										</div>
									</div>
								</li>
								<li id="slide4">
									<img src="" class="limo-slideshow-img" data-index="3" />
									<div class="description">
										<input type="checkbox" id="show-description-4"/>
										<label for="show-description-4" class="show-description-label">3</label>
										<div class="description-text">
											<h2>Ferrari</h2>
											<p>Ferrari - All the official contents of the Maranello based carmaker: all the cars in the range and the great historic cars</p>
										</div>
									</div>
								</li>
								<li id="slide5">
									<img src="" class="limo-slideshow-img" data-index="4" />
									<div class="description">
										<input type="checkbox" id="show-description-5"/>
										<label for="show-description-5" class="show-description-label">4</label>
										<div class="description-text">
											<h3>Aston Martin</h3>
											<p>The luxury British sports car manufacturer.</p><a href="#" class="btn tf-btn btn-success">Order</a>
										</div>
									</div>
								</li>
							</ul>
						</div>
					</div>
                </div>
                <div class="col-md-6">
<?php if ($showLimoForm): ?>
					<div class="section-title">
                     <h3>Kindly input the following details for our limo service.</h3>
                          <div class="clearfix"></div>
                        </div>
 <form action="bookedlimo.php" method="post">
                        <div class="row">
							<div class="col-md-12">
                                <div class="form-group">
                                    <label for="exampleInputPassword1">Username/Email address</label>
                                       <input type="text" class="form-control" id="email" name="email" 
                                        <?php if (isset($_SESSION['email']) && !empty($_SESSION['email'])): ?>
                                            value="<?php echo htmlspecialchars($_SESSION['email']); ?>" readonly
                                        <?php else: ?>
                                            placeholder="Username/Email"
                                        <?php endif; ?>
                                        required>
                                </div>
                            </div>
							
                            <div class="col-md-12">
                <div class="form-group">
                    <label for="phone">Contact number</label>
                    <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone Number" required>
                </div>
            </div>

<?php
$conn = getDBConnection();
$result = null;
try {
    $sql = "SELECT VehicleType, daily_quantity, VehiclePrice FROM hotelvehicletype WHERE status = 1 AND daily_quantity > 0";
    $result = $conn->query($sql);
} catch (PDOException $e) {
    // VehiclePrice字段可能不存在，尝试只查询基本字段
    $sql = "SELECT VehicleType, daily_quantity FROM hotelvehicletype WHERE status = 1 AND daily_quantity > 0";
    $result = $conn->query($sql);
}
?>

<div class="col-md-12">
    <div class="form-group">
        <label for="luxurycars">Luxury Cars</label>
        <select class="form-control" id="luxurycars" name="vehicleType" onchange="updateVehicleInfo()">
            <option value="">Select a Luxury Car</option>
            <?php
            if ($result) {
                while ($row = $result->fetch()) {
                    $vehicleType = htmlspecialchars($row["VehicleType"]);
                    $dailyQuantity = htmlspecialchars($row["daily_quantity"]);
                    $vehiclePrice = isset($row["VehiclePrice"]) ? htmlspecialchars($row["VehiclePrice"]) : '0';
                    echo "<option value=\"" . $vehicleType . "\" data-quantity=\"" . $dailyQuantity . "\" data-price=\"" . $vehiclePrice . "\">" . $vehicleType . " ($" . $vehiclePrice . ", Available: " . $dailyQuantity . ")</option>";
                }
            } else {
                echo "<option value=\"\" disabled>No vehicles available</option>";
            }
            closeDBConnection($conn);
            ?>
        </select>
    </div>
</div>
<div class="col-md-12">
    <div id="vehicleInfo" class="help-block" style="color: #666; font-size: 14px;"></div>
</div>
<div class="col-md-12">
    <div id="stockRefreshInfo" class="help-block" style="color: #008CBA; font-size: 12px; font-style: italic;">
        Last refreshed: --
    </div>
</div>

<div class="col-md-12">
    <div class="form-group">
        <label for="bookingDate">PickUp Date</label>
        <input type="date" class="form-control" id="bookingDate" name="bookingDate" min="<?php echo date('Y-m-d'); ?>">
    </div>
</div>

<div class="col-md-12">
    <div class="form-group">
        <label for="bookingTime">PickUp Time</label>
        <select class="form-control" id="bookingTime" name="bookingTime"></select>
    </div>
</div>

 <div class="col-md-12">
                <div class="form-group">
                    <label for="guests">Number of Guests</label>
                    <input type="number" class="form-control" id="guests" name="guests" min="1" max="10">
                </div>
            </div>

<!-- Destination -->
<div class="col-md-12">
    <div class="form-group">
        <label for="destination">Destination</label>
        <input type="text" class="form-control" id="destination" placeholder="Enter Destination">
    </div>
</div>

<!-- Text field -->
<div class="col-md-12">
    <div class="form-group">
        <label for="comment">Comment</label>
        <input type="text" class="form-control" id="comment" name="comment" placeholder="Leave your request here">
    </div>
</div>
                        </div>
                        
                        
                        <button type="submit" class="btn tf-btn btn-success">Submit Booking</button>
                        <button type="button" class="btn tf-btn btn-warning" id="addToCartBtn">Add to Cart</button>
                    </form>
<?php else: ?>
                    <div class="alert alert-warning">
                        <h4>Access Restricted</h4>
                        <p><?php echo htmlspecialchars($limoAccessMessage); ?></p>
                    </div>
<?php endif; ?>
                </div>
            </div>
        </div>
    </div>

      <?php include(__DIR__ . '/layout/footer.php');?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery.1.11.1.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script type="text/javascript" src="js/SmoothScroll.js"></script>
    <script type="text/javascript" src="js/jquery.isotope.js"></script>
    <script src="js/owl.carousel.js"></script>

    <script type="text/javascript" src="js/main.js"></script>
<script src="js/cart.js"></script>
<script>
    var select = document.getElementById("bookingTime");
var hours, minutes, ampm;
for(var i = 1800; i <= 82800; i += 1800){
    hours = Math.floor(i / 3600);
    minutes = Math.floor((i % 3600) / 60);
    ampm = hours >= 12 ? 'pm' : 'am';
    hours = hours % 12;
    hours = hours ? hours : 12;
    minutes = minutes < 10 ? '0'+minutes : minutes;
    var strTime = hours + ':' + minutes + ' ' + ampm;
    var opt = document.createElement('option');
    opt.value = strTime;
    opt.innerHTML = strTime;
    select.appendChild(opt);
}

$('form').submit(function() {
    $(this).find('button[type="submit"]').prop('disabled', true);
});

const limoImagePaths = <?php echo $limoImagePathsJSON; ?>;

function getRandomLimoImages(count) {
    if (limoImagePaths.length === 0) return [];
    const shuffled = [...limoImagePaths].sort(() => 0.5 - Math.random());
    return shuffled.slice(0, count);
}

function updateLimoImages() {
    const images = document.querySelectorAll('.limo-slideshow-img');
    if (images.length === 0) return;
    const randomImages = getRandomLimoImages(images.length);
    images.forEach((img, index) => {
        if (randomImages[index]) {
            img.src = randomImages[index];
        }
    });
}

const vehicleSelect = document.getElementById('luxurycars');
const vehicleInfo = document.getElementById('vehicleInfo');
const stockRefreshInfo = document.getElementById('stockRefreshInfo');
let refreshInterval = null;

function updateVehicleInfo() {
    if (vehicleSelect && vehicleInfo) {
        const selectedOption = vehicleSelect.options[vehicleSelect.selectedIndex];
        const quantity = selectedOption.getAttribute('data-quantity');
        if (quantity) {
            vehicleInfo.textContent = 'Available Vehicles: ' + quantity;
        } else {
            vehicleInfo.textContent = '';
        }
    }
}

function refreshStock() {
    $.ajax({
        url: 'function/get_available_stock.php',
        type: 'GET',
        data: { action: 'get_stock' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                updateRefreshTime(response.timestamp);
                updateVehicleStockOptions(response.vehicle_stock);
            }
        },
        error: function() {
            console.error('Failed to refresh stock');
        }
    });
}

function updateRefreshTime(timestamp) {
    stockRefreshInfo.textContent = 'Last refreshed: ' + timestamp;
}

function updateVehicleStockOptions(vehicleStock) {
    const options = vehicleSelect.options;
    let hasChanges = false;
    
    for (let i = 0; i < options.length; i++) {
        const option = options[i];
        if (option.value && vehicleStock[option.value] !== undefined) {
            const oldQty = option.getAttribute('data-quantity');
            const newQty = vehicleStock[option.value];
            
            if (oldQty !== newQty.toString()) {
                hasChanges = true;
                option.setAttribute('data-quantity', newQty);
                
                const textParts = option.text.split(' (');
                option.text = textParts[0] + ' (Available: ' + newQty + ')';
                
                if (newQty <= 0) {
                    option.disabled = true;
                    if (vehicleSelect.value === option.value) {
                        vehicleSelect.selectedIndex = 0;
                        updateVehicleInfo();
                    }
                } else {
                    option.disabled = false;
                }
            }
        }
    }
    
    if (hasChanges) {
        vehicleSelect.style.borderColor = '#dc3545';
        setTimeout(() => {
            vehicleSelect.style.borderColor = '#ced4da';
        }, 1000);
    }
}

if (vehicleSelect) {
    vehicleSelect.addEventListener('change', function() {
        updateLimoImages();
        updateVehicleInfo();
    });
}

updateLimoImages();

refreshStock();
refreshInterval = setInterval(refreshStock, 120000);

$(window).on('beforeunload', function() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
});

$('#addToCartBtn').click(function() {
    var vehicleType = $('#luxurycars').val();
    var date = $('#bookingDate').val();
    var time = $('#bookingTime').val();
    var guests = $('#guests').val() || 1;
    var destination = $('#destination').val() || '';
    var comment = $('#comment').val() || '';
    
    if (!vehicleType) {
        alert('Please select a vehicle type');
        return;
    }
    if (!date) {
        alert('Please select a pickup date');
        return;
    }
    if (!time) {
        alert('Please select a pickup time');
        return;
    }
    
    var selectedOption = $('#luxurycars option:selected');
    var itemType = 'Limo';
    var itemName = vehicleType;
    var itemPrice = parseFloat(selectedOption.data('price')) || 0;
    var itemDetails = 'Destination: ' + destination + ', Comment: ' + comment;
    
    addToCart(itemType, itemName, itemPrice, date, time, guests, itemDetails);
});
</script>
  </body>
</html>