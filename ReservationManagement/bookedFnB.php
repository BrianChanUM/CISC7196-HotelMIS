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

$showFnBForm = true;
$fnbAccessMessage = "";

if (!isset($_SESSION['username'])) {
    $showFnBForm = false;
    $fnbAccessMessage = "Please login first.";
} else {
    $userRole = $_SESSION['role'];
    if ($userRole != 'admin') {
        if (!checkPermission('dining_booking', 'create')) {
            $showFnBForm = false;
            $fnbAccessMessage = "You do not have permission to book dining services.";
        }
    }
}

if (!isset($_SESSION['fnb_booking_token'])) {
    $_SESSION['fnb_booking_token'] = md5(uniqid(rand(), true));
}

date_default_timezone_set('Asia/Shanghai');

// 废弃的直接支付回调代码 - 已迁移到payment_simulation.php统一处理
// 所有预订必须通过payment_simulation.php完成容量检查和扣减
// 此代码块已注释，因为：
// 1. 只检查容量但不扣减，导致库存计算错误
// 2. payment_simulation.php已经有完整的事务处理和库存扣减逻辑
/*
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'fnb_payment_return') {
    // 已废弃：使用payment_simulation.php代替
    die('This payment method is deprecated. Please use the standard booking flow.');
}
*/

