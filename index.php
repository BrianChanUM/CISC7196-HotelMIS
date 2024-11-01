<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Basic Page Needs
    ================================================== -->
    <meta charset="utf-8">
    <!--[if IE]><meta http-equiv="x-ua-compatible" content="IE=9" /><![endif]-->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CISC7196-HotelMIS-2023OCT18</title>
    <meta name="description" content="Spirit8 is a Digital agency one page template built based on bootstrap framework. This template is design by Robert Berki and coded by Jenn Pereira. It is simple, mobile responsive, perfect for portfolio and agency websites. Get this for free exclusively at ThemeForces.com">
    <meta name="keywords" content="bootstrap theme, portfolio template, digital agency, onepage, mobile responsive, spirit8, free website, free theme, themeforces themes, themeforces wordpress themes, themeforces bootstrap theme">
    <meta name="author" content="ThemeForces.com">
    
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

        <!-- Collect the nav links, forms, and other content for toggling --><style>.paging{background-color:grey; color:black;}</style>
<?php
    session_start();
    $user = json_encode($_SESSION);
?>

<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
     <?php include(__DIR__ . '/layout/header.php');?>
    <ul class="nav navbar-nav navbar-right" id="navbar"></ul>
	 <?php include(__DIR__ . '/layout/navbar.php');?>
 
	
</div>
<!-- /.navbar-collapse -->
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
                    <label for "button-2"></label>
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
                                        <h2>Elegant Glance</h2>
                                        <p>Experience serenity and comfort at its best.</p>
                                    </div>
                                </div>
                            </li>
                            <li id="slide2">
                                <img src="images/2.jpg" />
                                <div class="description">
                                    <input type="checkbox" id="show-description-2"/>
                                    <label for="show-description-2" class="show-description-label">1</label>
                                    <div class="description-text">
                                        <h2>Superior Room</h2>
                                        <p>Rooms designed to offer high-class comfort.</p>
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
                                        <p>Luxurious rooms that reflect your status.</p>
                                    </div>
                                </div>
                            </li>
                            <li id="slide4">
                                <img src="images/4.jpg" />
                                <div class="description">
                                    <input type="checkbox" id="show-description-4"/>
                                    <label for="show-description-4" class="show-description-label">3</label>
                                    <div class="description-text">
                                        <h2>Budget Rooms</h2>
                                        <p>Comfortable rooms that fit your budget.</p>
                                    </div>
                                </div>
                            </li>
                            <li id="slide5">
                                <img src="images/5.jpg" />
                                <div class="description">
                                    <input type="checkbox" id="show-description-5"/>
                                    <label for="show-description-5" class="show-description-label">4</label>
                                    <div class="description-text">
                                        <h2>Deluxe Rooms</h2>
                                        <p>Rooms designed to meet international standards.</p>
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
                        <h3>Welcome to Brian's HotelMIS</h3>
                        <div class="clearfix"></div>
                    </div>
                    <p class="intro">Renowned for its exceptional standards of efficiency, service, and five-star hospitality, Brian's HotelMIS is a leader in the Macau hospitality industry. Voted best in the region for hospitality by HMIS in October 2023, we invite you to experience unmatched luxury and comfort.</p>
                    <ul class="about-list">
                        <li>
                            <span class="fa fa-dot-circle-o"></span>
                            <strong>Cotai</strong> - <em>Forbes Travel Guide Five-Star Cotai presents a feast for the senses, featuring stunning art pieces, beautiful flowers, opulent accommodations, and diverse culinary experiences.</em>
                        </li>
                        <li>
                            <span class="fa fa-dot-circle-o"></span>
                            <strong>Macau</strong> - <em>Welcome to Macau – the only resort globally with eight Forbes Travel Guide Five-Star awards. Experience exceptional service, Michelin-starred dining, lavish shopping, and serene spas in the heart of Macau.</em>
                        </li>
                        <li>
                            <span class="fa fa-dot-circle-o"></span>
                            <strong>Taipa</strong> - <em>Committed to providing outstanding hospitality management and satisfying our clients' needs.</em>
                        </li>
                        <li>
                            <span class="fa fa-dot-circle-o"></span>
                            <strong>Contact</strong> - <em>Connect with our dedicated customer care: +853 66666666, +852 88888888</em>
                        </li>
                    </ul>
                    <form action="bookedhotel.php" method="post">
                        <button type="submit" class="btn tf-btn btn-success">Make Reservation now</button>
                    </form>
                </div>
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


