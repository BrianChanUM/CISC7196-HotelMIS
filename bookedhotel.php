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
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome/css/font-awesome.css">

    <!-- Slider
    ================================================== -->
    <link href="css/owl.carousel.css" rel="stylesheet" media="screen">
    <link href="css/owl.theme.css" rel="stylesheet" media="screen">
	  <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Stylesheet
    ================================================== -->
    <link rel="stylesheet" type="text/css"  href="css/style.css">
	<link rel="stylesheet" type="text/css"  href="css/bookedhotel.css">
    <link rel="stylesheet" type="text/css" href="css/responsive.css">

    <link href='http://fonts.googleapis.com/css?family=Lato:100,300,400,700,900,100italic,300italic,400italic,700italic,900italic' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,700,300,600,800,400' rel='stylesheet' type='text/css'>

    <script type="text/javascript" src="js/modernizr.custom.js"></script>
<style>
   .price-display {
    font-size: 2em; /* Adjust as needed */
    color: #008CBA; /* Adjust as needed */
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
									<img src="img/hotel/1.jpg" />
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
									<img src="img/hotel/2.jpg" />
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
									<img src="img/hotel/3.jpg" />
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
									<img src="img/hotel/4.jpg" />
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
									<img src="img/hotel/5.jpg" />
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
    <div class="section-title">
        <h3>Kindly input the following details for our hotel booking service.</h3>
        <div class="clearfix"></div>
    </div>
    <form action="bookedhotel.php" method="post">
        <div class="row">
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

$sql = "SELECT DISTINCT(HotelRoomtype) FROM hotelroomtype"  ;
$result = $conn->query($sql);
?>

            <div class="col-md-12">
                <div class="form-group">
                    <label for="hotel">Hotel</label>
                      <select class="form-control" id="hotel">
					  <option value="">Select a Room Type</option>
            <?php
            if ($result->num_rows > 0) {
                // output data of each row
                while($row = $result->fetch_assoc()) {
                    echo "<option value=\"" . $row["HotelRoomtype"] . "\">" . $row["HotelRoomtype"] . "</option>";
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
                    <label for="checkInDate">Check-in Date</label>
                    <input type="date" class="form-control" id="checkInDate" name="checkInDate">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="checkOutDate">Check-out Date</label>
                    <input type="date" class="form-control" id="checkOutDate" name="checkOutDate">
                </div>
            </div>
<!-- Price -->
<div class="col-md-12">
    <div class="form-group">
        <label for="price">Price / Night</label>
        <p id="price" class="price-display">$0.00</p>
		
	
		
    </div>
</div>
			

			
			 <!-- Number of guests -->
            <div class="col-md-12">
                <div class="form-group">
                    <label for="guests">Number of Guests</label>
                    <input type="number" class="form-control" id="guests" name="guests" min="1" max="10">
                </div>
            </div>
			
            <div class="col-md-12">
                <div class="form-group">
                    <label for="comment">Comment</label>
                    <input type="text" class="form-control" id="comment" name="comment" placeholder="Leave your request here">
                </div>
            </div>
        </div>
        <button type="submit" class="btn tf-btn btn-success">Submit Booking</button>
    </form>
	
		<div id="myModal" class="modal">
  <!-- Modal content -->
  <div class="modal-content">
	<img src="img/hotel/logo.png" alt="Logo" style="width:100px; height:auto;"> <!-- Add this line -->
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
  const today = new Date().toISOString().split('T')[0];
    const checkInDateInput = document.getElementById('checkInDate');
    const checkOutDateInput = document.getElementById('checkOutDate');

 // Set minimum date for check-in
    checkInDateInput.setAttribute('min', today);
	

    // Set minimum date for check-out (one day after check-in)
    checkInDateInput.addEventListener('change', () => {
        const checkInDate = new Date(checkInDateInput.value);
        const nextDay = new Date(checkInDate);
        nextDay.setDate(checkInDate.getDate() + 1);
        const formattedNextDay = nextDay.toISOString().split('T')[0];
        checkOutDateInput.setAttribute('min', formattedNextDay);
    });

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

<script>




    const hotelSelect = document.getElementById('hotel');
    const priceDisplay = document.getElementById('price');

	
    hotelSelect.addEventListener('change', () => {
        const selectedHotelRoomType = hotelSelect.value;
        // Make an AJAX request to get the price
        fetch('function/get_price.php?roomType=' + selectedHotelRoomType)
            .then(response => response.json())
            .then(data => {
                if (data.price !== 'N/A') {
                    priceDisplay.textContent = '$' + data.price;
                } else {
                    priceDisplay.textContent = 'Price not available';
                }
            })
            .catch(error => {
                console.error('Error fetching price:', error);
            });
    });
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
$stmt = $conn->prepare("INSERT INTO orderbookings (OrderType, Time, Email, OrderRemark, Status, OrderCreatedDate, OrderModifiedDate) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $ordertype, $time, $email, $orderremark, $status, $ordercreateddate, $ordermodifieddate);

date_default_timezone_set('Asia/Shanghai');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	// Retrieve the date and time from the form
$checkInDate = $_POST['checkInDate'];
$checkOutDate = $_POST['checkOutDate'];

$time = $checkInDate . ' to ' . $checkOutDate;
// Set parameters and execute
$ordertype = "Hotel";
$time = $time; // Use the formatted date and time
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
        modalText.innerHTML = 'Your order has been well receieved. <br><b>Your Hotel order ID is: Hotel " . $last_id . "</b>.<br>ContactNo. " . $phone . "</br>No. of Guests: " . $noofguest . "<br>Expected Arrival date  " . $time . "';
        modal.style.display = 'block'; // Show the modal
    </script>
    ";
} else {
    echo "<script type='text/javascript'>alert('There was an error');</script>";
}}

$stmt->close();
$conn->close();
?>
