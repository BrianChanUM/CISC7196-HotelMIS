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
									<img src="images/1.jpg" />
									<div class="description">
										<input type="checkbox" id="show-description-1"/>
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
										<input type="checkbox" id="show-description-2"/>
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
										<input type="checkbox" id="show-description-3"/>
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
										<input type="checkbox" id="show-description-4"/>
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
										<input type="checkbox" id="show-description-5"/>
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
                           
                            <h3>HMIS SignUp Portal- Kindly input the following details.</h3>
                          
                            <div class="clearfix"></div>
                        </div>
						
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
   
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <label for="adminEmployeeID">Login ID</label>
            <input type="text" class="form-control" id="adminEmployeeID" name="adminEmployeeID" placeholder="Login ID" pattern="\d{6}" title="Incorrect Employee ID" required>
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="adminEmail">Email address</label>
            <input type="email" class="form-control" id="adminEmail" name="adminEmail" placeholder="Email" pattern="[a-z0-9._%+-]+@MIS\.com$" title="Incorrect Email address" required>
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="adminPassword">Password</label>
            <input type="password" class="form-control" id="adminPassword" name="adminPassword" placeholder="Password" required oninput="checkPasswordSecurity(this)">
            <div id="passwordStrengthBar" style="height: 8px;"></div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label for="adminPasswordConfirm">Confirm Password</label>
            <input type="password" class="form-control" id="adminPasswordConfirm" name="adminPasswordConfirm" placeholder="Confirm Password" required oninput="checkPasswordMatch(this)">
        </div>
    </div>
	
	
  <!--  <div class="col-md-12">
        <div class="form-group">
            <label for="adminDeptCode">Role Code</label>
            <input type="text" class="form-control" id="adminDeptCode" name="adminDeptCode" placeholder="Role Code" required>
        </div>
    </div>  -->
    <div class="col-md-12">
        <div class="form-group">
            <button type="submit" class="btn tf-btn btn-success">Create account</button>
        </div>
    </div>
</div>

<div id="successMessage" style="display: none;">
    <p>Account created successfully!</p>
</div>
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
 <script>
 var user = null;
        var navbar = document.getElementById('navbar');

        if(user) {
            navbar.innerHTML += '<li><a href="#" class="page-scroll"><i class="fas fa-user"></i> Welcome, ' + user.username + '</a></li>';
            if(user.type == 'admin') {
                navbar.innerHTML += '<li><a href="admin.html" class="page-scroll">User Panel</a></li>';
            }
           navbar.innerHTML += '<li><a href="#" id="logout" class="page-scroll">Logout</a></li>';
        } else {
          
		navbar.innerHTML += '<li><a href="Signup.php" class="page-scroll">Signup</a></li>'; 
 
 
	function checkPasswordSecurity(input) {
    var password = input.value;
    var strengthBar = document.getElementById('passwordStrengthBar');
    
    // Check if the password length is at least 8 characters
    if (password.length < 8) {
        input.setCustomValidity("Password must be at least 8 characters long.");
        strengthBar.style.width = '10%';
        strengthBar.style.backgroundColor = 'red';
    } 
    // Check if the password contains at least one uppercase letter, one lowercase letter, and one symbol
    else if (!/[A-Z]/.test(password) || !/[a-z]/.test(password) || !/[\W]/.test(password)) {
        input.setCustomValidity("Password must contain at least one uppercase letter, one lowercase letter, and one symbol.");
        strengthBar.style.width = '15%';
        strengthBar.style.backgroundColor = 'orange';
    } 
    else {
        input.setCustomValidity("");
        strengthBar.style.width = '30%';
        strengthBar.style.backgroundColor = 'green';
    }
}

function checkPasswordMatch(input) {
    var password = document.getElementById('adminPassword').value;
    var confirmPassword = input.value;
    // Check if the confirmed password matches the original password
    if (password !== confirmPassword) {
        input.setCustomValidity("Passwords do not match.");
    } else {
        input.setCustomValidity("");
    }
}
<!--function submitForm() {
    // Add your form submission logic here
    // If the form is successfully submitted, display the success message
 //   document.getElementById('successMessage').style.display = 'block';
//}
//function submitForm() {
    // Add your form submission logic here
    // If the form is successfully submitted, display the success message and redirect
  //  window.alert('Account created successfully!');
    //window.location.href = 'index.php';}
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $UserName = $_POST["adminEmployeeID"];
    $Password = $_POST["adminPassword"];
    $Role = $_POST["adminDeptCode"];
    $CreateDate = date("Y-m-d H:i:s");
    $ModifiedDate = date("Y-m-d H:i:s");
    $Email = $_POST["adminEmail"];

    // Check if username or email already exists
    $checkUserSql = "SELECT * FROM user WHERE UserName='$UserName' OR Email='$Email'";
    $result = $conn->query($checkUserSql);

    if ($result->num_rows > 0) {
        echo "<script type='text/javascript'>alert('Username or Email already exists. Please try again with different credentials.'); window.location.href = 'index.php';</script>";
    } else {
        $sql = "INSERT INTO user (UserName, Password, Role, CreateDate, ModifiedDate, Email)
        VALUES ('$UserName', '$Password', 'guest', '$CreateDate', '$ModifiedDate', '$Email')";

        if ($conn->query($sql) === TRUE) {
            // Get the last inserted ID
            $last_id = $conn->insert_id;

            // Insert into userprofile table
            $sql2 = "INSERT INTO userprofile (UID, Department, Level, SalaryRate, OnboardDate, ModifiedDate)
            VALUES ('$last_id', 'Department', 'Level', 'SalaryRate', '$CreateDate', '$ModifiedDate')";

            if ($conn->query($sql2) === TRUE) {
                echo "<script type='text/javascript'>alert('Your Account and Profile have been created successfully!'); window.location.href = 'index.php';</script>";
            } else {
                echo "Error: " . $sql2 . "<br>" . $conn->error;
            }
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$conn->close();
?>