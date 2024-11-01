<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usernameOrEmail = $_POST["username"];
    $password = $_POST["password"];

    // Create connection
    $conn = new mysqli('localhost', 'root', '', 'HMIS');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Sanitize and validate the input
    $usernameOrEmail = $conn->real_escape_string($usernameOrEmail);
    $password = $conn->real_escape_string($password);

    // Query the database
    $sql = "SELECT * FROM user WHERE (UserName = '$usernameOrEmail' OR Email = '$usernameOrEmail') AND Password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Login successful
        $row = $result->fetch_assoc();
        $_SESSION["username"] = $usernameOrEmail;
        $_SESSION["role"] = $row["Role"];
        echo "<script type='text/javascript'>alert('Login successful! Welcome back, " . $_SESSION["username"] . ". Your role is: " . $_SESSION["role"] . "'); window.location.href = 'INDEX.php';</script>";
    } else {
        // Login failed
        echo "<script type='text/javascript'>alert('Invalid username/email or password.'); window.location.href = 'login.php';</script>";
    }

    $conn->close();
}
?>


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
	   <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

        <!-- Collect the nav links, forms, and other content for toggling -->
		
<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
    <ul class="nav navbar-nav navbar-right" id="navbar"></ul>
	

    <?php include(__DIR__ . '/layout/header.php');?>
	
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
              
						
						
					</div>
                </div>
                <div class="col-md-6">
					<div class="section-title">
                           
                            <h3>This is Hotel MIS login page</h3>
							<!--<h4>Connect with our page and the world around you on hotel.</h4> -->
                            <div class="clearfix"></div>
                        </div>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="username">Username/Email address</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Username/Email" required>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
            </div>
        </div>
       <!-- <div class="col-md-12">
            <div class="form-group">
                <label for="role">Role</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="">Select Role</option>
                    <option value="admin">Admin</option>
                    <option value="staff">Staff</option>
                    <option value="guest">Guest</option>
                </select>
            </div>
        </div> -->
    </div>

		<button type="submit" class="btn tf-btn btn-primary">Log In</button>
		<a href="Signup.php" class="btn tf-btn btn-success">Create New Account</a>
		<a href="ResetPW.php" class="btn tf-btn btn-default">Forgot Password?</a>
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
}
        document.getElementById('logout').addEventListener('click', function() {
            var confirmLogout = window.confirm('Are you sure you want to logout?');
            if(confirmLogout) {
                user = null;
                window.alert('Logout successful!');
                window.location.href = 'index.php';
            }
        });
		
		
function validateLogin(username, password) {
    // Your actual validation logic goes here
    // For now, let's assume it returns a user object on success
    return {
        role: 'admin' // or 'user'
    };
}

// Call this function when the login button is clicked
function onLoginButtonClick(event) {
    event.preventDefault();

    var username = document.getElementById('username').value;
    var password = document.getElementById('password').value;

    var user = validateLogin(username, password);
    if (user) {
        // Login successful, redirect based on role
        if (user.role === 'admin') {
            window.location.href = 'login_admin.html';
        } else {
            window.location.href = 'login_user.html';
        }
    } else {
        // Login failed, show an error message
        alert('Login failed. Please check your username and password.');
    }
}

// Attach the click event to your login button
document.getElementById('loginButton').addEventListener('click', onLoginButtonClick);
		
		
    </script>

  </body>
</html>

