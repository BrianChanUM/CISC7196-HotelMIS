<?php
    session_start();
    $user = json_encode($_SESSION);
?>




<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Basic Page Needs
    ================================================== -->
    <meta charset="utf-8">
    <!--[if IE]>
			<meta http-equiv="x-ua-compatible" content="IE=9" />
			<![endif]-->
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
	<link rel="stylesheet" type="text/css" href="css/ordertable.css">
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Slider
    ================================================== -->
    <link href="css/owl.carousel.css" rel="stylesheet" media="screen">
    <link href="css/owl.theme.css" rel="stylesheet" media="screen">
    <!-- Stylesheet
    ================================================== -->
    <link rel="stylesheet" type="text/css" href="css/style.css">
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
        <!-- Collect the nav links, forms, and other content for toggling -->
        
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1"> <?php include(__DIR__ . '/layout/header.php');?> <ul class="nav navbar-nav navbar-right" id="navbar"></ul> <?php include(__DIR__ . '/layout/navbar.php');?> </div>
        <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
    </nav>
    <!-- Home Page
    ==========================================-->
    <div id="tf-home" class="text-center">
      <a href="#tf-contact"></a>
    </div>
    <div id="tf-about">
      <div class="container">

        <div class="row">
		 <h2>Hotel Table Bookings</h2> 
		 
		<div class="tab">
			<input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search for bookings.."> 
			<button class="tablinks" onclick="openTab(event, 'All')">All</button>
              <button class="tablinks" onclick="openTab(event, 'TBC')">TBC</button>
              <button class="tablinks" onclick="openTab(event, 'Confirmed')">Confirmed</button>
              <button class="tablinks" onclick="openTab(event, 'Canceled')">Canceled</button>
				 <button onclick="changePage(-1)">Previous</button>
				<button onclick="changePage(1)">Next</button>
<!-- Add these two input fields for the start and end dates -->
<input type="date" id="startDate" name="startDate">
<input type="date" id="endDate" name="endDate">
<button onclick="filterByDate()">Filter</button>
            </div> 
			
	<p id="recordNumber"></p>
          <div class="col-md-6">
            <div id="All" class="tabcontent">
              <h3>All</h3>
 <table id="orderbookings">
  <?php
          $servername = "localhost";
          $username = "root";
          $password = "";
          $dbname = "hmis";

          // Create connection
          $conn = new mysqli($servername, $username, $password, $dbname);

          // Check connection
          if ($conn->connect_error) {
              die("Connection failed: " . $conn->connect_error);
          }
		  
			$startDate = isset($_POST['startDate']) ? $_POST['startDate'] : '';
			$endDate = isset($_POST['endDate']) ? $_POST['endDate'] : '';
			$statuses = ['All', 'TBC', 'Confirmed', 'Canceled'];
	

    foreach ($statuses as $status) {
        echo "<div id=\"$status\" class=\"tabcontent\">";
        echo "<h3>$status</h3>";
       echo "<table id=\"orderbookings_$status\">";


        // Add the table headers here
        echo "<tr>";
        echo "<th onclick=\"sortTable(0)\">Order ID</th>";
        echo "<th onclick=\"sortTable(1)\">Order Type</th>";
        echo "<th onclick=\"sortTable(2)\">Time</th>";
        echo "<th onclick=\"sortTable(3)\">Email</th>";
        echo "<th onclick=\"sortTable(4)\">Order Remark</th>";
        echo "<th onclick=\"sortTable(5)\">Status</th>";
        echo "<th onclick=\"sortTable(6)\">Order Created Date</th>";
        echo "<th onclick=\"sortTable(7)\">Order Modified Date</th>";
        echo "<th>Action</th>";
        echo "</tr>";

               if ($status == 'All') {
            $sql = "SELECT * FROM orderbookings";
            if (!empty($startDate) && !empty($endDate)) {
                $sql .= " WHERE OrderCreatedDate BETWEEN '$startDate' AND '$endDate'";
            }
        } else {
            $sql = "SELECT * FROM orderbookings WHERE status = '$status'";
            if (!empty($startDate) && !empty($endDate)) {
                $sql .= " AND OrderCreatedDate BETWEEN '$startDate' AND '$endDate'";
            }
            $sql .= " ORDER BY OrderCreatedDate DESC";
        }

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["OrderID"] . "</td>";
                echo "<td>" . $row["OrderType"] . "</td>";
                echo "<td>" . $row["Time"] . "</td>";
                echo "<td>" . $row["Email"] . "</td>";
                echo "<td>" . $row["OrderRemark"] . "</td>";
                echo "<td>" . $row["Status"] . "</td>";
                echo "<td>" . $row["OrderCreatedDate"] . "</td>";
                echo "<td>" . $row["OrderModifiedDate"] . "</td>";
                echo "<td><button class='action-btn review' onclick='openModal(\"" . $row["OrderID"] . "\", \"" . $row["OrderType"] . "\", \"" . $row["Time"] . "\", \"" . $row["Email"] . "\", \"" . $row["OrderRemark"] . "\", \"" . $row["Status"] . "\", \"" . $row["OrderCreatedDate"] . "\", \"" . $row["OrderModifiedDate"] . "\", \"" . $row["AssignedTo"] . "\")'>Review</button></td>";
                echo "</tr>";
            }
        } else {
            echo "0 results";
        }

        echo "</table>";
        echo "</div>";
    }
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["orderId"]) && isset($_POST["status"])) {
        $orderId = $_POST["orderId"];
        $status = $_POST["status"];
        $date = date('Y-m-d H:i:s'); // Get the current date and time

        $sql = "UPDATE orderbookings SET Status='$status', OrderModifiedDate=NOW() WHERE OrderID=$orderId";
        if ($conn->query($sql) === TRUE) {
            echo "Status updated successfully";
        } else {
            echo "Error updating status: " . $conn->error;
        }
    }
}
    $conn->close();
