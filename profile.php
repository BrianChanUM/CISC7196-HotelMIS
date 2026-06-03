<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/language.php';
require_once __DIR__ . '/config/db_config.php';

// Connect to the database using config
$db = new PDO('mysql:host=' . $db_config['host'] . ';dbname=' . $db_config['dbname'], $db_config['username'], $db_config['password']);
// Check if the user exists
$query = $db->prepare('SELECT * FROM user WHERE UserName = :username OR Email = :username');
$query->execute(['username' => $_SESSION['username']]);
$userprofile = $query->fetch(PDO::FETCH_ASSOC);

if($userprofile) {
    // User found, set the session username to the username from the database
    $_SESSION['username'] = $userprofile['UserName'];
    $_SESSION['role'] = $userprofile['Role'];

    // Fetch the user profile if the role is not 'guest'
    if($_SESSION['role'] != 'guest') {
        $query = $db->prepare('SELECT * FROM user INNER JOIN userprofile ON user.UID = userprofile.UID WHERE UserName = :username');
        $query->execute(['username' => $_SESSION['username']]);
        $userprofile = $query->fetch(PDO::FETCH_ASSOC);
    }
} else {
    // User not found, handle the error
    echo "Invalid username or email";
}
// Encode the user data into a JSON string for use in other pages
$user = json_encode($_SESSION);
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
          <a class="navbar-brand" href="index.php"><?php echo t('hotel_management_system'); ?></a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->


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
    <div id="tf-home" class="text-right">

</div>
  
	
	<div id="tf-about" class="text-center">  <h2>User Profile</h2> 
<div id="tf-about" class="text-right">  
 <div class="container" >
    
        <form id="editUserForm" method="POST" action="ResetCurrentPW.php">
            <table class="table">
                <tbody>

                    <tr>
                        <td>Profile Name:</td>
                        <td><input type="text" class="form-control" id="name" value="<?php echo $userprofile['UserName']; ?>" readonly></td>
                    </tr>
                    <tr>
                        <td>Email:</td>
                        <td><input type="text" class="form-control" id="email" value="<?php echo $userprofile['Email']; ?>" readonly></td>
                    </tr>
                    <tr>
                        <td>Role:</td>
                        <td><input type="text" class="form-control" id="role" value="<?php echo $userprofile['Role']; ?>" readonly></td>
                    </tr>
					                    <tr>
                        <td>Password:</td>
                        <td><input type="password" class="form-control" id="password" value="<?php echo $userprofile['Password']; ?>" readonly></td>
                    </tr>
                    <tr>
                        <td>Create Date:</td>
                        <td><input type="text" class="form-control" id="createDate" value="<?php echo $userprofile['CreateDate']; ?>" readonly></td>
                    </tr>
					<tr>
                        <td>Last Modified Date:</td>
                        <td><input type="text" class="form-control" id="ModifiedDate" value="<?php echo $userprofile['ModifiedDate']; ?>" readonly></td>
                    </tr>
					<tr>
                <?php if($userprofile['Role'] != 'guest'): ?>
                <tr>
                    <td>Department:</td>
                    <td><input type="text" class="form-control" id="department" value="<?php echo $userprofile['Department']; ?>" readonly></td>
                </tr>
                <tr>
                    <td>Level:</td>
                    <td><input type="text" class="form-control" id="level" value="<?php echo $userprofile['Level']; ?>" readonly></td>
                </tr>
                <tr>
                    <td>Salary Rate:</td>
                    <td><input type="text" class="form-control" id="salaryRate" value="<?php echo $userprofile['SalaryRate']; ?>" readonly></td>
                </tr>
                <tr>
                    <td>Onboard Date:</td>
                    <td><input type="text" class="form-control" id="onboardDate" value="<?php echo $userprofile['OnboardDate']; ?>" readonly></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

			<button type="button" id="changeButton" class="btn btn-primary">Edit Profile</button>
			
        </form>
    </div> 

    </div>
 </div>

    <nav id="footer">
        <div class="container">
            <div class="pull-left fnav">
                  <p>ALL RIGHTS RESERVED. COPYRIGHT © 2023. Designed by BC<a href="#"> CISC7196-HotelMIS-2023OCT18</a> </p>
            </div>
            <div class="pull-right fnav">
                <ul class="footer-social">
                    <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                    <li><a href="#"><i class="fa fa-dribbble"></i></a></li>
                    <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                    <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                </ul>
            </div>
        </div>
    </nav>


    <!-- Add your JavaScript links here -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>


    <!-- Javascripts
    ================================================== -->
    <script type="text/javascript" src="js/main.js"></script>
     <script>
document.getElementById('changeButton').addEventListener('click', function() {
    // Create a new iframe
    var iframe = document.createElement('iframe');

    // Set the iframe attributes
    iframe.src = 'profile.php';
    iframe.width = '100%';
    iframe.height = '600';

    // Remove any existing iframes
    var iframeContainer = document.getElementById('iframeContainer');
    while (iframeContainer.firstChild) {
        iframeContainer.firstChild.remove();
    }

    // Add the new iframe to the container
    iframeContainer.appendChild(iframe);
});

document.getElementById('changeButton').addEventListener('click', function() {
    // Open a new window with 'profile.php'
    window.open('profiledetails.php', '_blank', 'width=600,height=600');
});

    </script>
  </body>
</html>

<style>
.table {
    width: 100%;
    margin: 20px 0;
    border-collapse: collapse;
}

.table td, .table th {
    border: 1px solid #ddd;
    padding: 4px;
}

.table tr:nth-child(even) {
    background-color: #ffe6e6; /* Light red for even rows */

}

.table tr:nth-child(odd) {
    background-color: #e6f2ff; /* Light blue for odd rows */
}

.table tr:hover {
    background-color: #cccccc; /* Gray color on hover */
}

.table th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
    background-color: #4CAF50; /* Green color for headers */
    color: white;
}

</style>