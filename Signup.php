<?php
require_once __DIR__ . '/config/db_config.php';
require_once __DIR__ . '/config/language.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $loginID = $_POST['adminEmployeeID'];
    $email = $_POST['adminEmail'];
    $pass = $_POST['adminPassword'];

    $conn = getDBConnection();

    $checkStmt = $conn->prepare("SELECT UID FROM user WHERE UserName = ?");
    $checkStmt->execute([$loginID]);
    if ($checkStmt->rowCount() > 0) {
        echo "<script type='text/javascript'>alert('Username already exists, please choose another username'); window.location.href = 'Signup.php';</script>";
        closeDBConnection($conn);
        exit();
    }

    $checkEmailStmt = $conn->prepare("SELECT UID FROM user WHERE Email = ?");
    $checkEmailStmt->execute([$email]);
    if ($checkEmailStmt->rowCount() > 0) {
        echo "<script type='text/javascript'>alert('Email has already been registered, please use another email'); window.location.href = 'Signup.php';</script>";
        closeDBConnection($conn);
        exit();
    }

    $hashedPass = password_hash($pass, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO user (UserName, Email, Password, Role) VALUES (?, ?, ?, 'guest')");
    $stmt->execute([$loginID, $email, $hashedPass]);

    if ($stmt->rowCount() > 0) {
        echo "<script type='text/javascript'>alert('" . t('signup_success') . "'); window.location.href = 'login.php';</script>";
    } else {
        echo "<script type='text/javascript'>alert('" . t('signup_error') . "'); window.location.href = 'Signup.php';</script>";
    }

    closeDBConnection($conn);
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=9">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo t('hotel_management_system'); ?> - <?php echo t('signup'); ?></title>
    
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="img/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="img/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="img/apple-touch-icon-114x114.png">

    <link rel="stylesheet" type="text/css"  href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome/css/font-awesome.css">
    <link href="css/owl.carousel.css" rel="stylesheet" media="screen">
    <link href="css/owl.theme.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" type="text/css"  href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/responsive.css">

    <link href='http://fonts.googleapis.com/css?family=Lato:100,300,400,700,900,100italic,300italic,400italic,700italic,900italic' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,700,300,600,800,400' rel='stylesheet' type='text/css'>

    <script type="text/javascript" src="js/modernizr.custom.js"></script>

  </head>
  <body>
    <nav id="tf-menu" class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
            <span class="sr-only">切换导航</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php"><?php echo t('hotel_management_system'); ?></a>
        </div>

        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <?php include(__DIR__ . '/layout/header.php');?>
            <ul class="nav navbar-nav navbar-right" id="navbar"></ul>
            <?php include(__DIR__ . '/layout/language_switcher.php');?>
            <?php include(__DIR__ . '/layout/navbar.php');?>
        </div>
      </div>
    </nav>

    <div id="tf-home" class="text-center">
        <a href="#tf-contact"></a>
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
                                            <h2>Elegant Glimpse</h2>
                                            <p>We provide the most comfortable tranquility and peace of mind</p>
                                        </div>
                                    </div>
                                </li>
                                <li id="slide2">
                                    <img src="images/2.jpg" />
                                    <div class="description">
                                        <input type="checkbox" id="show-description-2"/>
                                        <label for="show-description-2" class="show-description-label">1</label>
                                        <div class="description-text">
                                            <h2>Luxury Rooms</h2>
                                            <p>These rooms are designed specifically for your comfort</p>
                                        </div>
                                    </div>
                                </li>
                                <li id="slide3">
                                    <img src="images/3.jpg" />
                                    <div class="description">
                                        <input type="checkbox" id="show-description-3"/>
                                        <label for="show-description-3" class="show-description-label">2</label>
                                        <div class="description-text">
                                            <h2>VIP Rooms</h2>
                                            <p>These rooms reflect your taste and identity</p>
                                        </div>
                                    </div>
                                </li>
                                <li id="slide4">
                                    <img src="images/4.jpg" />
                                    <div class="description">
                                        <input type="checkbox" id="show-description-4"/>
                                        <label for="show-description-4" class="show-description-label">3</label>
                                        <div class="description-text">
                                            <h2>Deluxe Rooms</h2>
                                            <p>We offer rooms at various price ranges for your enjoyment</p>
                                        </div>
                                    </div>
                                </li>
                                <li id="slide5">
                                    <img src="images/5.jpg" />
                                    <div class="description">
                                        <input type="checkbox" id="show-description-5"/>
                                        <label for="show-description-5" class="show-description-label">4</label>
                                        <div class="description-text">
                                            <h2>Luxury Suites</h2>
                                            <p>Our rooms are designed to international standards for premium service</p>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="section-title">
                        <h3><?php echo t('signup_title'); ?></h3>
                        <div class="clearfix"></div>
                    </div>
                    
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="adminEmployeeID">Login ID</label>
                                    <input type="text" class="form-control" id="adminEmployeeID" name="adminEmployeeID" 
                                    placeholder="Please enter Login ID" 
                                    pattern="[A-Za-z0-9]{6,12}" 
                                    title="Login ID must be 6-12 characters (letters or numbers)" 
                                    required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="adminEmail">Email Address</label>
                                    <input type="email" class="form-control" id="adminEmail" name="adminEmail" placeholder="Please enter email" pattern="[a-z0-9._%+-]+@MIS\.com$" title="Invalid email format" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="adminPassword">Password</label>
                                    <input type="password" class="form-control" id="adminPassword" name="adminPassword" placeholder="Please enter password" required oninput="checkPasswordSecurity(this)">
                                    <div id="passwordStrengthBar" style="height: 8px;"></div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="adminPasswordConfirm">Confirm Password</label>
                                    <input type="password" class="form-control" id="adminPasswordConfirm" name="adminPasswordConfirm" placeholder="Please re-enter password" required oninput="checkPasswordMatch(this)">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" class="btn tf-btn btn-success">Create Account</button>
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

    <script src="js/jquery.1.11.1.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script type="text/javascript" src="js/SmoothScroll.js"></script>
    <script type="text/javascript" src="js/jquery.isotope.js"></script>
    <script src="js/owl.carousel.js"></script>
    <script type="text/javascript" src="js/main.js"></script>

    <script>
        function checkPasswordSecurity(input) {
            var password = input.value;
            var strengthBar = document.getElementById('passwordStrengthBar');
            
            if (password.length < 8) {
                input.setCustomValidity("Password must be at least 8 characters.");
                strengthBar.style.width = '10%';
                strengthBar.style.backgroundColor = 'red';
            } 
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
            if (password !== confirmPassword) {
                input.setCustomValidity("Passwords do not match.");
            } else {
                input.setCustomValidity("");
            }
        }
    </script>
  </body>
</html>