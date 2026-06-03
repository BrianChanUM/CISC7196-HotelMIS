<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/language.php';
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
          <a class="navbar-brand" href="index.php"><?php echo t('hotel_management_system'); ?></a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <?php
            $user = json_encode($_SESSION);
        ?>

        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <?php include(__DIR__ . '/layout/header.php'); ?>
            <ul class="nav navbar-nav navbar-right" id="navbar"></ul>
            <?php include(__DIR__ . '/layout/navbar.php'); ?>
            <?php include(__DIR__ . '/layout/language_switcher.php'); ?>
        </div>
    </div>
</nav>

    <!-- Home Page
    ==========================================-->
    <div id="tf-home" class="text-center">
	<a href="#tf-team"></a>
       
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
                <div class="col-md-6">
                    <div class="about-text">
                        <div class="section-title">
                           
                            <h3>SELECT YOUR EVENT TYPE</h3>
                          
                            <div class="clearfix"></div>
                        </div>
                        <p class="intro">We believe in the power of co-creation. The in-house EVENT STUDIO is a team of creative planners goes beyond conventional event planning by assisting with venue selection, conceptualizing themes, décor, entertainment, visual and lighting effects as well as marketing coordination to create events that are bespoke and tailor-made for the needs of delegates. This team provides a one-stop shop of experienced planners who are familiar with all of the venues at the Galaxy Macau Integrated Resort.

With the assistance of EVENT STUDIO, delegates will be able to enjoy new experiences each day, and meeting organizers will not have to worry about the logistics of arranging transportation as all venues are within convenient walking distance to one another. </p>
                        <ul class="about-list">
                            <li>
                                <span class="fa fa-dot-circle-o"></span>
                                <strong>MEETINGS & CONFERENCES</strong> - <em> Expansive and flexible, GICC offers 10,000 sqm of pillar-less exhibition space that can be divided into multiple halls. Each hall is separated by high quality, soundproof partitions for hosting events of any size – from large-scale tradeshows to intimate board meetings.

</em>.
								
                            </li>
                            <li>
                                <span class="fa fa-dot-circle-o"></span>
                                <strong>EXHIBITIONS</strong> - <em>We have Fully flexible for events of any scale, GICC features 10,000 sqm of pillar-less space for up to 7,000 delegates, setting the stage for a wide range of exhibitions and trade shows.</em>
                            </li>
                            <li>
                                <span class="fa fa-dot-circle-o"></span>
                                <strong>CONCERTS</strong> - <em>Conveniently located within GICC, the 16,000-seat Galaxy Arena is the largest indoor arena in Macau and where spectacular events come to life - from world concert tours to thrilling fight nights, every guest at Galaxy Arena will enjoy 360° unobstructed views and high quality surround sound while high definition broadcasting will bring the event to every corner of the arena.</em>
                            </li>
							
							 <a href="contact.php" class="btn tf-btn btn-success">Read More</a>
                        </ul>
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