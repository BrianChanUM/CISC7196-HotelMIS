<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/language.php';

function checkPermission($module, $permissionType) {
    if (!isset($_SESSION['permissions'])) {
        return false;
    }
    $key = $module . '_' . $permissionType;
    return isset($_SESSION['permissions'][$key]) && $_SESSION['permissions'][$key];
}

$showHotelForm = true;
$hotelAccessMessage = "";

if (!isset($_SESSION['username'])) {
    $showHotelForm = false;
    $hotelAccessMessage = "Please login first.";
} else {
    $userRole = $_SESSION['role'];
    if ($userRole != 'admin') {
        if (!checkPermission('hotel_booking', 'create')) {
            $showHotelForm = false;
            $hotelAccessMessage = "You do not have permission to book hotel rooms.";
        }
    }
}

// Handle form submission first (before any HTML output)
date_default_timezone_set('Asia/Shanghai');
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['email']) && !empty($_POST['booking_token']) && $_POST['booking_token'] === $_SESSION['booking_token']) {
    $_SESSION['booking_token'] = md5(uniqid(rand(), true));
    
    $servername = "localhost";
    $username = "root";
    $password = "123456";
    $dbname = "hmis";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $checkInDate = $_POST['checkInDate'];
    $checkOutDate = $_POST['checkOutDate'];
    $hotelType = $_POST['hotel'];
    $time = $checkInDate . ' 14:00:00';
    $ordertype = "Hotel";
    $email = $_POST['email'];
    $phone = preg_replace('/\D/', '', $_POST['phone']);
    $orderremark = $hotelType . ' | ' . $_POST['comment'] . ' | Check-out: ' . $checkOutDate;
    $status = "TBC";
    $ordercreateddate = date('Y-m-d H:i:s');
    $ordermodifieddate = date('Y-m-d H:i:s');
    $noofguest = $_POST['guests'];
    $isRequired = 0;
    $assignedTo = '';

    $checkStock = $conn->prepare("SELECT daily_quantity FROM hotelroomtype WHERE HotelRoomtype = ?");
    $checkStock->bind_param("s", $hotelType);
    $checkStock->execute();
    $stockResult = $checkStock->get_result();
    
    if ($stockResult->num_rows > 0) {
        $stockRow = $stockResult->fetch_assoc();
        $availableRooms = $stockRow['daily_quantity'];
        
        if ($availableRooms <= 0) {
            $_SESSION['booking_error'] = "Sorry, no rooms available for " . $hotelType;
            $checkStock->close();
            $conn->close();
            header("Location: bookedhotel.php?error=1");
            exit();
        }
        
        $deductStock = $conn->prepare("UPDATE hotelroomtype SET daily_quantity = daily_quantity - 1 WHERE HotelRoomtype = ? AND daily_quantity > 0");
        $deductStock->bind_param("s", $hotelType);
        $deductStock->execute();
        $deductStock->close();
    }
    $checkStock->close();

    $stmt = $conn->prepare("INSERT INTO orderbookings (OrderType, Time, ContactNo, Email, OrderRemark, Status, OrderCreatedDate, OrderModifiedDate, NoofGuest, isRequired, AssignedTo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssiis", $ordertype, $time, $phone, $email, $orderremark, $status, $ordercreateddate, $ordermodifieddate, $noofguest, $isRequired, $assignedTo);

    if ($stmt->execute()) {
        $last_id = $conn->insert_id;
        $_SESSION['booking_success'] = "Your Hotel order ID is: Hotel " . $last_id;
        $stmt->close();
        $conn->close();
        header("Location: bookedhotel.php?success=1");
        exit();
    } else {
        $stmt->close();
        $conn->close();
    }
}

