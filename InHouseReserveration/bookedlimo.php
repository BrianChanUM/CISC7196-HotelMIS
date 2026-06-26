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
<style>


#bookingTime option {
    height: 50px;
}
</style>
   
	
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
    $user = json_encode($_SESSION);
    
    $limoImages = glob(__DIR__ . '/img/limo/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    $limoImagePaths = [];
    foreach ($limoImages as $img) {
        $limoImagePaths[] = 'img/limo/' . basename($img);
    }
    $limoImagePathsJSON = json_encode($limoImagePaths);
?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username = "root";
    $password = "123456";
    $dbname = "hmis";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO orderbookings (OrderType,Time,ContactNo, Email, OrderRemark, Status, OrderCreatedDate, OrderModifiedDate, NoofGuest, isRequired, AssignedTo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssiis", $ordertype, $time, $phone, $email, $orderremark, $status, $ordercreateddate, $ordermodifieddate, $noofguest, $isRequired, $assignedTo);

    // Set parameters and execute
    $ordertype = "Limo";
    //$time = $_POST['pickupTime'];

    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $vehicleType = isset($_POST['vehicleType']) ? $_POST['vehicleType'] : '';
    $orderremark = $vehicleType . ' | ' . (isset($_POST['comment']) ? $_POST['comment'] : '');
    $status = "TBC";
    $ordercreateddate = date('Y-m-d H:i:s');
    $ordermodifieddate = date('Y-m-d H:i:s');
    $noofguest = isset($_POST['guests']) ? $_POST['guests'] : '';
    $isRequired = 0; // Default value for isRequired field
    $assignedTo = ''; // Default value for AssignedTo field
    
    $checkStock = $conn->prepare("SELECT daily_quantity FROM hotelvehicletype WHERE VehicleType = ?");
    $checkStock->bind_param("s", $vehicleType);
    $checkStock->execute();
    $stockResult = $checkStock->get_result();
    
    if ($stockResult->num_rows > 0) {
        $stockRow = $stockResult->fetch_assoc();
        $availableVehicles = $stockRow['daily_quantity'];
        
        if ($availableVehicles <= 0) {
            echo "<script type='text/javascript'>alert('Sorry, no vehicles available for " . $vehicleType . "');</script>";
            $checkStock->close();
            $conn->close();
        } else {
            $deductStock = $conn->prepare("UPDATE hotelvehicletype SET daily_quantity = daily_quantity - 1 WHERE VehicleType = ? AND daily_quantity > 0");
            $deductStock->bind_param("s", $vehicleType);
            $deductStock->execute();
            $deductStock->close();
        }
    }
    $checkStock->close();
    
    // Retrieve the date and time from the form
    $bookingDate = isset($_POST['bookingDate']) ? $_POST['bookingDate'] : '';
    $bookingTime = isset($_POST['bookingTime']) ? $_POST['bookingTime'] : '';

    // Combine the date and time into a single DateTime object
    $datetime = new DateTime($bookingDate . ' ' . $bookingTime);

    // Format the DateTime object as a string in the 'Y-m-d H:i:s' format
    $time = $datetime->format('Y-m-d H:i:s');

 //   $stmt->execute();

    if ($stmt->execute()) {
        echo "<script type='text/javascript'>alert('New Limo Order created successfully');</script>";
    } else {
        echo "<script type='text/javascript'>alert('There was an error');</script>";
    }
    $stmt->close();
    $conn->close();
}
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
                                       <input type="text" class="form-control" id="email" name="email" placeholder="Username/Email" required>
                                </div>
                            </div>
							
                            <div class="col-md-12">
                <div class="form-group">
                    <label for="phone">Contact number</label>
                    <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone Number" required>
                </div>
            </div>
							 <!-- Dropdown list -->

<!-- Pickup Option -->


<?php
$servername = "localhost";
$username = "root";
$password = "123456";
$dbname = "hmis";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT VehicleType, daily_quantity FROM hotelvehicletype WHERE status = 1 AND daily_quantity > 0";
$result = $conn->query($sql);
?>

<div class="col-md-12">
    <div class="form-group">
        <label for="luxurycars">Luxury Cars</label>
        <select class="form-control" id="luxurycars" name="vehicleType" onchange="updateVehicleInfo()">
            <option value="">Select a Luxury Car</option>
            <?php
            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
                    echo "<option value=\"" . $row["VehicleType"] . "\" data-quantity=\"" . $row["daily_quantity"] . "\">" . $row["VehicleType"] . " (Available: " . $row["daily_quantity"] . ")</option>";
                }
            } else {
                echo "0 results";
            }
            ?>
        </select>
    </div>
</div>
<div class="col-md-12">
    <div id="vehicleInfo" class="help-block" style="color: #666; font-size: 14px;"></div>
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
    var select = document.getElementById("bookingTime");
var hours, minutes, ampm;
for(var i = 1800; i <= 82800; i += 1800){
    hours = Math.floor(i / 3600);
    minutes = Math.floor((i % 3600) / 60);
    ampm = hours >= 12 ? 'pm' : 'am';
    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
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

if (vehicleSelect) {
    vehicleSelect.addEventListener('change', function() {
        updateLimoImages();
        updateVehicleInfo();
    });
}

updateLimoImages();

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
    
    var itemType = 'Limo';
    var itemName = vehicleType;
    var itemPrice = 0; // Price could be calculated based on vehicle type
    var itemDetails = 'Destination: ' + destination + ', Comment: ' + comment;
    
    addToCart(itemType, itemName, itemPrice, date, time, guests, itemDetails);
});
</script>
  </body>
</html>