?>
</table></div>
            
          
          <div id="myModal" class="modal">
            <div class="modal-content">
              <span class="close">&times;</span>
              <p id="modalText"></p>
              <div id="buttonContainer">
                <button class="action-btn confirm" onclick="confirmBooking()">Confirm</button>
                <button class="action-btn cancel" onclick="cancelBooking()">Cancel</button>
                <button id="closeButton" onclick="closeModal()">X</button>
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
   
    <!-- Javascripts
    ================================================== -->
    <script type="text/javascript" src="js/main.js"></script>
    <script>
      var modal = document.getElementById("myModal");
      var span = document.getElementsByClassName("close")[0];

function openModal(orderId, time, places, eventType, contact, phone, email, status, AssignedTo) {
    var modal = document.getElementById("myModal"); // Make sure "myModal" is the id of your modal
    var modalText = document.getElementById("modalText");
    modalText.innerHTML = "<b>Order Details</b><br>Order ID: " + orderId + "<br>Order Type: " + time + "<br>Time: " + places + "<br>Email: " + eventType + "<br>Order Remark: " + contact + "<br>Last Status: " + phone + "<br>Order Created Date: " + email + "<br>Status: " + status + "<br><b>Assigned To:</b> " + AssignedTo;
    modalText.dataset.orderId = orderId; // Set the orderId in a data attribute
    modal.style.display = "block";
}
      span.onclick = function() {
        modal.style.display = "none";
      }
      window.onclick = function(event) {
        if (event.target == modal) {
          modal.style.display = "none";
        }
      }
      document.querySelectorAll(".confirm").forEach(function(button) {
        button.addEventListener("click", function() {
          updateStatus("Confirmed");
        });
      });
      document.querySelectorAll(".cancel").forEach(function(button) {
        button.addEventListener("click", function() {
          updateStatus("Canceled");
        });
      });

 function updateStatus(status) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "<?php echo $_SERVER['PHP_SELF']; ?>", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); 
    xhr.onreadystatechange = function() {
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
            // Log the server response for debugging
            console.log('Server response:', this.responseText);
            // Close the modal
            modal.style.display = "none";
            // Refresh the page
            location.reload();
        }
    }
    xhr.onerror = function() {
        console.log('Request failed', xhr.response);
    };
    console.log("Sending data: orderId=" + document.getElementById("modalText").dataset.orderId + "&status=" + status);
    xhr.send("orderId=" + document.getElementById("modalText").dataset.orderId + "&status=" + status);
}
		
        function confirmBooking() {
          alert("Booking confirmed!");
        }

        function cancelBooking() {
          alert("Booking canceled!");
        }

        function closeModal() {
          modal.style.display = "none";
        }
		
		function openTab(evt, status) {
          var i, tabcontent, tablinks;
          tabcontent = document.getElementsByClassName("tabcontent");
          for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
          }
          tablinks = document.getElementsByClassName("tablinks");
          for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
          }
          document.getElementById(status).style.display = "block";
          evt.currentTarget.className += " active";
        }
		
		document.addEventListener('DOMContentLoaded', function() {
  openTab(null, 'All');
});
		
