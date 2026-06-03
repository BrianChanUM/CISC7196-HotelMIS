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
    <?php include(__DIR__ . '/layout/header.php');?>
    <ul class="nav navbar-nav navbar-right" id="navbar"></ul>
    <?php include(__DIR__ . '/layout/language_switcher.php');?>
	<?php include(__DIR__ . '/layout/navbar.php');?>

	
</div><!-- /.navbar-collapse -->
      </div><!-- /.container-fluid -->
    </nav>
    <!-- Home Page
    ==========================================-->
    <div id="tf-home" class="text-center">
	<a href="#tf-contact" ></a>
       
    </div>
	
	<div id="tf-about">
 <div class="container">
        <h2>Weekly Timesheet</h2> </br>
        <form id="timesheetForm" method="post" > <!-- action="save_timesheet.php" -->
            <table class="table" id="timesheetTable"> 
                <thead>
                    <tr>
                        <th>Day</th>
                        <th>Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Hours Worked</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Rows will be added here by JavaScript -->
                </tbody>
            </table>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
		
		

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

	
	        var daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        var tableBody = document.querySelector('#timesheetTable tbody');
        daysOfWeek.forEach(function(day) {
            var row = document.createElement('tr');

            var dayCell = document.createElement('td');
            dayCell.textContent = day;
            row.appendChild(dayCell);

            var dateCell = document.createElement('td');
            var dateInput = document.createElement('input');
            dateInput.type = 'date';
            dateInput.className = 'form-control date';
            dateCell.appendChild(dateInput);
            row.appendChild(dateCell);

            var startTimeCell = document.createElement('td');
            var startTimeInput = document.createElement('input');
            startTimeInput.type = 'time';
            startTimeInput.className = 'form-control start-time';
            startTimeCell.appendChild(startTimeInput);
            row.appendChild(startTimeCell);

            var endTimeCell = document.createElement('td');
            var endTimeInput = document.createElement('input');
            endTimeInput.type = 'time';
            endTimeInput.className = 'form-control end-time';
            endTimeCell.appendChild(endTimeInput);
            row.appendChild(endTimeCell);

            var hoursWorkedCell = document.createElement('td');
            var hoursWorkedInput = document.createElement('input');
            hoursWorkedInput.type = 'number';
            hoursWorkedInput.className = 'form-control hours-worked';
            hoursWorkedInput.readOnly = true;
            hoursWorkedCell.appendChild(hoursWorkedInput);
            row.appendChild(hoursWorkedCell);

            tableBody.appendChild(row);
        });

        document.querySelectorAll('.start-time, .end-time').forEach(function(input) {
            input.addEventListener('input', function() {
                var startTime = input.parentElement.previousElementSibling.querySelector('input').valueAsNumber;
                var endTime = input.parentElement.nextElementSibling.querySelector('input').valueAsNumber;
                if (startTime && endTime) {
                    var hoursWorked = (endTime - startTime) / 1000 / 60 / 60;
                    input.parentElement.nextElementSibling.nextElementSibling.querySelector('input').value = hoursWorked.toFixed(2);
                }
            });
        });

        document.getElementById('timesheetForm').addEventListener('submit', function(event) {
            event.preventDefault();
            // Add your code to save the timesheet data here
            alert('Timesheet data saved successfully!');
        });
		
		document.querySelectorAll('.start-time, .end-time').forEach(function(input) {
    input.addEventListener('input', function() {
        var startTime = input.parentElement.previousElementSibling.querySelector('input').value;
        var endTime = input.parentElement.nextElementSibling.querySelector('input').value;
        if (startTime && endTime) {
            var start = new Date("1970-01-01 " + startTime);
            var end = new Date("1970-01-01 " + endTime);
            if (end < start) {
                // If the end time is earlier than the start time, add 24 hours to the end time
                end.setDate(end.getDate() + 1);
            }
            var diff = end - start;
            var hoursWorked = diff / 1000 / 60 / 60;
            input.parentElement.nextElementSibling.nextElementSibling.querySelector('input').value = hoursWorked.toFixed(2);
        }
    });
});
		
		
		
		
		
    </script>

  </body>
</html>


<?php
// Database connection
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
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO usertimesheet (UID, Date, StartTime, EndTime, HoursWorked) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $uid, $date, $start_time, $end_time, $hours_worked);

    // Set parameters and execute
    $uid = $_POST["uid"];
    $date = $_POST["date"];
    $start_time = $_POST["start_time"];
    $end_time = $_POST["end_time"];
    $hours_worked = $_POST["hours_worked"];
    $stmt->execute();

    echo "New records created successfully";

    $stmt->close();
    $conn->close();
}
?>