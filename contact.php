<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Basic Page Needs
    ================================================== -->
    <meta charset="utf-8">
    <!--[if IE]>
			<meta http-equiv="x-ua-compatible" content="IE=9" />
			<![endif]-->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HotelMIS-OCT18</title>
    
    <!-- Favicons
    ================================================== -->
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="img/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="img/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="img/apple-touch-icon-114x114.png">
    <!-- Bootstrap -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome/css/font-awesome.css">
	  <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Slider
    ================================================== -->
    <link href="css/owl.carousel.css" rel="stylesheet" media="screen">
    <link href="css/owl.theme.css" rel="stylesheet" media="screen">
    <!-- Stylesheet
    ================================================== -->
    <link rel="stylesheet" type="text/css" href="css/style.css">
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
        <!-- Collect the nav links, forms, and other content for toggling -->
        <style>
          .paging {
            background-color: grey;
            color: black;
          }
        </style>
		
		<style>
    .radio-buttons {
        display: flex;
        align-items: center;
    }
    .radio-buttons label {
        margin-right: 10px;
    }
</style>
<?php
    session_start();
    $user = json_encode($_SESSION);
	
?>

<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
    <?php include(__DIR__ . '/layout/header.php');?>
    <ul class="nav navbar-nav navbar-right" id="navbar"></ul>
	<?php include(__DIR__ . '/layout/navbar.php');?>

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
$etypes = [];

// New query to fetch etype
$etype_query = "SELECT etype FROM enquirytype WHERE estatus = 1";
$etype_result = $conn->query($etype_query);

if ($etype_result->num_rows > 0) {
    // output data of each row
    while($row = $etype_result->fetch_assoc()) {
        $etypes[] = $row["etype"];
    }
} else {
    echo "0 results";
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //var_dump($_POST);
    $eUser = $_POST['eUser'];
    $eEmail = $_POST['eEmail'];
    $ePhone = $_POST['ePhone'];
    $eType = $_POST['eType'];
    $eContent = $_POST['eContent'];
    $eIsCall = ($_POST['eIsCall'] == 'yes') ? 1 : 0;
    $eCreatedDate = date("Y-m-d H:i:s");

    $sql = "INSERT INTO enquiry (eUser, eEmail, ePhone, eType, eContent, eIsCall, eCreatedDate) VALUES ('$eUser', '$eEmail', '$ePhone', '$eType', '$eContent', '$eIsCall', '$eCreatedDate')";
    if ($conn->query($sql) === TRUE) {
        echo "<script type='text/javascript'>alert('Thank you for your contact, your enquiry has been submitted successfully!');</script>";
    } else {
        echo "Error: " . "<br>" . $conn->error;
    }
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn->close();
?>
	
</div><!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
    </nav>
    <!-- Home Page
    ==========================================-->
    <div id="tf-home" class="text-center">
      <a href="#tf-contact"></a>
    </div>
    <div id="tf-about">
      <div class="container">
        <div class="row">
          <div class="col-md-6">
            <div id="slideshow-wrap">
              <input type="radio" id="button-1" name="controls" checked="checked" />
              <label for="button-1"></label>
              <input type="radio" id="button-2" name="controls" />
              <label for="button-2"></label>
              <input type="radio" id="button-3" name="controls" />
              <label for="button-3"></label>
              <input type="radio" id="button-4" name="controls" />
              <label for="button-4"></label>
              <input type="radio" id="button-5" name="controls" />
              <label for="button-5"></label>
              <div id="slideshow-inner">
                <ul>
                  <li id="slide1">
                    <img src="images/1.jpg" />
                    <div class="description">
                      <input type="checkbox" id="show-description-1" />
                      <label for="show-description-1" class="show-description-label">I</label>
                      <div class="description-text">
                        <h2>Eleganant Glance</h2>
                        <p>We provide the best serenity of peace of mind with confortability stature</p>
                      </div>
                    </div>
                  </li>
                  <li id="slide2">
                    <img src="images/2.jpg" />
                    <div class="description">
                      <input type="checkbox" id="show-description-2" />
                      <label for="show-description-2" class="show-description-label">1</label>
                      <div class="description-text">
                        <h2>Superior rom</h2>
                        <p>These are rooms design to suite your high class confortability . </p>
                      </div>
                    </div>
                  </li>
                  <li id="slide3">
                    <img src="images/3.jpg" />
                    <div class="description">
                      <input type="checkbox" id="show-description-3" />
                      <label for="show-description-3" class="show-description-label">2</label>
                      <div class="description-text">
                        <h2>VIP rooms</h2>
                        <p>These are room design to value your money and status of life.</p>
                      </div>
                    </div>
                  </li>
                  <li id="slide4">
                    <img src="images/4.jpg" />
                    <div class="description">
                      <input type="checkbox" id="show-description-4" />
                      <label for="show-description-4" class="show-description-label">3</label>
                      <div class="description-text">
                        <h2>Mwanainchi Rooms</h2>
                        <p>We have rooms to suite your pocket with lots of enjoyment</p>
                      </div>
                    </div>
                  </li>
                  <li id="slide5">
                    <img src="images/5.jpg" />
                    <div class="description">
                      <input type="checkbox" id="show-description-5" />
                      <label for="show-description-5" class="show-description-label">4</label>
                      <div class="description-text">
                        <h2>Deluxe Rooms!</h2>
                        <p>We design our room to meet international and Pan-African style for our customer.</p>
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="section-title">
              <h3>Kindly fill the Enquiry form below.</h3>
              <div class="clearfix"></div>
            </div>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label for="eUser">Name</label>
                     <input type="text" class="form-control" id="eUser" name="eUser" placeholder="Enter your name"  required>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label for="eEmail">Email address</label>
                    <input type="email" class="form-control" id="eEmail" name="eEmail" placeholder="Enter email" required>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label for="ePhone">Phone Number</label>
                    <input type="tel" class="form-control" id="ePhone" name="ePhone" placeholder="Enter phone number" required>
					 <div class="form-group">
    <label for="eIsCall">Do you want to contact by phone?</label><br>
    <div class="radio-buttons">
        <label for="yes"><input type="radio" id="yes" name="eIsCall" value="yes"> Yes</label>
        <label for="no"><input type="radio" id="no" name="eIsCall" value="no"> No</label>
    </div>
</div>
				  
				  </div>
                </div>
                <div class="col-md-12">
    <div class="form-group">
        <label for="eType">Enquiry Type</label>
        <select class="form-control" id="eType" name="eType" required>
            <?php foreach ($etypes as $etype): ?>
                <option value="<?php echo $etype; ?>"><?php echo $etype; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>


                <div class="col-md-12">
                  <div class="form-group">
                    <label for="eContent">Message</label>
                    <textarea class="form-control" id="eContent" name="eContent" rows="5" required></textarea>
                  </div>
                </div>
              </div>
              <button type="submit" class="btn tf-btn btn-success">Submit</button>
            </form>
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

	
	
  </body>
</html> 
