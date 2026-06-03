<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/language.php';

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

// Generate a unique token for form submission
if (!isset($_SESSION['fnb_booking_token'])) {
    $_SESSION['fnb_booking_token'] = md5(uniqid(rand(), true));
}

// Handle form submission first (before any HTML output)
date_default_timezone_set('Asia/Shanghai');
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['email']) && !empty($_POST['fnb_booking_token']) && $_POST['fnb_booking_token'] === $_SESSION['fnb_booking_token']) {
    $_SESSION['fnb_booking_token'] = md5(uniqid(rand(), true));
    
    $servername = "localhost";
    $username = "root";
    $password = "123456";
    $dbname = "hmis";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $bookingDate = $_POST['bookingDate'];
    $bookingTime = $_POST['bookingTime'];
    $outletName = $_POST['outlet'];
    $datetime = new DateTime($bookingDate . ' ' . $bookingTime);
    $time = $datetime->format('Y-m-d H:i:s');
    
    $ordertype = "F&B";
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $orderremark = $outletName . ' | ' . $_POST['comment'];
    $status = "TBC";
    $ordercreateddate = date('Y-m-d H:i:s');
    $ordermodifieddate = date('Y-m-d H:i:s');
    $noofguest = $_POST['guests'];
    $isRequired = 0;
    $assignedTo = '';

    $checkCapacity = $conn->prepare("SELECT capacity FROM hoteloutlet WHERE OutletName = ?");
    $checkCapacity->bind_param("s", $outletName);
    $checkCapacity->execute();
    $capacityResult = $checkCapacity->get_result();
    
    if ($capacityResult->num_rows > 0) {
        $capacityRow = $capacityResult->fetch_assoc();
        $totalCapacity = $capacityRow['capacity'];
        
        $bookedQuery = $conn->prepare("SELECT SUM(NoofGuest) as booked FROM orderbookings 
            WHERE OrderType = 'F&B' AND OrderRemark LIKE ? AND Status IN ('TBC', 'Confirmed') 
            AND DATE(OrderCreatedDate) = ?");
        $remarkPattern = '%' . $outletName . '%';
        $bookedQuery->bind_param("ss", $remarkPattern, $bookingDate);
        $bookedQuery->execute();
        $bookedResult = $bookedQuery->get_result();
        $bookedRow = $bookedResult->fetch_assoc();
        $bookedSeats = $bookedRow['booked'] ? $bookedRow['booked'] : 0;
        
        $availableSeats = $totalCapacity - $bookedSeats;
        
        if ($availableSeats < $noofguest) {
            $_SESSION['fnb_booking_error'] = "Sorry, only " . $availableSeats . " seats available for " . $outletName;
            $checkCapacity->close();
            $conn->close();
            header("Location: bookedFnB.php?error=1");
            exit();
        }
        $bookedQuery->close();
    }
    $checkCapacity->close();

    $stmt = $conn->prepare("INSERT INTO orderbookings (OrderType,Time,ContactNo, Email, OrderRemark, Status, OrderCreatedDate, OrderModifiedDate, NoofGuest, isRequired, AssignedTo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssiis", $ordertype, $time, $phone, $email, $orderremark, $status, $ordercreateddate, $ordermodifieddate, $noofguest, $isRequired, $assignedTo);

    if ($stmt->execute()) {
        $last_id = $conn->insert_id;
        $_SESSION['fnb_booking_success'] = "Your order ID is: F&B " . $last_id;
        $stmt->close();
        $conn->close();
        header("Location: bookedFnB.php?success=1");
        exit();
    } else {
        $stmt->close();
        $conn->close();
    }
}

$servername = "localhost";
$username = "root";
$password = "123456";
$dbname = "hmis";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$outlets = [];
$sql = "SELECT * FROM hoteloutlet WHERE Status = 1 AND Style != 'IRD'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $outlets[] = $row;
    }
}
$conn->close(); // Close connection after fetching outlets