// 创建待支付订单并跳转到支付页面
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['email']) && !empty($_POST['fnb_booking_token']) && $_POST['fnb_booking_token'] === $_SESSION['fnb_booking_token'] && !isset($_POST['action'])) {
    $_SESSION['fnb_booking_token'] = md5(uniqid(rand(), true));
    
    require_once __DIR__ . '/function/create_pending_order.php';
    
    $bookingDate = $_POST['bookingDate'];
    $bookingTime = $_POST['bookingTime'];
    $outletName = $_POST['outlet'];
    $datetime = new DateTime($bookingDate . ' ' . $bookingTime);
    $time = $datetime->format('Y-m-d H:i:s');
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $orderremark = $outletName . ' | ' . $_POST['comment'];
    $noofguest = $_POST['guests'];
    
    // 检查容量
    require_once __DIR__ . '/config/db_config.php';
    $conn = getDBConnection();
    $checkCapacity = $conn->prepare("SELECT capacity FROM hoteloutlet WHERE OutletName = ?");
    $checkCapacity->execute([$outletName]);
    $capacityRow = $checkCapacity->fetch();
    
    if ($capacityRow) {
        $totalCapacity = $capacityRow['capacity'];
        $remarkPattern = '%' . $outletName . '%';
        $bookedQuery = $conn->prepare("SELECT SUM(NoofGuest) as booked FROM orderbookings 
            WHERE OrderType = 'F&B' AND OrderRemark LIKE ? AND Status IN ('TBC', 'Confirmed') 
            AND DATE(OrderCreatedDate) = ?");
        $bookedQuery->execute([$remarkPattern, $bookingDate]);
        $bookedRow = $bookedQuery->fetch();
        $bookedSeats = $bookedRow['booked'] ? $bookedRow['booked'] : 0;
        $availableSeats = $totalCapacity - $bookedSeats;
        
        if ($availableSeats < $noofguest) {
            closeDBConnection($conn);
            $_SESSION['fnb_booking_error'] = "Sorry, only " . $availableSeats . " seats available";
            header("Location: bookedFnB.php?error=1");
            exit();
        }
    }
    
    closeDBConnection($conn);
    
    // 根据memory规则：Dining(F&B)默认价格$30每人
    $price = 30 * $noofguest;
    
    // 创建待支付订单
    createPendingOrder([
        'OrderType' => 'F&B',
        'Time' => $time,
        'ContactNo' => $phone,
        'Email' => $email,
        'OrderRemark' => $orderremark,
        'NoofGuest' => $noofguest,
        'Amount' => $price,
        'outlet_name' => $outletName  // 新增：餐厅网点名称，用于payment_simulation库存扣减
    ]);
    
    header("Location: payment_simulation.php");
    exit();
}

require_once __DIR__ . '/config/db_config.php';

$conn = getDBConnection();

$outlets = [];
$sql = "SELECT * FROM hoteloutlet WHERE status = 1 AND Style != 'IRD'";
$stmt = $conn->query($sql);
while ($row = $stmt->fetch()) {
    $outlets[] = $row;
}
closeDBConnection($conn);

$fnbImages = glob(__DIR__ . '/img/fnb/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
$fnbImagePaths = [];
foreach ($fnbImages as $img) {
    $fnbImagePaths[] = 'img/fnb/' . basename($img);
}
$fnbImagePathsJSON = json_encode($fnbImagePaths);
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
	<link rel="stylesheet" type="text/css"  href="css/bookedfnb.css">															  
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome/css/font-awesome.css">

    <link href="css/owl.carousel.css" rel="stylesheet" media="screen">
    <link href="css/owl.theme.css" rel="stylesheet" media="screen">

    <link rel="stylesheet" type="text/css"  href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/responsive.css">

    <link href='http://fonts.googleapis.com/css?family=Lato:100,300,400,700,900,100italic,300italic,400italic,700italic,900italic' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,700,300,600,800,400' rel='stylesheet' type='text/css'>

    <script type="text/javascript" src="js/modernizr.custom.js"></script>

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

<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $user = json_encode($_SESSION);
?>

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
									<img src="" class="fnb-slideshow-img" data-index="0" />
									<div class="description">
										<input type="checkbox" id="show-description-1"/>
										<label for="show-description-1" class="show-description-label">I</label>
									</div>
								</li>
								<li id="slide2">
									<img src="" class="fnb-slideshow-img" data-index="1" />
									<div class="description">
										<input type="checkbox" id="show-description-2"/>
										<label for="show-description-2" class="show-description-label">1</label>
									</div>
								</li>
								<li id="slide3">
									<img src="" class="fnb-slideshow-img" data-index="2" />
									<div class="description">
										<input type="checkbox" id="show-description-3"/>
										<label for="show-description-3" class="show-description-label">2</label>
									</div>
								</li>
								<li id="slide4">
									<img src="" class="fnb-slideshow-img" data-index="3" />
									<div class="description">
										<input type="checkbox" id="show-description-4"/>
										<label for="show-description-4" class="show-description-label">3</label>
									</div>
								</li>
								<li id="slide5">
									<img src="" class="fnb-slideshow-img" data-index="4" />
									<div class="description">
										<input type="checkbox" id="show-description-5"/>
										<label for="show-description-5" class="show-description-label">4</label>
									</div>
								</li>
							</ul>
						</div>
					</div>
                </div>
<div class="col-md-6">
    <div class="section-title">
        <h3>F&B Reservation service.</h3>
        <div class="clearfix"></div>
    </div>
    <?php if (isset($_SESSION['fnb_booking_success']) && isset($_GET['success'])): ?>
    <div class="alert alert-success">
        <?php echo $_SESSION['fnb_booking_success']; unset($_SESSION['fnb_booking_success']); ?>
    </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['fnb_booking_error']) && isset($_GET['error'])): ?>
    <div class="alert alert-danger">
        <?php echo $_SESSION['fnb_booking_error']; unset($_SESSION['fnb_booking_error']); ?>
    </div>
    <?php endif; ?>
    <form action="bookedFnB.php" method="post">
        <input type="hidden" name="fnb_booking_token" value="<?php echo $_SESSION['fnb_booking_token']; ?>">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="email">Username/Email address</label>
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
            <div class="col-md-12">
                <div class="form-group">
                    <label for="outlet">Restaurant</label>
                    <select class="form-control" id="outlet" name="outlet">
                        <option value="">Select a Restaurant</option>
                        <?php foreach ($outlets as $outlet): ?>
                        <option value="<?php echo htmlspecialchars($outlet['OutletName']); ?>" 
                                data-capacity="<?php echo isset($outlet['capacity']) ? htmlspecialchars($outlet['capacity']) : 50; ?>">
                            <?php echo htmlspecialchars($outlet['OutletName']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="help-block" id="seats-info">Select a restaurant to see available seats</p>
                </div>
            </div>
           
<div class="col-md-12">
    <div class="form-group">
        <label for="bookingDate">Booking Date</label>
        <input type="date" class="form-control" id="bookingDate" name="bookingDate">
    </div>
</div>

<div class="col-md-12">
    <div class="form-group">
        <label for="bookingTime">Booking Time</label>
        <input type="time" class="form-control" id="bookingTime" name="bookingTime" list="time-slots">
    </div>
</div>

<datalist id="time-slots">
</datalist>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="guests">Number of Guests</label>
                    <input type="number" class="form-control" id="guests" name="guests" min="1" max="10">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="comment">Special Request</label>
                    <input type="text" class="form-control" id="comment" name="comment" placeholder="Leave your request here">
                </div>
            </div>
        </div>
        <button type="submit" class="btn tf-btn btn-success">Submit Booking</button>
        <button type="button" class="btn tf-btn btn-warning" id="addToCartBtn">Add to Cart</button>
    </form>
	
		<div id="myModal" class="modal">
  <div class="modal-content">
	<img src="img/fnb/logo.png" alt="Logo" style="width:100px; height:auto;">
    <span class="close">&times;</span>
    <p id="modalText">Your reservation is confirmed.</p>
  </div>
</div>
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
    var startTime = 17;
    var endTime = 21;
	var today = new Date().toISOString().split('T')[0];
    document.getElementById('bookingDate').setAttribute('min', today);
    var datalist = document.getElementById('time-slots');

    for (var i = startTime; i <= endTime; i++) {
        var option1 = document.createElement('option');
        var option2 = document.createElement('option');
        option1.value = (i < 10 ? '0' : '') + i + ':00';
        option2.value = (i < 10 ? '0' : '') + i + ':30';

        datalist.appendChild(option1);
        datalist.appendChild(option2);
    }
	
		var modal = document.getElementById("myModal");
		var span = document.getElementsByClassName("close")[0];

		span.onclick = function() {
		  modal.style.display = "none";
		}

		window.onclick = function(event) {
		  if (event.target == modal) {
			modal.style.display = "none";
		  }
		}

		const fnbImagePaths = <?php echo $fnbImagePathsJSON; ?>;

		function getRandomImages(count) {
			if (fnbImagePaths.length === 0) return [];
			const shuffled = [...fnbImagePaths].sort(() => 0.5 - Math.random());
			return shuffled.slice(0, count);
		}

		function updateFnbImages() {
			const images = document.querySelectorAll('.fnb-slideshow-img');
			if (images.length === 0) return;
			const randomImages = getRandomImages(images.length);
			images.forEach((img, index) => {
				if (randomImages[index]) {
					img.src = randomImages[index];
				}
			});
		}

		const outletSelect = document.getElementById('outlet');
		const seatsInfo = document.getElementById('seats-info');
		const bookingDateInput = document.getElementById('bookingDate');

		outletSelect.addEventListener('change', function() {
			const selectedOutlet = this.value;
			const selectedOption = this.options[this.selectedIndex];
			const capacity = selectedOption.dataset.capacity || 50;
			
			if (selectedOutlet) {
				seatsInfo.textContent = `Total capacity: ${capacity} seats`;
				checkAvailableSeats(selectedOutlet, bookingDateInput.value);
				updateFnbImages();
			} else {
				seatsInfo.textContent = 'Select a restaurant to see available seats';
			}
		});

		bookingDateInput.addEventListener('change', function() {
			const selectedOutlet = outletSelect.value;
			if (selectedOutlet && this.value) {
				checkAvailableSeats(selectedOutlet, this.value);
			}
		});

		function checkAvailableSeats(outlet, date) {
			fetch('function/check_seats.php?outlet=' + encodeURIComponent(outlet) + '&date=' + encodeURIComponent(date))
				.then(response => response.json())
				.then(data => {
					if (data.error) {
						seatsInfo.textContent = data.error;
					} else {
						seatsInfo.textContent = `Capacity: ${data.capacity} | Booked: ${data.booked} | Available: ${data.available}`;
						if (data.available <= 0) {
							seatsInfo.style.color = 'red';
							seatsInfo.textContent += ' - FULL, please choose another date or restaurant!';
						} else if (data.available < 10) {
							seatsInfo.style.color = 'orange';
						} else {
							seatsInfo.style.color = 'green';
						}
					}
				})
				.catch(error => {
					console.error('Error checking seats:', error);
				});
		}

		$('#addToCartBtn').click(function() {
			var outlet = $('#outlet').val();
			var date = $('#bookingDate').val();
			var time = $('#bookingTime').val();
			var guests = parseInt($('#guests').val() || 1);
			var comment = $('#comment').val() || '';

			if (!outlet) {
				alert('Please select a restaurant');
				return;
			}

			if (!date) {
				alert('Please select a date');
				return;
			}

			if (!time) {
				alert('Please select a time');
				return;
			}

			// Check capacity before adding to cart
			$.ajax({
				url: 'function/check_outlet_capacity.php',
				type: 'POST',
				data: { outlet_name: outlet, guests: guests },
				dataType: 'json',  // 告诉jQuery期望返回JSON格式
				success: function(result) {
					// jQuery已经自动解析了JSON，result已经是对象
					if (result.success) {
						if (result.capacity >= guests) {
							var itemType = 'Dining';
							var itemName = outlet;
							var itemPrice = 30 * guests;  // 根据memory规则：默认$30每人
							var itemDetails = 'Guests: ' + guests + ', Time: ' + time + (comment ? ', Notes: ' + comment : '');
							addToCart(itemType, itemName, itemPrice, date, time, guests, itemDetails);
						} else {
							alert('Not enough capacity. Available: ' + result.capacity + ', Required: ' + guests);
						}
					} else {
						alert(result.message || 'Failed to check capacity');
					}
				},
				error: function() {
					alert('Error checking capacity. Please try again.');
				}
			});
		});

		updateFnbImages();
</script>

  </body>
</html>