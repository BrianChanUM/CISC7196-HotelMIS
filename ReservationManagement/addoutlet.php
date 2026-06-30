<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/session_check.php';
require_once __DIR__ . '/config/language.php';
require_once __DIR__ . '/function/check_permission.php';
require_once __DIR__ . '/config/db_config.php';

requirePermission('admin_outlets', 'create', 'index.php');

$errorMessage = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $outletName = trim($_POST['OutletName']);
    $outletStyle = $_POST['OutletStyle'];
    $capacity = isset($_POST['capacity']) ? intval($_POST['capacity']) : 50;
    $status = isset($_POST['status']) ? 1 : 0;

    if (empty($outletName)) {
        $errorMessage = "Outlet name is required.";
    } else if ($outletStyle !== 'FnB' && $outletStyle !== 'IRD') {
        $errorMessage = "Invalid outlet style. Please select either 'FnB' or 'IRD'.";
    } else {
        $conn = getDBConnection();
        
        $existingOutletQuery = $conn->prepare("SELECT OutletName FROM hoteloutlet WHERE OutletName = ?");
        $existingOutletQuery->execute([$outletName]);
        
        if ($existingOutletQuery->rowCount() > 0) {
            $errorMessage = "Outlet name '" . htmlspecialchars($outletName) . "' already exists. Please choose a different name.";
        } else {
            $insertQuery = $conn->prepare("INSERT INTO hoteloutlet (OutletName, Style, capacity, status) VALUES (?, ?, ?, ?)");
            $insertQuery->execute([$outletName, $outletStyle, $capacity, $status]);
            
            if ($insertQuery->rowCount() > 0) {
                $successMessage = "Outlet " . htmlspecialchars($outletName) . " has been created successfully!";
            } else {
                $errorMessage = "Error creating outlet.";
            }
        }
        
        closeDBConnection($conn);
    }
}
?><!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CISC7196-HotelMIS-2023OCT18</title>

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

            <?php
            $user = json_encode($_SESSION);
            ?>

            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <?php include(__DIR__ . '/layout/header.php'); ?>
                <ul class="nav navbar-nav navbar-right" id="navbar"></ul>
                <?php include(__DIR__ . '/layout/language_switcher.php'); ?>
                <?php include(__DIR__ . '/layout/navbar.php'); ?>

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
                    <div class="section-title">
                        <h3>To Creat New Outlet Page</h3>
                        <div class="clearfix"></div>
                    </div>

                    <?php if ($errorMessage): ?>
                    <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
                    <?php endif; ?>
                    <?php if ($successMessage): ?>
                    <div class="alert alert-success"><?php echo $successMessage; ?></div>
                    <?php endif; ?>

                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="OutletName">Outlet Name:</label>
                                    <input type="text" class="form-control" id="OutletName" name="OutletName" placeholder="OutletName" required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="outlet">Outlet Style:</label>
                                    <select class="form-control" id="OutletStyle" Name="OutletStyle">
                                        <option value="">Select a Room Type</option>
                                        <option value="FnB">FnB (Food and Beverage)</option>
                                        <option value="IRD">IRD (In-Room Dining)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="capacity">Seating Capacity:</label>
                                    <input type="number" class="form-control" id="capacity" name="capacity" placeholder="Number of seats" value="50" min="1" required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" name="status" value="1" checked> Enable this outlet
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" class="btn tf-btn btn-primary">Create Outlet</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php include(__DIR__ . '/layout/footer.php'); ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery.1.11.1.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script type="text/javascript" src="js/SmoothScroll.js"></script>
    <script type="text/javascript" src="js/jquery.isotope.js"></script>

    <script src="js/owl.carousel.js"></script>

    <script type="text/javascript" src="js/main.js"></script>

</body>

</html>