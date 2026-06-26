<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    require_once __DIR__ . '/config/language.php';
    require_once __DIR__ . '/function/check_permission.php';
    requireModulePermission('admin_job_assignment', 'index.php');
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
	<link rel="stylesheet" type="text/css" href="css/dutytable.css">
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
	<style>
  .assign-btn{
    background-color: #04AA6D; /* Green */
    border: none;
    color: white;
    padding: 10px 16px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 14px;
    cursor: pointer; /* Add a pointer cursor on hover */
  }
</style>
	
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
        
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1"> <?php include(__DIR__ . '/layout/header.php');?> <ul class="nav navbar-nav navbar-right" id="navbar"></ul> <?php include(__DIR__ . '/layout/language_switcher.php');?> <?php include(__DIR__ . '/layout/navbar.php');?> </div>
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
		 <h2>Pending Assign duty</h2> 
		<div class="tab">
				 <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search for bookings.."> 
			<button class="tablinks" onclick="openTab(event, 'All')">All</button>
             <!--  <button class="tablinks" onclick="openTab(event, 'TBC')">TBC</button> -->
              <button class="tablinks" onclick="openTab(event, 'Confirmed')">Confirmed</button>
              <button class="tablinks" onclick="openTab(event, 'Rejected')">Rejected</button>
	  <button onclick="changePage(-1)">Previous</button>
            <button onclick="changePage(1)">Next</button>
			
            </div> 
	<p id="recordNumber"></p>
          <div class="col-md-6">

            <div id="All" class="tabcontent">
              <h3>All</h3>
 <table id="orderbookings">
  <tr>
    <th onclick="sortTable(0)">Order ID</th>
    <th onclick="sortTable(1)">Order Type</th>
    <th onclick="sortTable(2)">Time</th>
    <th onclick="sortTable(3)">Email</th>
    <th onclick="sortTable(4)">Order Remark</th>
    <th onclick="sortTable(5)">Status</th>
    <th onclick="sortTable(6)">Assigned To</th>
    <th onclick="sortTable(7)">Order Modified Date</th>
    <th>Action</th>
  </tr>
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
  $sql = "SELECT * FROM orderbookings where ordertype in ('IRD','Limo') ";
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
          echo "<td>" . $row["AssignedTo"] . "</td>";
		  
          echo "<td>" . $row["OrderModifiedDate"] . "</td>";
          echo "<td><button class='action-btn review' onclick='openModal(\"" . $row["OrderID"] . "\", \"" . $row["OrderType"] . "\", \"" . $row["Time"] . "\", \"" . $row["Email"] . "\", \"" . $row["OrderRemark"] . "\", \"" . $row["Status"] . "\", \"" . $row["AssignedTo"] . "\", \"" . $row["OrderModifiedDate"] . "\")'>Review</button></td>";
		  
          echo "</tr>";
      }
  } else {
      echo "0 results";
  }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $orderId = $_POST["orderId"];
    $status = $_POST["status"];
    $date = date('Y-m-d H:i:s');

    $getOrderSql = "SELECT OrderType, OrderRemark FROM orderbookings WHERE OrderID = $orderId";
    $orderResult = $conn->query($getOrderSql);
    
    if ($orderResult && $orderRow = $orderResult->fetch_assoc()) {
        $orderType = $orderRow['OrderType'];
        $orderRemark = $orderRow['OrderRemark'];
        
        if ($status == 'Canceled') {
            if ($orderType == 'Hotel') {
                preg_match('/^([A-Za-z0-9\s]+)\s\|/', $orderRemark, $matches);
                if (isset($matches[1])) {
                    $roomType = trim($matches[1]);
                    $restoreSql = "UPDATE hotelroomtype SET daily_quantity = daily_quantity + 1 WHERE HotelRoomtype = ?";
                    $restoreStmt = $conn->prepare($restoreSql);
                    $restoreStmt->bind_param("s", $roomType);
                    $restoreStmt->execute();
                    $restoreStmt->close();
                }
            } elseif ($orderType == 'Limo') {
                preg_match('/^([A-Za-z0-9\s]+)\s\|/', $orderRemark, $matches);
                if (isset($matches[1])) {
                    $vehicleType = trim($matches[1]);
                    $restoreSql = "UPDATE hotelvehicletype SET daily_quantity = daily_quantity + 1 WHERE VehicleType = ?";
                    $restoreStmt = $conn->prepare($restoreSql);
                    $restoreStmt->bind_param("s", $vehicleType);
                    $restoreStmt->execute();
                    $restoreStmt->close();
                }
            }
        }
    }

    $sql = "UPDATE orderbookings SET Status='$status', OrderModifiedDate=NOW() WHERE OrderID=$orderId";
    if ($conn->query($sql) === TRUE) {
        echo "Status updated successfully";
    } else {
        echo "Error updating status: " . $conn->error;
    }
}
  $conn->close();
  ?>