// Generate a unique token for form submission
if (!isset($_SESSION['booking_token'])) {
    $_SESSION['booking_token'] = md5(uniqid(rand(), true));
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
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome/css/font-awesome.css">

    <!-- Slider
    ================================================== -->
    <link href="css/owl.carousel.css" rel="stylesheet" media="screen">
    <link href="css/owl.theme.css" rel="stylesheet" media="screen">
    <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Stylesheet
    ================================================== -->
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/bookedhotel.css">
    <link rel="stylesheet" type="text/css" href="css/responsive.css">

    <link href='http://fonts.googleapis.com/css?family=Lato:100,300,400,700,900,100italic,300italic,400italic,700italic,900italic' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,700,300,600,800,400' rel='stylesheet' type='text/css'>

    <script type="text/javascript" src="js/modernizr.custom.js"></script>
    <style>
        .price-display {
            font-size: 2em;
            /* Adjust as needed */
            color: #008CBA;
            /* Adjust as needed */
        }
    </style>
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

            $hotelImages = glob(__DIR__ . '/img/hotel/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
            $hotelImagePaths = [];
            foreach ($hotelImages as $img) {
                $hotelImagePaths[] = 'img/hotel/' . basename($img);
            }
            $hotelImagePathsJSON = json_encode($hotelImagePaths);
            ?>

            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <?php include(__DIR__ . '/layout/header.php'); ?>
                <ul class="nav navbar-nav navbar-right" id="navbar"></ul>
                <?php include(__DIR__ . '/layout/language_switcher.php'); ?>
                <?php include(__DIR__ . '/layout/navbar.php'); ?>


            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
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
                                    <img src="" class="hotel-slideshow-img" data-index="0" />
                                    <div class="description">
                                        <input type="checkbox" id="show-description-1" />
                                        <label for="show-description-1" class="show-description-label">I</label>
                                        <div class="description-text">
                                            <h3>Rolls-Royce Motor Cars</h3>
                                            <p>Rolls-Royce Motor Cars Limited is a British luxury automobile maker which has operated as a wholly owned subsidiary of BMW AG since 2003</p>
                                        </div>
                                    </div>
                                </li>
                                <li id="slide2">
                                    <img src="" class="hotel-slideshow-img" data-index="1" />
                                    <div class="description">
                                        <input type="checkbox" id="show-description-2" />
                                        <label for="show-description-2" class="show-description-label">1</label>
                                        <div class="description-text">
                                            <h3>Bentley</h3>
                                            <p>Bentley Motors Limited is a British designer, manufacturer and marketer of luxury cars and SUVs. Headquartered in Crewe, England,</p>
                                        </div>
                                    </div>
                                </li>
                                <li id="slide3">
                                    <img src="" class="hotel-slideshow-img" data-index="2" />
                                    <div class="description">
                                        <input type="checkbox" id="show-description-3" />
                                        <label for="show-description-3" class="show-description-label">2</label>
                                        <div class="description-text">
                                            <h3>Toyota and Lexus</h3>
                                            <p>Toyota owns the Lexus brand, but Toyota has its headquarters in Toyota City, Japan, while Lexus operations are headquartered in Nagoya</p>
                                        </div>
                                    </div>
                                </li>
                                <li id="slide4">
                                    <img src="" class="hotel-slideshow-img" data-index="3" />
                                    <div class="description">
                                        <input type="checkbox" id="show-description-4" />
                                        <label for="show-description-4" class="show-description-label">3</label>
                                        <div class="description-text">
                                            <h2>Ferrari</h2>
                                            <p>Ferrari - All the official contents of the Maranello based carmaker: all the cars in the range and the great historic cars</p>
                                        </div>
                                    </div>
                                </li>
                                <li id="slide5">
                                    <img src="" class="hotel-slideshow-img" data-index="4" />
                                    <div class="description">
                                        <input type="checkbox" id="show-description-5" />
                                        <label for="show-description-5" class="show-description-label">4</label>
                                        <div class="description-text">
                                            <h3>Aston Martin</h3>
                                            <p>The luxury British sports car manufacturer.</p><a href="#" class="btn tf-btn btn-success">Order</a>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="section-title">
                        <h3>Kindly input the following details for our hotel booking service.</h3>
                        <div class="clearfix"></div>
                    </div>
                    <?php if (isset($_SESSION['booking_success']) && isset($_GET['success'])): ?>
                    <div class="alert alert-success">
                        <?php echo $_SESSION['booking_success']; unset($_SESSION['booking_success']); ?>
                    </div>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['booking_error']) && isset($_GET['error'])): ?>
                    <div class="alert alert-danger">
                        <?php echo $_SESSION['booking_error']; unset($_SESSION['booking_error']); ?>
                    </div>
                    <?php endif; ?>
                    <form action="bookedhotel.php" method="post">
                        <input type="hidden" name="booking_token" value="<?php echo $_SESSION['booking_token']; ?>">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="email">Username/Email address</label>
                                    <input type="text" class="form-control" id="email" name="email" placeholder="Username/Email" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="phone">Contact number</label>
                                    <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone Number" required>
                                </div>
                            </div>

                            <?php
                            $servername = "localhost";
                            $username = "root";
                            $password = "123456";
                            $dbname = "hmis";

                            // Create connection
                            $conn = new mysqli($servername, $username, $password, $dbname);

                            // Check connection
                            if ($conn->connect_error) {
                                die("Connection failed: " . $conn->connect_error);
                            }

                            $sql = "SELECT HotelRoomtype, daily_quantity FROM hotelroomtype WHERE daily_quantity > 0";
                            $result = $conn->query($sql);
                            ?>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="hotel">Hotel</label>
                                    <select class="form-control" id="hotel" name="hotel" onchange="updateHotelInfo()">
                                        <option value="">Select a Room Type</option>
                                        <?php
                                        if ($result->num_rows > 0) {
                                            // output data of each row
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<option value=\"" . $row["HotelRoomtype"] . "\" data-quantity=\"" . $row["daily_quantity"] . "\">" . $row["HotelRoomtype"] . " (Available: " . $row["daily_quantity"] . ")</option>";
                                            }
                                        } else {
                                            echo "0 results";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div id="hotelInfo" class="help-block" style="color: #666; font-size: 14px;"></div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="checkInDate">Check-in Date</label>
                                    <input type="date" class="form-control" id="checkInDate" name="checkInDate">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="checkOutDate">Check-out Date</label>
                                    <input type="date" class="form-control" id="checkOutDate" name="checkOutDate">
                                </div>
                            </div>
                            <!-- Price -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="price">Price / Night</label>
                                    <p id="price" class="price-display">$0.00</p>



                                </div>
                            </div>



                            <!-- Number of guests -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="guests">Number of Guests</label>
                                    <input type="number" class="form-control" id="guests" name="guests" min="1" max="10">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="comment">Comment</label>
                                    <input type="text" class="form-control" id="comment" name="comment" placeholder="Leave your request here">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn tf-btn btn-success">Submit Booking</button>
                        <button type="button" class="btn tf-btn btn-warning" id="addToCartBtn">Add to Cart</button>
                    </form>

                    <div id="myModal" class="modal">
                        <!-- Modal content -->
                        <div class="modal-content">
                            <img src="img/hotel/logo.png" alt="Logo" style="width:100px; height:auto;"> <!-- Add this line -->
                            <span class="close">&times;</span>
                            <p id="modalText">Your reservation is confirmed.</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <?php include(__DIR__ . '/layout/footer.php'); ?>


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
    <script src="js/cart.js"></script>
    <script>
        const today = new Date().toISOString().split('T')[0];
        const checkInDateInput = document.getElementById('checkInDate');
        const checkOutDateInput = document.getElementById('checkOutDate');

        // Set minimum date for check-in
        checkInDateInput.setAttribute('min', today);


        // Set minimum date for check-out (one day after check-in)
        checkInDateInput.addEventListener('change', () => {
            const checkInDate = new Date(checkInDateInput.value);
            const nextDay = new Date(checkInDate);
            nextDay.setDate(checkInDate.getDate() + 1);
            const formattedNextDay = nextDay.toISOString().split('T')[0];
            checkOutDateInput.setAttribute('min', formattedNextDay);
        });

        // Get the modal
        var modal = document.getElementById("myModal");

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];


        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>

    <script>
        const hotelSelect = document.getElementById('hotel');
        const priceDisplay = document.getElementById('price');
        const hotelInfo = document.getElementById('hotelInfo');

        function updateHotelInfo() {
            const selectedOption = hotelSelect.options[hotelSelect.selectedIndex];
            const quantity = selectedOption.getAttribute('data-quantity');
            if (quantity) {
                hotelInfo.textContent = 'Available Rooms: ' + quantity;
            } else {
                hotelInfo.textContent = '';
            }
        }

        hotelSelect.addEventListener('change', () => {
            const selectedHotelRoomType = hotelSelect.value;
            // Make an AJAX request to get the price
            fetch('function/get_price.php?roomType=' + selectedHotelRoomType)
                .then(response => response.json())
                .then(data => {
                    if (data.price !== 'N/A') {
                        priceDisplay.textContent = '$' + data.price;
                    } else {
                        priceDisplay.textContent = 'Price not available';
                    }
                })
                .catch(error => {
                    console.error('Error fetching price:', error);
                });

            updateSlideshowImages();
            updateHotelInfo();
        });

        const hotelImagePaths = <?php echo $hotelImagePathsJSON; ?>;

        function getRandomImages(count) {
            if (hotelImagePaths.length === 0) return [];
            const shuffled = [...hotelImagePaths].sort(() => 0.5 - Math.random());
            return shuffled.slice(0, count);
        }

        function updateSlideshowImages() {
            const images = document.querySelectorAll('.hotel-slideshow-img');
            const randomImages = getRandomImages(images.length);

            images.forEach((img, index) => {
                if (randomImages[index]) {
                    img.src = randomImages[index];
                }
            });
        }

        updateSlideshowImages();

        $('#addToCartBtn').click(function() {
            var hotel = $('#hotel').val();
            var checkInDate = $('#checkInDate').val();
            var checkOutDate = $('#checkOutDate').val();
            var guests = $('#guests').val() || 1;
            var comment = $('#comment').val() || '';
            var priceText = $('#price').text().replace('$', '');
            var price = parseFloat(priceText) || 0;
            
            if (!hotel) {
                alert('Please select a hotel room type');
                return;
            }
            
            if (!checkInDate) {
                alert('Please select a check-in date');
                return;
            }
            
            if (!checkOutDate) {
                alert('Please select a check-out date');
                return;
            }
            
            var itemType = 'Hotel';
            var itemName = hotel;
            var itemPrice = price;
            var itemDetails = 'Check-in: ' + checkInDate + ', Check-out: ' + checkOutDate + ', Guests: ' + guests + (comment ? ', Notes: ' + comment : '');
            
            addToCart(itemType, itemName, itemPrice, checkInDate, '14:00', guests, itemDetails);
        });
    </script>


</body>

</html>