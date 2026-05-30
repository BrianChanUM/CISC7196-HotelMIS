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
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
    <ul class="nav navbar-nav navbar-right" id="navbar">
	
	</ul>
    <ul class="nav navbar-nav navbar-left">
        <li><a href="index.php" class="page-scroll">Home</a></li>
        <li><a href="services.html" class="page-scroll">Services</a></li>
        <li><a href="hotel.html" class="page-scroll">Portfolio</a></li>
        <li><a href="limo.html" class="page-scroll">Limo Service</a></li>
        <li><a href="contact.php" class="page-scroll">Contact</a></li>
    </ul>
</div><!-- /.navbar-collapse -->
      </div><!-- /.container-fluid -->
    </nav>

    <!-- Home Page
    ==========================================-->
    <div id="tf-home" class="text-center">
	<a href="#tf-about"></a>
       
    </div>

    <!-- About Us Page
    ==========================================-->
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
				    <div class="row">
<div class="col-md-12">
    <table class="table">
        <thead>
            <tr>
                <th>Username/Email address</th>
                <th>Name</th>
                <th>Email address</th>
                <th>Password</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><input type="text" class="form-control" id="username" placeholder="Username/Email" value="<?php echo $user['username']; ?>" readonly></td>
                <td><input type="text" class="form-control" id="name" placeholder="Enter your name" value="<?php echo $user['name']; ?>" readonly></td>
                <td><input type="email" class="form-control" id="email" placeholder="Enter email" value="<?php echo $user['email']; ?>" readonly></td>
                <td><input type="password" class="form-control" id="password" placeholder="Password" value="<?php echo $user['password']; ?>" readonly></td>
            </tr>
            <td><button class="btn btn-primary" onclick="editUser()">Edit</button></td>
        </tbody>
    </table>
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
    var user = <?php echo $user; ?>;
    var navbar = document.getElementById('navbar');

 if(user && user.username) {
        navbar.innerHTML += '<li><a href="#" class="page-scroll"><i class="fas fa-user"></i> Welcome, ' + user.username + ' (' + user.role + ')</a></li>';
        if(user.role == 'admin') {
            navbar.innerHTML += '<li><a href="admin.html" class="page-scroll">AdminPanel</a></li>';
        } else if(user.role == 'staff') { navbar.innerHTML += '<li><a href="admin.html" class="page-scroll">StaffPanel</a></li>';
        } 
        navbar.innerHTML += '<li><a href="#" id="logout" class="page-scroll">Logout</a></li>';
    } else {

        navbar.innerHTML += '<li><a href="login.php" class="page-scroll">Login</a></li>';
        navbar.innerHTML += '<li><a href="signup.php" class="page-scroll">Signup</a></li>'; 
    }

    document.getElementById('logout').addEventListener('click', function() {
    var confirmLogout = window.confirm('Are you sure you want to logout?');
    if(confirmLogout) {
        // Call a PHP function to destroy the session
        fetch('logout.php')
        .then(response => response.text())
        .then(data => {
            if(data == 'success') {
                window.alert('Logout successful!');
                window.location.href = 'index.php';
            } else {
                window.alert('Logout failed!');
            }
        });
    }
});
</script>
  </body>
</html>


