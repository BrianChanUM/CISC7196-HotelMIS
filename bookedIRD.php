<!DOCTYPE html>
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
	<link rel="stylesheet" type="text/css"  href="css/bookedIRD.css">
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
          <a class="navbar-brand" href="index.php">HotelMIS </a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling --><style>.paging{background-color:grey; color:black;}</style>
<?php
    session_start();
    $user = json_encode($_SESSION);
?>




<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
    <?php include(__DIR__ . '/layout/header.php');?>
    <ul class="nav navbar-nav navbar-right" id="navbar"></ul>
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
									<img src="img/ird/1.jpg" />
									<div class="description">
										<input type="checkbox" id="show-description-1"/>
										<label for="show-description-1" class="show-description-label">I</label>
									
									</div>
								</li>
								<li id="slide2">
									<img src="img/ird/2.jpg" />
									<div class="description">
										<input type="checkbox" id="show-description-2"/>
										<label for="show-description-2" class="show-description-label">1</label>
										
									</div>
								</li>
								<li id="slide3">
									<img src="img/ird/3.jpg" />
									<div class="description">
										<input type="checkbox" id="show-description-3"/>
										<label for="show-description-3" class="show-description-label">2</label>
										
									</div>
								</li>
								<li id="slide4">
									<img src="img/ird/4.jpg" />
									<div class="description">
										<input type="checkbox" id="show-description-4"/>
										<label for="show-description-4" class="show-description-label">3</label>
										
									</div>
								</li>
								<li id="slide5">
									<img src="img/ird/5.jpg" />
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
        <h3>Welcome to visit our In Room Dining Reservation service.</h3>
        <div class="clearfix"></div>
    </div>
	

	
	
	
	
	
	
    <form action="bookedIRD.php" method="post">
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
			
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "HMIS";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT DISTINCT(OutletName) FROM hoteloutlet WHERE STATUS = 1 and style = 'IRD'"  ;
$result = $conn->query($sql);
?>			
			
			
            <div class="col-md-12">
                <div class="form-group">
                    <label for="outlet">Service Type</label>
                    <select class="form-control" id="outlet">
					<option value="">Select a Room Service Type</option>
                        <?php
            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
                    echo "<option value=\"" . $row["OutletName"] . "\">" . $row["OutletName"] . "</option>";
                }
            } else {
                echo "0 results";
            }
            ?>
                    </select>
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
    </form>
			<div id="myModal" class="modal">
  <!-- Modal content -->
  <div class="modal-content">
	<img src="img/ird/logo.png" alt="Logo" style="width:100px; height:auto;"> <!-- Add this line -->
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

<script>
    // Start and end times (24 hour clock)
    var startTime = 06;
    var endTime = 9;
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

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}
	
	
</script>

  </body>
</html>

	<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "HMIS";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO orderbookings (OrderType,Time,ContactNo, Email, OrderRemark, Status, OrderCreatedDate, OrderModifiedDate, NoofGuest) VALUES (?, ?,?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssssi", $ordertype, $time, $phone, $email, $orderremark, $status, $ordercreateddate, $ordermodifieddate, $noofguest);

date_default_timezone_set('Asia/Shanghai');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
// Set parameters and execute
$ordertype = "IRD";

// Retrieve the date and time from the form
$bookingDate = $_POST['bookingDate'];
$bookingTime = $_POST['bookingTime'];

// Combine the date and time into a single DateTime object
$datetime = new DateTime($bookingDate . ' ' . $bookingTime);

// Format the DateTime object as a string in the 'Y-m-d H:i:s' format
$time = $datetime->format('Y-m-d H:i:s');

$email = $_POST['email'];
$phone = $_POST['phone'];
$orderremark = $_POST['comment'];
$status = "TBC";
$ordercreateddate = date('Y-m-d H:i:s'); // Use date function instead of now()
$ordermodifieddate = date('Y-m-d H:i:s');

// Retrieve the number of guests from the form
$noofguest = $_POST['guests'];

if ($stmt->execute()) {
    $last_id = $conn->insert_id; // Get the last inserted ID
    echo "
    <script type='text/javascript'>
        var modal = document.getElementById('myModal');
        var modalText = document.getElementById('modalText');
        modalText.innerHTML = 'Your order has been well receieved. <br><b>Your order ID is: IRD " . $last_id . "</b>.<br>ContactNo. " . $phone . "</br>No. of Guests: " . $noofguest . "<br>Expected Delivery date  " . $time . "';
        modal.style.display = 'block'; // Show the modal
    </script>
    ";
} else {
    echo "<script type='text/javascript'>alert('There was an error');</script>";
}
}

$stmt->close();
$conn->close();
?>
	
	