</table></div>
            
       
			
			
			
            <div id="Confirmed" class="tabcontent">
              <h3>Confirmed</h3>
              <table id="confirmedTable">
                <table id="orderbookings">
                  <tr>
                    <th onclick="sortTable(0)">Order ID</th>
                    <th onclick="sortTable(1)">Order Type</th>
                    <th onclick="sortTable(2)">Time</th>
                    <th onclick="sortTable(3)">Email</th>
                    <th onclick="sortTable(4)">Order Remark</th>
                    <th onclick="sortTable(5)">Status</th>
                    <th onclick="sortTable(6)">Order Created Date</th>
                    <th onclick="sortTable(7)">Assign Duty to</th>
					<th onclick="sortTable(8)">Order Modified Date</th> <!-- New column header -->
                    <th>Action</th>
                  </tr>   
				  
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
  $sql = "SELECT * FROM orderbookings where status = 'Confirmed'";
  $result = $conn->query($sql);
  
   // Fetch driver names
  $sql = "SELECT DriverName FROM hoteldriver";
  $driversResult = $conn->query($sql);
  $drivers = [];
  while($driver = $driversResult->fetch_assoc()) {
      $drivers[] = $driver["DriverName"];
  }

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
		   echo "<td>";
    echo "<select id='driverSelect_" . $row["OrderID"] . "' name='driver'>";
	echo "<option value='None'>None</option>"; // Default "None" option
    foreach($drivers as $driver) {
        echo "<option value='" . $driver . "'";
        if ($driver == $row["AssignedTo"]) {
            echo " selected";
        }
        echo ">" . $driver . "</option>";
    }
          echo "</select>";
          echo "</td>";
          echo "<td>" . $row["OrderModifiedDate"] . "</td>";
         // echo "<td><button class='action-btn review' onclick='openModal(\"" . $row["OrderID"] . "\", \"" . $row["OrderType"] . "\", \"" . $row["Time"] . "\", \"" . $row["Email"] . "\", \"" . $row["OrderRemark"] . "\", \"" . $row["Status"] . "\", \"" . $row["OrderCreatedDate"] . "\", \"" . $row["OrderModifiedDate"] . "\")'>Review</button></td>";
         echo "<td><button class='action-btn assign-btn' onclick='assignDriver(\"" . $row["OrderID"] . "\")'>Assign</button></td>";
		 echo "</tr>";
      }
  } else {
      echo "0 results";
  }
  
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $orderId = $_POST["orderId"];
    $status = $_POST["status"];
    $date = date('Y-m-d H:i:s');

    $getOrderSql = "SELECT OrderType, OrderRemark FROM orderbookings WHERE OrderID = $orderId";
    $orderResult = $conn->query($getOrderSql);
    
    if ($orderResult && $orderRow = $orderResult->fetch_assoc()) {
        $orderType = $orderRow['OrderType'];
        $orderRemark = $orderRow['OrderRemark'];
        
        if ($status == 'Canceled') {
            if ($orderType == 'Hotel') {
                preg_match('/^([A-Za-z0-9\s]+)\s\|/', $orderRemark, $matches);
                if (isset($matches[1])) {
                    $roomType = trim($matches[1]);
                    $restoreSql = "UPDATE hotelroomtype SET daily_quantity = daily_quantity + 1 WHERE HotelRoomtype = ?";
                    $restoreStmt = $conn->prepare($restoreSql);
                    $restoreStmt->bind_param("s", $roomType);
                    $restoreStmt->execute();
                    $restoreStmt->close();
                }
            } elseif ($orderType == 'Limo') {
                preg_match('/^([A-Za-z0-9\s]+)\s\|/', $orderRemark, $matches);
                if (isset($matches[1])) {
                    $vehicleType = trim($matches[1]);
                    $restoreSql = "UPDATE hotelvehicletype SET daily_quantity = daily_quantity + 1 WHERE VehicleType = ?";
                    $restoreStmt = $conn->prepare($restoreSql);
                    $restoreStmt->bind_param("s", $vehicleType);
                    $restoreStmt->execute();
                    $restoreStmt->close();
                }
            }
        }
    }

   $sql = "UPDATE orderbookings SET Status='$status', OrderModifiedDate=NOW() WHERE OrderID=$orderId";


    if ($conn->query($sql) === TRUE) {
        echo "Status updated successfully";
    } else {
        echo "Error updating status: " . $conn->error;
    }
}
  
  
  $conn->close();
  ?>
                </table>
              </table>
            </div>
            <div id="Rejected" class="tabcontent">
              <h3>Rejected</h3>
              <table id="rejectedTable">
                <table id="orderbookings">
                  <tr>
                    <th onclick="sortTable(0)">Order ID</th>
                    <th onclick="sortTable(1)">Order Type</th>
                    <th onclick="sortTable(2)">Time</th>
                    <th onclick="sortTable(3)">Email</th>
                    <th onclick="sortTable(4)">Order Remark</th>
                    <th onclick="sortTable(5)">Status</th>
                    <th onclick="sortTable(6)">Order Created Date</th>
                    <th onclick="sortTable(7)">Order Modified Date</th>
                    <th>Action</th>
                  </tr>   <?php

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
  $sql = "SELECT * FROM orderbookings where status = 'Rejected'";
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
          echo "<td><button class='action-btn review' onclick='openModal(\"" . $row["OrderID"] . "\", \"" . $row["OrderType"] . "\", \"" . $row["Time"] . "\", \"" . $row["Email"] . "\", \"" . $row["OrderRemark"] . "\", \"" . $row["Status"] . "\", \"" . $row["OrderCreatedDate"] . "\", \"" . $row["OrderModifiedDate"] . "\")'>Review</button></td>";
          echo "</tr>";
      }
  } else {
      echo "0 results";
  }
  
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $orderId = $_POST["orderId"];
    $status = $_POST["status"];
    $date = date('Y-m-d H:i:s');

    $getOrderSql = "SELECT OrderType, OrderRemark FROM orderbookings WHERE OrderID = $orderId";
    $orderResult = $conn->query($getOrderSql);
    
    if ($orderResult && $orderRow = $orderResult->fetch_assoc()) {
        $orderType = $orderRow['OrderType'];
        $orderRemark = $orderRow['OrderRemark'];
        
        if ($status == 'Canceled') {
            if ($orderType == 'Hotel') {
                preg_match('/^([A-Za-z0-9\s]+)\s\|/', $orderRemark, $matches);
                if (isset($matches[1])) {
                    $roomType = trim($matches[1]);
                    $restoreSql = "UPDATE hotelroomtype SET daily_quantity = daily_quantity + 1 WHERE HotelRoomtype = ?";
                    $restoreStmt = $conn->prepare($restoreSql);
                    $restoreStmt->bind_param("s", $roomType);
                    $restoreStmt->execute();
                    $restoreStmt->close();
                }
            } elseif ($orderType == 'Limo') {
                preg_match('/^([A-Za-z0-9\s]+)\s\|/', $orderRemark, $matches);
                if (isset($matches[1])) {
                    $vehicleType = trim($matches[1]);
                    $restoreSql = "UPDATE hotelvehicletype SET daily_quantity = daily_quantity + 1 WHERE VehicleType = ?";
                    $restoreStmt = $conn->prepare($restoreSql);
                    $restoreStmt->bind_param("s", $vehicleType);
                    $restoreStmt->execute();
                    $restoreStmt->close();
                }
            }
        }
    }

    $sql = "UPDATE orderbookings SET Status='$status', OrderModifiedDate=NOW() WHERE OrderID=$orderId";


    if ($conn->query($sql) === TRUE) {
        echo "Status updated successfully";
    } else {
        echo "Error updating status: " . $conn->error;
    }
}
  
  
  $conn->close();
  ?>
                </table>
              </table>
            </div>
          </div>
		  
		  
		  
		  
          <div id="myModal" class="modal">
            <div class="modal-content">
              <span class="close">&times;</span>
              <p id="modalText"></p>
              <div id="buttonContainer">
                <button class="action-btn confirm" onclick="confirmBooking()">Confirm</button>
                <button class="action-btn reject" onclick="rejectBooking()">Reject</button>
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