$fnbImages = glob(__DIR__ . '/img/fnb/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
$fnbImagePaths = [];
foreach ($fnbImages as $img) {
    $fnbImagePaths[] = 'img/fnb/' . basename($img);
}
$fnbImagePathsJSON = json_encode($fnbImagePaths);
?><!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Basic Page Needs
    ================================================== -->
    <meta charset="utf-8">
    <!--[if IE]><meta http-equiv="x-ua-compatible" content="IE=9" /><![endif]-->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CISC7196-HotelMIS-2023OCT18</title>
    
    <!-- Favicons
    ================================================== -->
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="img/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="img/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="img/apple-touch-icon-114x114.png">

    <!-- Bootstrap -->
    <link rel="stylesheet" type="text/css"  href="css/bootstrap.css">
	<link rel="stylesheet" type="text/css"  href="css/bookedfnb.css">															  
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome/css/font-awesome.css">

    <!-- Slider
    ================================================== -->
    <link href="css/owl.carousel.css" rel="stylesheet" media="screen">
    <link href="css/owl.theme.css" rel="stylesheet" media="screen">
	  <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Stylesheet
    ================================================== -->
    <link rel="stylesheet" type="text/css"  href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/responsive.css">

    <link href='http://fonts.googleapis.com/css?family=Lato:100,300,400,700,900,100italic,300italic,400italic,700italic,900italic' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,700,300,600,800,400' rel='stylesheet' type='text/css'>

    <script type="text/javascript" src="js/modernizr.custom.js"></script>

  </head>
  <body>
    <!-- Navigation
    ==========================================-->
    <nav id="tf-menu" class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php"><?php echo t('hotel_management_system'); ?></a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
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

	
</div><!-- /.navbar-collapse -->
      </div><!-- /.container-fluid -->
    </nav>

    <!-- Home Page
    ==========================================-->
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
            <!-- User details -->
            <div class="col-md-12">
                <div class="form-group">
                    <label for="email">Username/Email address</label>
                    <input type="text" class="form-control" id="email" name="email" placeholder="Username/Email" required>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="phone">Contact number</label>
                    <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone Number" required>
                </div>
            </div>
            <!-- F&B Outlet selection -->
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
    <!-- Time slots will be added here by JavaScript -->
</datalist>
    <!-- Time slots will be added here by JavaScript -->
</datalist>
            <!-- Number of guests -->
            <div class="col-md-12">
                <div class="form-group">
                    <label for="guests">Number of Guests</label>
                    <input type="number" class="form-control" id="guests" name="guests" min="1" max="10">
                </div>
            </div>
            <!-- Additional comments -->
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
  <!-- Modal content -->
  <div class="modal-content">
	<img src="img/fnb/logo.png" alt="Logo" style="width:100px; height:auto;"> <!-- Add this line -->
    <span class="close">&times;</span>
    <p id="modalText">Your reservation is confirmed.</p>
  </div>
</div>
</div>
            </div>
        </div>
    </div>


  <?php include(__DIR__ . '/layout/footer.php');?>


    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery.1.11.1.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script type="text/javascript" src="js/SmoothScroll.js"></script>
    <script type="text/javascript" src="js/jquery.isotope.js"></script>
    <script src="js/owl.carousel.js"></script>

    <!-- Javascripts
    ================================================== -->
    <script type="text/javascript" src="js/main.js"></script>
    <script src="js/cart.js"></script>

<script>
    // Start and end times (24 hour clock)
    var startTime = 17;
    var endTime = 21;
	var today = new Date().toISOString().split('T')[0];
    document.getElementById('bookingDate').setAttribute('min', today);
    // Get the datalist element
    var datalist = document.getElementById('time-slots');

    // Loop over the times and add them as options
    for (var i = startTime; i <= endTime; i++) {
        // Create the options for on the hour and 30 minutes past the hour
        var option1 = document.createElement('option');
        var option2 = document.createElement('option');
        option1.value = (i < 10 ? '0' : '') + i + ':00';
        option2.value = (i < 10 ? '0' : '') + i + ':30';

        // Add the options to the datalist element
        datalist.appendChild(option1);
        datalist.appendChild(option2);
    }
	
		// Get the modal
var modal = document.getElementById("myModal");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks on <span> (x), close the modal
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
    var guests = $('#guests').val() || 1;
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
    
    var itemType = 'Dining';
    var itemName = outlet;
    var itemPrice = 0;
    var itemDetails = 'Guests: ' + guests + ', Time: ' + time + (comment ? ', Notes: ' + comment : '');
    
    addToCart(itemType, itemName, itemPrice, date, time, guests, itemDetails);
});

updateFnbImages();
</script>

  </body>
</html>

	