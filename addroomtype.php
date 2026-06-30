<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/session_check.php';
require_once __DIR__ . '/config/language.php';
require_once __DIR__ . '/function/check_permission.php';
require_once __DIR__ . '/config/db_config.php';
requirePermission('admin_rooms', 'create', 'index.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = getDBConnection();
    
    $roomType = isset($_POST['HotelRoomtype']) ? $_POST['HotelRoomtype'] : '';
    $roomPrice = isset($_POST['HotelRoomPrice']) ? $_POST['HotelRoomPrice'] : '';
    $dailyQuantity = isset($_POST['dailyQuantity']) ? intval($_POST['dailyQuantity']) : 1;
    $status = isset($_POST['status']) ? 1 : 0;
    
    $imagePath = '';
    if (isset($_FILES['roomImage']) && $_FILES['roomImage']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/img/hotel/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileName = basename($_FILES['roomImage']['name']);
        $targetFilePath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['roomImage']['tmp_name'], $targetFilePath)) {
            $imagePath = 'img/hotel/' . $fileName;
        }
    }

    $checkStmt = $conn->prepare("SELECT HotelRoomtype FROM hotelroomtype WHERE HotelRoomtype = ?");
    $checkStmt->execute([$roomType]);
    
    if ($checkStmt->rowCount() > 0) {
        $errorMessage = "Sorry, that room type already exists. Please double-check.";
    } else {
        $insertStmt = $conn->prepare("INSERT INTO hotelroomtype (HotelRoomtype, HotelRoomPrice, daily_quantity, image_path, status) VALUES (?, ?, ?, ?, ?)");
        if ($insertStmt->execute([$roomType, $roomPrice, $dailyQuantity, $imagePath, $status])) {
            $successMessage = "The RoomType " . htmlspecialchars($roomType) . " has been created successfully!";
        } else {
            $errorMessage = "Error creating room type.";
        }
    }
    
    closeDBConnection($conn);
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
					<div class="section-title">
                           
                            <h3>To Creat New Room Type</h3>
                          
                            <div class="clearfix"></div>
                        </div>
						
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data"> 
   
	<div class="row">
       

		 <div class="col-md-12">
        <div class="form-group">
            <label for="HotelRoomtype">Room Type:</label>
            <input type="text" class="form-control" id="HotelRoomtype" name="HotelRoomtype" placeholder="Room Type Name"   required>
        </div>
    </div>
		
		 <div class="col-md-12">
        <div class="form-group">
            <label for="HotelRoomPrice">Room Price:</label>
            <input type="text" class="form-control" id="HotelRoomPrice" name="HotelRoomPrice" placeholder="Price per night"   required>
        </div>
    </div>
	
	<div class="col-md-12">
        <div class="form-group">
            <label for="dailyQuantity">Daily Quantity (Number of rooms):</label>
            <input type="number" class="form-control" id="dailyQuantity" name="dailyQuantity" placeholder="Available rooms per day" value="1" min="1" required>
        </div>
    </div>
	
	<div class="col-md-12">
        <div class="form-group">
            <label for="roomImage">Room Image:</label>
            <input type="file" class="form-control" id="roomImage" name="roomImage" accept="image/*">
        </div>
    </div>
	
	<div class="col-md-12">
        <div class="form-group">
            <label>
                <input type="checkbox" name="status" value="1" checked> Enable this room type
            </label>
        </div>
    </div>
		
<div class="col-md-12">
                <div class="form-group">
        <!-- Add other relevant fields here -->
		<button type="submit" class="btn tf-btn btn-primary">Create New Room Type</button>
		
		  
          </div>
            </div>
		
		
		
       <!-- <input type="submit" value="Add Outlet"> -->
	       </div>

</form>
					
            
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

<?php
if (isset($successMessage)) {
    echo "<script>alert('" . $successMessage . "');</script>";
}

if (isset($errorMessage)) {
    echo "<script>alert('" . $errorMessage . "');</script>";
}
?>