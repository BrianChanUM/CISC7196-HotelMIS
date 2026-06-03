<?php
require_once __DIR__ . '/config/db_config.php';
require_once __DIR__ . '/config/language.php';

$conn = getDBConnection();

$etypes = [];
$etype_query = "SELECT etype FROM enquirytype WHERE estatus = 1";
$etype_result = executeQuery($etype_query);

if ($etype_result && $etype_result->num_rows > 0) {
    while($row = $etype_result->fetch_assoc()) {
        $etypes[] = $row["etype"];
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $eUser = $_POST['eUser'];
    $eEmail = $_POST['eEmail'];
    $ePhone = $_POST['ePhone'];
    $eType = $_POST['eType'];
    $eContent = $_POST['eContent'];
    $eIsCall = ($_POST['eIsCall'] == 'yes') ? 1 : 0;
    $eCreatedDate = date("Y-m-d H:i:s");

    $sql = "INSERT INTO enquiry (eUser, eEmail, ePhone, eType, eContent, eIsCall, eCreatedDate) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssis", $eUser, $eEmail, $ePhone, $eType, $eContent, $eIsCall, $eCreatedDate);
    
    if ($stmt->execute() === TRUE) {
        echo "<script type='text/javascript'>alert('" . t('thank_you_contact') . "');</script>";
    } else {
        echo "错误: " . "<br>" . $conn->error;
    }
    $stmt->close();
    closeDBConnection($conn);
    $conn = null; // Set to null to avoid double close
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Only close connection if it's still open (not closed in POST handling)
if ($conn) {
    closeDBConnection($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=9">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo t('hotel_management_system'); ?> - <?php echo t('contact'); ?></title>
    
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="img/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="img/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="img/apple-touch-icon-114x114.png">

    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome/css/font-awesome.css">
    <link href="css/owl.carousel.css" rel="stylesheet" media="screen">
    <link href="css/owl.theme.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" type="text/css" href="css/style.css">
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
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php"><?php echo t('hotel_management_system'); ?></a>
        </div>
        
        <style>
            .paging {
                background-color: grey;
                color: black;
            }
        </style>
        
        <style>
            .radio-buttons {
                display: flex;
            }
            .radio-buttons label {
                margin-right: 15px;
            }
        </style>
        
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

    <div id="tf-contact" class="hf-margin-top">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="section-title">
                        <h2><?php echo t('contact_us'); ?></h2>
                        <div class="clearfix"></div>
                        <p><?php echo t('welcome_contact'); ?></p>
                    </div>
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="eUser"><?php echo t('contact_name'); ?></label>
                                    <input type="text" class="form-control" id="eUser" name="eUser" placeholder="Enter your name" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="eEmail"><?php echo t('contact_email'); ?></label>
                                    <input type="email" class="form-control" id="eEmail" name="eEmail" placeholder="Enter your email" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="ePhone"><?php echo t('contact_phone'); ?></label>
                                    <input type="text" class="form-control" id="ePhone" name="ePhone" placeholder="Enter your phone number" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="eType"><?php echo t('enquiry_type'); ?></label>
                                    <select class="form-control" id="eType" name="eType">
                                        <?php if (!empty($etypes)): ?>
                                            <?php foreach ($etypes as $type): ?>
                                                <option value="<?php echo htmlspecialchars($type); ?>"><?php echo htmlspecialchars($type); ?></option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="General Enquiry">General Enquiry</option>
                                            <option value="Room Booking">Room Booking</option>
                                            <option value="Dining">Dining</option>
                                            <option value="Limo Service">Limo Service</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="eContent"><?php echo t('message'); ?></label>
                                    <textarea class="form-control" id="eContent" name="eContent" rows="5" placeholder="Enter your message" required></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Request Callback:</label>
                                    <div class="radio-buttons">
                                        <label><input type="radio" name="eIsCall" value="yes"> Yes</label>
                                        <label><input type="radio" name="eIsCall" value="no" checked> No</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary"><?php echo t('submit'); ?></button>
                            <button type="reset" class="btn btn-default"><?php echo t('reset'); ?></button>
                        </div>
                    </form>
                </div>
                <div class="col-md-6">
                    <div class="section-title">
                        <h2>Contact Us</h2>
                        <div class="clearfix"></div>
                    </div>
                    <p><?php echo t('quality_service'); ?></p>
                    <p><strong><?php echo t('address'); ?>:</strong> 123 Hotel Road, City, Country</p>
                    <p><strong><?php echo t('phone'); ?>:</strong> +1 234 567 8900</p>
                    <p><strong><?php echo t('contact_email'); ?>:</strong> info@hotelmis.com</p>
                    <p><strong><?php echo t('business_hours'); ?>:</strong> <?php echo t('24_7_service'); ?></p>
                </div>
            </div>
        </div>
    </div>

    <nav id="footer">
        <div class="container">
            <div class="pull-left fnav">
                <p>Copyright &copy; 2023 | Designed by <a href="#">CISC7196-HotelMIS-October 2023</a></p>
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

    <script src="js/jquery.1.11.1.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script type="text/javascript" src="js/owl.carousel.js"></script>
    <script type="text/javascript" src="js/SmoothScroll.js"></script>
    <script type="text/javascript" src="js/main.js"></script>
  </body>
</html>
