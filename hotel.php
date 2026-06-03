<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/language.php';
?><!DOCTYPE html>
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
				<div class="categories">
                <h3>HotelMIS Brian Chan Portfolio</h3>
                <ul class="cat">
                    <li class="pull-left">
                    <li class="pull-left">
                        <ol class="type">
                            <li><a href="#" data-filter="*" class="active">All</a></li>
                            <li><a href="#" data-filter=".web">Superior</a></li>
                            <li><a href="#" data-filter=".photography">VIP</a></li>
                            <li><a href="#" data-filter=".app" >Deluxe</a></li>
                            <li><a href="#" data-filter=".branding" >Mwanainchi</a></li>
                        </ol>
                    </li>
                </ul>
                <div class="clearfix"></div>
            </div>

            <div id="lightbox" class="row">

                <div class="col-sm-6 col-md-3 col-lg-3 branding">
                    <div class="portfolio-item">
                        <div class="hover-bg">
                            <a href="#" Onclick="alert('The Room is now Reserved!.Welcome to Hillview Guest! An elegant place of serenity and tranquility.For Assistance to us on 07222222/ 07111111');">
                                <div class="hover-text">
                                    <small>Air Conditioning</small>
									
									<small> <strong>Ksh:10000 per night</strong></small>
                                    <div class="clearfix"></div>
                                    <i class="fa fa-plus"></i>
                                </div>
                                <img src="img/portfolio/1.jpg" class="img-responsive" alt="...">
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3 col-lg-3 photography app">
                    <div class="portfolio-item">
                        <div class="hover-bg">
                            <a href="#" Onclick="alert('The Room is now Reserved!.Welcome to Hillview Guest! An elegant place of serenity and tranquility.For Assistance to us on 07222222/ 07111111');">
                                <div class="hover-text">
                                    <small>Air Conditioning, LCD Screen</small>
									
									<small> <strong>Ksh:40000 per night</strong></small>
                                    <div class="clearfix"></div>
                                    <i class="fa fa-plus"></i>
                                </div>
                                <img src="img/portfolio/2.jpg" class="img-responsive" alt="...">
                            </a>
                        </div>
                    </div>
                </div>
				
				<div class="col-sm-6 col-md-3 col-lg-3 photography">
                    <div class="portfolio-item">
                        <div class="hover-bg">
                            <a href="#" Onclick="alert('The Room is now Reserved!.Welcome to Hillview Guest! An elegant place of serenity and tranquility.For Assistance to us on 07222222/ 07111111');">
                                <div class="hover-text">
                                    <small>Air Conditioning, LCD Screen</small>
									
									<small> <strong>Ksh:40000 per night</strong></small>
                                    <div class="clearfix"></div>
                                    <i class="fa fa-plus"></i>
                                </div>
                                <img src="img/portfolio/3.jpg" class="img-responsive" alt="...">
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3 col-lg-3 branding">
                    <div class="portfolio-item">
                        <div class="hover-bg">
                            <a href="#" Onclick="alert('The Room is now Reserved!.Welcome to Hillview Guest! An elegant place of serenity and tranquility.For Assistance to us on 07222222/ 07111111');">
                                <div class="hover-text">
                                    <small>Air Conditioning</small>
									
									<small> <strong>Ksh:10000 per night</strong></small>
                                    <div class="clearfix"></div>
                                    <i class="fa fa-plus"></i>
                                </div>
                                <img src="img/portfolio/4.jpg" class="img-responsive" alt="...">
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3 col-lg-3 branding">
                    <div class="portfolio-item">
                        <div class="hover-bg">
                            <a href="#" Onclick="alert('The Room is now Reserved!.Welcome to Hillview Guest! An elegant place of serenity and tranquility.For Assistance to us on 07222222/ 07111111');">
                                <div class="hover-text">
                                    <small>Air Conditioning</small>
									
									<small> <strong>Ksh:10000 per night</strong></small>
                                    <div class="clearfix"></div>
                                    <i class="fa fa-plus"></i>
                                </div>
                                <img src="img/portfolio/5.jpg" class="img-responsive" alt="...">
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3 col-lg-3 web">
                    <div class="portfolio-item">
                        <div class="hover-bg">
                            <a href="" Onclick="alert('The Room is now Reserved!.Welcome to Hillview Guest! An elegant place of serenity and tranquility.For Assistance to us on 07222222/ 07111111');">
                                <div class="hover-text">
                                    <small>Air Conditioning, 2 Telephone Line, LCD Screen</small>
									
									<small> <strong>Ksh:50000 per night</strong></small>
									
                                    <div class="clearfix"></div>
                                    <i class="fa fa-plus"></i>
                                </div>
                                <img src="img/portfolio/6.jpg" class="img-responsive" alt="...">
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3 col-lg-3 app">
                    <div class="portfolio-item">
                        <div class="hover-bg">
                            <a href="#" Onclick="alert('The Room is now Reserved!.Welcome to Hillview Guest! An elegant place of serenity and tranquility.For Assistance to us on 07222222/ 07111111');">
                                <div class="hover-text">
                                    <small>Air Conditioning, 2 Telephone Line</small>
									
									<small> <strong>Ksh:30000 per night</strong></small>
                                    <div class="clearfix"></div>
                                    <i class="fa fa-plus"></i>
                                </div>
                                <img src="img/portfolio/7.jpg" class="img-responsive" alt="...">
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3 col-lg-3 photography">
                    <div class="portfolio-item">
                        <div class="hover-bg">
                            <a href="#" Onclick="alert('The Room is now Reserved!.Welcome to Hillview Guest! An elegant place of serenity and tranquility.For Assistance to us on 07222222/ 07111111');">
                                <div class="hover-text">
                                    <small>Air Conditioning, LCD Screen</small>
									
									<small> <strong>Ksh:40000 per night</strong></small>
                                    <div class="clearfix"></div>
                                    <i class="fa fa-plus"></i>
                                </div>
                                <img src="img/portfolio/8.jpg" class="img-responsive" alt="...">
                            </a>
                        </div>
                    </div>
                </div>
				
				<div class="col-sm-6 col-md-3 col-lg-3 web">
                    <div class="portfolio-item">
                        <div class="hover-bg">
                            <a href="#" Onclick="alert('The Room is now Reserved!.Welcome to Hillview Guest! An elegant place of serenity and tranquility.For Assistance to us on 07222222/ 07111111');">
                                <div class="hover-text">
                                    <small>Air Conditioning, LCD Screen</small>
									
									<small> <strong>Ksh:40000 per night</strong></small>
                                    <div class="clearfix"></div>
                                    <i class="fa fa-plus"></i>
                                </div>
                                <img src="img/portfolio/9.jpg" class="img-responsive" alt="...">
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3 col-lg-3 web">
                    <div class="portfolio-item">
                        <div class="hover-bg">
                            <a href="#" Onclick="alert('The Room is now Reserved!.Welcome to Hillview Guest! An elegant place of serenity and tranquility.For Assistance to us on 07222222/ 07111111');">
                                <div class="hover-text">
                                     <small>Air Conditioning, 2 Telephone Line, LCD Screen</small>
									
									<small> <strong>Ksh:50000 per night</strong></small>
                                    <div class="clearfix"></div>
                                    <i class="fa fa-plus"></i>
                                </div>
                                <img src="img/portfolio/10.jpg" class="img-responsive" alt="...">
                            </a>
                        </div>
                    </div>
                </div>
                    
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