var statuses = ['All', 'TBC', 'Confirmed', 'Canceled'];
var limit = 20; // Number of rows per page
var currentPage = 1;

function paginate() {
    statuses.forEach(function(status) {
        var table = document.getElementById('orderbookings_' + status);
        var totalRows = table.rows.length;
        for (var i = 1; i < totalRows; i++) {
            if (i < ((currentPage - 1) * limit) + 1 || i > (currentPage * limit)) {
                table.rows[i].style.display = 'none';
            } else {
                table.rows[i].style.display = '';
            }
        }
        // Update record number
        var startRecord = ((currentPage - 1) * limit) + 1;
        var endRecord = Math.min(currentPage * limit, totalRows - 1);
        document.getElementById('recordNumber').innerText = 'Showing ' + startRecord + ' to ' + endRecord + ' of ' + (totalRows - 1);
    });
}

window.changePage = function(delta) {
    currentPage += delta;
    // Make sure currentPage is within valid range
    var maxPage = 1; // Initialize to 1, will be calculated for each table
    statuses.forEach(function(status) {
        var table = document.getElementById('orderbookings_' + status);
        var totalRows = table.rows.length;
        maxPage = Math.max(maxPage, Math.ceil((totalRows - 1) / limit));
    });
    currentPage = Math.max(1, Math.min(currentPage, maxPage));
    paginate();
}

paginate();

function filterByDate() {
    var startDate = $('#startDate').val();
    var endDate = $('#endDate').val();

    $.ajax({
        url: window.location.href,  // Replace with the path to your PHP script
        type: 'POST',
        data: {
            'startDate': startDate,
            'endDate': endDate
        },
        success: function(data) {
            // Refresh your table with the new data
            $('#orderbookings').html(data);
        }
    });
}

function searchTable() {
    // Get the search query
    var query = document.getElementById('searchInput').value.toLowerCase();

    // Loop through all the tables
    statuses.forEach(function(status) {
        var table = document.getElementById('orderbookings_' + status);
        var rows = table.rows;

        // Loop through all the rows (skip the first one, as it's the header)
        for (var i = 1; i < rows.length; i++) {
            // If the row's text includes the search query, show it; otherwise, hide it
            if (rows[i].textContent.toLowerCase().includes(query)) {
                rows[i].style.display = '';
            } else {
                rows[i].style.display = 'none';
            }
        }
    });
}

    </script>
  </body>
</html>

<!--
<?php
// Database connection
$servername = "localhost";
          $username = "root";
          $password = "";
          $dbname = "hmis";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch users
$sql = "SELECT id, username FROM user WHERE role = 'staff'";
$result = $conn->query($sql);

// Initialize users array
$users = array();

if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
} else {
    echo "0 results";
}

$conn->close();

// Return users as JSON
echo json_encode($users);
?>  