function openModal(orderId, time, places, eventType, contact, phone, email, status) {
    var modal = document.getElementById("myModal");
    var modalText = document.getElementById("modalText");
    modalText.innerHTML = "<b>Order Details</b><br>Order ID: " + orderId + "<br>Order Type: " + time + "<br>Time: " + places + "<br>Email: " + eventType + "<br>Order Remark: " + contact + "<br>Last Status: " + phone + "<br>Assigned To: " + email + "<br>Modified Time: " + status;
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
      document.querySelectorAll(".reject").forEach(function(button) {
        button.addEventListener("click", function() {
          updateStatus("Rejected");
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

        function rejectBooking() {
          alert("Booking rejected!");
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
		
		
		
        var table = document.getElementById('orderbookings');
        var totalRows = table.rows.length - 1;
        var limit = 10; // Number of rows per page
        var totalPages = Math.ceil(totalRows / limit);
        var currentPage = 1;

        function paginate() {
          for (var i = 1; i < totalRows; i++) {
            if (i < ((currentPage - 1) * limit) + 1 || i > (currentPage * limit)) {
              table.rows[i].style.display = 'none';
            } else {
              table.rows[i].style.display = '';
            }
          }
          // Update record number
          var startRecord = ((currentPage - 1) * limit) + 1;
          var endRecord = Math.min(currentPage * limit, totalRows);
          document.getElementById('recordNumber').innerText = 'Showing ' + startRecord + ' to ' + endRecord + ' of ' + totalRows;
        }

        function changePage(delta) {
          currentPage += delta;
          // Make sure currentPage is within valid range
          currentPage = Math.max(1, Math.min(currentPage, Math.ceil(totalRows / limit)));
          paginate();
        }
        paginate();

		function assignDriver(orderId) {
    var driverSelect = document.getElementById("driverSelect_" + orderId);
    var driver = driverSelect.options[driverSelect.selectedIndex].value;
	if (driver === "None") {
        alert("Please select a driver");
        return;
    }

    // Make an AJAX call to a PHP script to update the AssignedTo field
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "function/assignDriver.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
            alert("Driver assigned successfully");
			location.reload(); // Refresh the page
        }
    }
    xhr.send("orderId=" + orderId + "&driver=" + driver);
}

        
    </script>
  </body>
</html>