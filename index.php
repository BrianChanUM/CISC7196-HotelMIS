<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/db_config.php';
require_once __DIR__ . '/config/language.php';

$hotBookings = [];
$sql = "SELECT OrderType, COUNT(*) as Total FROM orderbookings GROUP BY OrderType ORDER BY Total DESC LIMIT 10";
$result = executeQuery($sql);

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $hotBookings[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=9">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo t('hotel_management_system'); ?> - <?php echo t('home'); ?></title>
    <meta name="description" content="<?php echo t('hotel_management_system'); ?>">
    <meta name="keywords" content="<?php echo t('hotel_management_system'); ?>">
    <meta name="author" content="<?php echo t('hotel_management_system'); ?>">

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
                    <span class="sr-only"><?php echo t('toggle_navigation'); ?></span>
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
                .service-box {
                    background: #fff;
                    padding: 30px;
                    border-radius: 8px;
                    text-align: center;
                    margin-bottom: 20px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                    transition: transform 0.3s ease;
                    position: relative;
                }
                .service-box:hover {
                    transform: translateY(-5px);
                }
                .service-icon {
                    margin-bottom: 15px;
                    color: #333;
                }
                .rank-badge {
                    position: absolute;
                    top: 10px;
                    right: 10px;
                    background: #333;
                    color: #fff;
                    padding: 5px 10px;
                    border-radius: 20px;
                    font-size: 12px;
                    font-weight: bold;
                }
                .bg-grey {
                    background-color: #f5f5f5;
                }
                .section {
                    padding: 60px 0;
                }
            </style>
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
        <a href="#tf-about"></a>
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
                                        <input type="checkbox" id="show-description-1"/>
                                        <label for="show-description-1" class="show-description-label">I</label>
                                        <div class="description-text">
                                            <h2>优雅一瞥</h2>
                                            <p>我们提供最舒适的宁静与安心</p>
                                        </div>
                                    </div>
                                </li>
                                <li id="slide2">
                                    <img src="images/2.jpg" />
                                    <div class="description">
                                        <input type="checkbox" id="show-description-2"/>
                                        <label for="show-description-2" class="show-description-label">1</label>
                                        <div class="description-text">
                                            <h2>豪华客房</h2>
                                            <p>这些房间专为您的舒适体验而设计</p>
                                        </div>
                                    </div>
                                </li>
                                <li id="slide3">
                                    <img src="images/3.jpg" />
                                    <div class="description">
                                        <input type="checkbox" id="show-description-3"/>
                                        <label for="show-description-3" class="show-description-label">2</label>
                                        <div class="description-text">
                                            <h2>贵宾房</h2>
                                            <p>这些房间体现您的品味与身份</p>
                                        </div>
                                    </div>
                                </li>
                                <li id="slide4">
                                    <img src="images/4.jpg" />
                                    <div class="description">
                                        <input type="checkbox" id="show-description-4"/>
                                        <label for="show-description-4" class="show-description-label">3</label>
                                        <div class="description-text">
                                            <h2>精品客房</h2>
                                            <p>我们提供各种价位的房间让您享受美好时光</p>
                                        </div>
                                    </div>
                                </li>
                                <li id="slide5">
                                    <img src="images/5.jpg" />
                                    <div class="description">
                                        <input type="checkbox" id="show-description-5"/>
                                        <label for="show-description-5" class="show-description-label">4</label>
                                        <div class="description-text">
                                            <h2>豪华套房</h2>
                                            <p>我们按国际标准设计房间，为客户提供优质服务</p>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="about-text">
                        <div class="section-title">
                            <h3><?php echo t('about_us'); ?></h3>
                            <div class="clearfix"></div>
                        </div>
                        <p class="intro"><?php echo t('welcome_message'); ?></p>
                        <ul class="about-list">
                            <li>
                                <span class="fa fa-dot-circle-o"></span>
                                <strong><?php echo t('quality_service'); ?></strong>
                            </li>
                            <li>
                                <span class="fa fa-dot-circle-o"></span>
                                <strong><?php echo t('luxury_rooms'); ?></strong>
                            </li>
                            <li>
                                <span class="fa fa-dot-circle-o"></span>
                                <strong><?php echo t('fine_dining'); ?></strong>
                            </li>
                            <li>
                                <span class="fa fa-dot-circle-o"></span>
                                <strong><?php echo t('limo_service'); ?></strong>
                            </li>
                            <li>
                                <span class="fa fa-dot-circle-o"></span>
                                <strong><?php echo t('24_7_service_text'); ?></strong>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="tf-services" class="text-center">
        <div class="container">
            <div class="section-title">
                <h3><?php echo t('our_services'); ?></h3>
                <div class="clearfix"></div>
            </div>
            <div class="row">
                <div class="col-md-3 col-sm-6 service-box">
                    <div class="service-icon"><span class="fa fa-hotel"></span></div>
                    <h4><?php echo t('rooms_and_suites'); ?></h4>
                    <p><?php echo t('comfortable_luxury_rooms'); ?></p>
                </div>
                <div class="col-md-3 col-sm-6 service-box">
                    <div class="service-icon"><span class="fa fa-cutlery"></span></div>
                    <h4><?php echo t('dining_services'); ?></h4>
                    <p><?php echo t('variety_of_food'); ?></p>
                </div>
                <div class="col-md-3 col-sm-6 service-box">
                    <div class="service-icon"><span class="fa fa-car"></span></div>
                    <h4><?php echo t('limousine'); ?></h4>
                    <p><?php echo t('professional_driver'); ?></p>
                </div>
                <div class="col-md-3 col-sm-6 service-box">
                    <div class="service-icon"><span class="fa fa-glass"></span></div>
                    <h4><?php echo t('room_service'); ?></h4>
                    <p><?php echo t('24hour_service'); ?></p>
                </div>
            </div>
        </div>
    </div>

    <div id="tf-hot-bookings" class="bg-grey section">
        <div class="container">
            <div class="section-title text-center">
                <h3><i class="fa fa-trending-up" style="color: #ff6b6b;"></i> Hot Bookings</h3>
                <p class="lead">Most Popular Services This Week</p>
                <div class="clearfix"></div>
            </div>
            <div class="row">
                <?php if (!empty($hotBookings)): ?>
                    <?php $rank = 1; foreach ($hotBookings as $booking): ?>
                        <div class="col-md-3 col-sm-6">
                            <div class="service-box" style="background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);">
                                <span class="rank-badge" style="background: <?php echo $rank == 1 ? '#ff6b6b' : ($rank == 2 ? '#feca57' : ($rank == 3 ? '#48dbfb' : '#95a5a6')); ?>;">
                                    #<?php echo $rank; ?>
                                </span>
                                <div class="service-icon" style="font-size: 40px;">
                                    <?php 
                                    switch($booking['OrderType']) {
                                        case 'Hotel': echo '<span class="fa fa-hotel" style="color: #3498db;"></span>'; break;
                                        case 'F&B': 
                                        case 'Dining': echo '<span class="fa fa-cutlery" style="color: #e74c3c;"></span>'; break;
                                        case 'Limo': echo '<span class="fa fa-car" style="color: #2ecc71;"></span>'; break;
                                        case 'IRD': echo '<span class="fa fa-glass" style="color: #9b59b6;"></span>'; break;
                                        default: echo '<span class="fa fa-calendar" style="color: #333;"></span>';
                                    }
                                    ?>
                                </div>
                                <h4><?php echo $booking['OrderType']; ?></h4>
                                <div class="progress" style="height: 8px; margin-bottom: 10px;">
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: <?php echo min(100, $booking['Total'] * 10); ?>%; 
                                                background: <?php echo $rank == 1 ? '#ff6b6b' : ($rank == 2 ? '#feca57' : ($rank == 3 ? '#48dbfb' : '#3498db')); ?>;">
                                    </div>
                                </div>
                                <p style="font-size: 18px; font-weight: bold; color: #333;">
                                    <?php echo $booking['Total']; ?> Bookings
                                </p>
                            </div>
                        </div>
                        <?php $rank++; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-md-12 text-center">
                        <p>No booking data available yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include(__DIR__ . '/layout/footer.php');?>

    <script src="js/jquery.1.11.1.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script type="text/javascript" src="js/owl.carousel.js"></script>
    <script type="text/javascript" src="js/SmoothScroll.js"></script>
    <script type="text/javascript" src="js/jquery.isotope.js"></script>
    <script type="text/javascript" src="js/main.js"></script>
</body>
</html>
