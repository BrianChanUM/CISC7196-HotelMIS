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
          <a class="navbar-brand" href="index.php">HotelMIS Task Scheduler</a>
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
            <div class="row">
                       <div class="col-md-6">
					<div class="section-title">
                            <h3>To Create HMIS Auto Schedule Rules</h3>
                            <div class="clearfix"></div>
                        </div>
						

   	<div class="row">
       <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 

        <div class="task-form">
            <input type="text" id="task" name="task_name" placeholder="Enter task..." required>
            <select id="priority" name="task_priority">
                <option value="top">Top Priority</option>
                <option value="middle">Middle Priority</option>
                <option value="low">Less Priority</option>
            </select>
            <input type="date" id="deadline" name="task_date" required>
            <input type="time" id="task_time" name="task_time" required>
            <button type="submit" id="add-task">Add Task</button>
        </div>
    </form>
        <div class="task-list" id="task-list">
            <!-- Tasks will be added here dynamically -->
        </div>
        <script src="logic.js"></script>
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

<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set your connection variables
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "HMIS";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted and if form data is set
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['task_name'], $_POST['task_priority'], $_POST['task_date'], $_POST['task_time'])) {
    // Get form data
    $task_name = $_POST['task_name'];
    $task_priority = $_POST['task_priority'];
    $task_date = $_POST['task_date'];
    $task_time = $_POST['task_time'];

    // Debug output
    echo "Form Data Received: <br>";
    echo "Task Name: $task_name <br>";
    echo "Task Priority: $task_priority <br>";
    echo "Task Date: $task_date <br>";
    echo "Task Time: $task_time <br>";

    // Insert task into the database
    $sql = "INSERT INTO tasks (task_name, task_priority, task_date, task_time) VALUES ('$task_name', '$task_priority', '$task_date', '$task_time')";

    if ($conn->query($sql) === TRUE) {
        echo "New task created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Form data is missing.";
}

$conn->close();
?>
