<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/session_check.php';
require_once __DIR__ . '/config/language.php';
require_once __DIR__ . '/function/check_permission.php';
require_once __DIR__ . '/config/db_config.php';
requireModulePermission('admin_job_assignment', 'index.php');
$user = json_encode($_SESSION);

$conn = getDBConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $orderId = isset($_POST["orderId"]) ? intval($_POST["orderId"]) : 0;
    $status = isset($_POST["status"]) ? $_POST["status"] : '';

    if ($orderId > 0 && !empty($status)) {
        $getOrderStmt = $conn->prepare("SELECT OrderType, OrderRemark FROM orderbookings WHERE OrderID = ?");
        $getOrderStmt->execute([$orderId]);
        $orderRow = $getOrderStmt->fetch();
        
        if ($orderRow) {
            $orderType = $orderRow['OrderType'];
            $orderRemark = $orderRow['OrderRemark'];
            
            if ($status == 'Canceled') {
                if ($orderType == 'Hotel') {
                    preg_match('/^([A-Za-z0-9\s]+)\s\|/', $orderRemark, $matches);
                    if (isset($matches[1])) {
                        $roomType = trim($matches[1]);
                        $restoreStmt = $conn->prepare("UPDATE hotelroomtype SET daily_quantity = daily_quantity + 1 WHERE HotelRoomtype = ?");
                        $restoreStmt->execute([$roomType]);
                    }
                } elseif ($orderType == 'Limo') {
                    preg_match('/^([A-Za-z0-9\s]+)\s\|/', $orderRemark, $matches);
                    if (isset($matches[1])) {
                        $vehicleType = trim($matches[1]);
                        $restoreStmt = $conn->prepare("UPDATE hotelvehicletype SET daily_quantity = daily_quantity + 1 WHERE VehicleType = ?");
                        $restoreStmt->execute([$vehicleType]);
                    }
                }
            }
        }

        $updateStmt = $conn->prepare("UPDATE orderbookings SET Status = ?, OrderModifiedDate = NOW() WHERE OrderID = ?");
        if ($updateStmt->execute([$status, $orderId])) {
            echo "Status updated successfully";
        } else {
            echo "Error updating status";
        }
    }
}

function getOrdersByStatus($status, $conn) {
    if ($status === 'All') {
        $stmt = $conn->prepare("SELECT * FROM orderbookings WHERE ordertype = 'Limo'");
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("SELECT * FROM orderbookings WHERE status = ? AND ordertype = 'Limo'");
        $stmt->execute([$status]);
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getDrivers($conn) {
    $stmt = $stmt = $conn->prepare("SELECT DriverID, DriverName, DriverStatus FROM hoteldriver where DriverStatus = 'available' or DriverStatus = 'busy'");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getStatusLabel($status) {
    $labels = [
        'offline' => 'Offline',
        'online' => 'Online',
        'available' => 'Available',
        'busy' => 'Busy'
    ];
    return isset($labels[$status]) ? $labels[$status] : $status;
}

$drivers = getDrivers($conn);
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
  
  .complete-btn {
    background-color: #ff9800; /* Orange */
    border: none;
    color: white;
    padding: 10px 16px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 14px;
    cursor: pointer;
    margin-left: 5px;
  }

  .escalate-btn {
    background-color: #dc3545; /* Red */
    border: none;
    color: white;
    padding: 10px 16px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 14px;
    cursor: pointer;
    margin-left: 5px;
  }

  .escalate-btn:disabled {
    background-color: #6c757d; /* Gray */
    cursor: not-allowed;
    opacity: 0.6;
  }

  .status-badge {
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: bold;
  }
  .status-offline { background-color: #dc3545; color: white; }
  .status-online { background-color: #17a2b8; color: white; }
  .status-available { background-color: #28a745; color: white; }
  .status-busy { background-color: #ffc107; color: #333; }
  
  .status-btn {
      padding: 5px 10px;
      margin: 2px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 12px;
  }
  .status-online-btn { background-color: #17a2b8; color: white; }
  .status-available-btn { background-color: #28a745; color: white; }
  .status-offline-btn { background-color: #dc3545; color: white; }
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
              <button class="tablinks" onclick="openTab(event, 'DriverManagement')">Driver Mgmt</button>
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
  $orders = getOrdersByStatus('All', $conn);
  if (!empty($orders)) {
      foreach($orders as $row) {
          echo "<tr>";
          echo "<td>" . htmlspecialchars($row["OrderID"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["OrderType"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["Time"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["Email"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["OrderRemark"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["Status"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["AssignedTo"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["OrderModifiedDate"]) . "</td>";
          echo "<td><button class='action-btn review' onclick='openModal(\"" . htmlspecialchars($row["OrderID"]) . "\", \"" . htmlspecialchars($row["OrderType"]) . "\", \"" . htmlspecialchars($row["Time"]) . "\", \"" . htmlspecialchars($row["Email"]) . "\", \"" . htmlspecialchars($row["OrderRemark"]) . "\", \"" . htmlspecialchars($row["Status"]) . "\", \"" . htmlspecialchars($row["AssignedTo"]) . "\", \"" . htmlspecialchars($row["OrderModifiedDate"]) . "\")'>Review</button></td>";
          echo "</tr>";
      }
  } else {
      echo "<tr><td colspan='9'>0 results</td></tr>";
  }
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
					<th onclick="sortTable(8)">Order Modified Date</th>
                    <th>Action</th>
                  </tr>   
				  
	<?php
  $confirmedOrders = getOrdersByStatus('Confirmed', $conn);
  if (!empty($confirmedOrders)) {
      foreach($confirmedOrders as $row) {
          echo "<tr>";
          echo "<td>" . htmlspecialchars($row["OrderID"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["OrderType"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["Time"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["Email"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["OrderRemark"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["Status"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["OrderCreatedDate"]) . "</td>";
          echo "<td>";
          echo "<select id='driverSelect_" . htmlspecialchars($row["OrderID"]) . "' name='driverId'>";
          echo "<option value='0'>None</option>";
          foreach($drivers as $driver) {
              $statusLabel = getStatusLabel($driver['DriverStatus']);
              $disabled = ($driver['DriverStatus'] == 'offline' || $driver['DriverStatus'] == 'busy') ? 'disabled' : '';
              echo "<option value='" . htmlspecialchars($driver['DriverID']) . "' " . $disabled;
              if ($driver['DriverName'] == $row["AssignedTo"]) {
                  echo " selected";
              }
              echo ">" . htmlspecialchars($driver['DriverName']) . " (" . $statusLabel . ")</option>";
          }
          echo "</select>";
          echo "</td>";
          echo "<td>" . htmlspecialchars($row["OrderModifiedDate"]) . "</td>";
          echo "<td>";
          echo "<button class='action-btn assign-btn' onclick='assignDriver(\"" . htmlspecialchars($row["OrderID"]) . "\")'>Assign</button>";
          echo "<button class='action-btn auto-assign-btn' onclick='autoAssignDriver(\"" . htmlspecialchars($row["OrderID"]) . "\")'>Auto Assign</button>";
          if ($row["AssignedTo"] != "None" && $row["AssignedTo"] != "") {
              echo "<button class='action-btn complete-btn' onclick='completeOrder(\"" . htmlspecialchars($row["OrderID"]) . "\", \"" . htmlspecialchars($row["AssignedTo"]) . "\")'>Complete</button>";
          }
          $escalated = isset($row["escalated"]) ? intval($row["escalated"]) : 0;
          $disabledAttr = $escalated == 1 ? 'disabled' : '';
          $buttonText = $escalated == 1 ? 'Escalated ✓' : 'Escalate';
          echo "<button class='action-btn escalate-btn' " . $disabledAttr . " onclick='escalateOrder(\"" . htmlspecialchars($row["OrderID"]) . "\")'>" . $buttonText . "</button>";
          echo "</td>";
          echo "</tr>";
      }
  } else {
      echo "<tr><td colspan='10'>0 results</td></tr>";
  }
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
  $rejectedOrders = getOrdersByStatus('Rejected', $conn);
  if (!empty($rejectedOrders)) {
      foreach($rejectedOrders as $row) {
          echo "<tr>";
          echo "<td>" . htmlspecialchars($row["OrderID"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["OrderType"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["Time"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["Email"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["OrderRemark"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["Status"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["OrderCreatedDate"]) . "</td>";
          echo "<td>" . htmlspecialchars($row["OrderModifiedDate"]) . "</td>";
          echo "<td><button class='action-btn review' onclick='openModal(\"" . htmlspecialchars($row["OrderID"]) . "\", \"" . htmlspecialchars($row["OrderType"]) . "\", \"" . htmlspecialchars($row["Time"]) . "\", \"" . htmlspecialchars($row["Email"]) . "\", \"" . htmlspecialchars($row["OrderRemark"]) . "\", \"" . htmlspecialchars($row["Status"]) . "\", \"" . htmlspecialchars($row["OrderCreatedDate"]) . "\", \"" . htmlspecialchars($row["OrderModifiedDate"]) . "\")'>Review</button></td>";
          echo "</tr>";
      }
  } else {
      echo "<tr><td colspan='9'>0 results</td></tr>";
  }

closeDBConnection($conn);
  ?>
                </table>
              </table>
            </div>
            
            <div id="DriverManagement" class="tabcontent">
              <h3>Driver Status Management</h3>
              <table id="driverTable">
                <tr>
                    <th>Driver ID</th>
                    <th>Driver Name</th>
                    <th>Source</th>
                    <th>Current Status</th>
                    <th>Action</th>
                </tr>
                <?php
                $conn = getDBConnection();
                // 司机管理菜单：查询所有司机，无任何条件和排序
                $driverStmt = $conn->prepare("SELECT * FROM hoteldriver");
                $driverStmt->execute();
                $allDrivers = $driverStmt->fetchAll(PDO::FETCH_ASSOC);
                
                foreach($allDrivers as $driver) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($driver["DriverID"]) . "</td>";
                    echo "<td>" . htmlspecialchars($driver["DriverName"]) . "</td>";
                    echo "<td>" . htmlspecialchars($driver["DriverSource"]) . "</td>";
                    echo "<td><span class='status-badge status-" . htmlspecialchars($driver["DriverStatus"]) . "'>" . getStatusLabel($driver["DriverStatus"]) . "</span></td>";
                    echo "<td>";
                    echo "<button class='status-btn status-online-btn' onclick='updateDriverStatus(" . $driver["DriverID"] . ", \"online\")'>Online</button>";
                    echo "<button class='status-btn status-available-btn' onclick='updateDriverStatus(" . $driver["DriverID"] . ", \"available\")'>Available</button>";
                    echo "<button class='status-btn status-offline-btn' onclick='updateDriverStatus(" . $driver["DriverID"] . ", \"offline\")'>Offline</button>";
                    echo "</td>";
                    echo "</tr>";
                }
                closeDBConnection($conn);
                ?>
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
    modalText.dataset.orderId = orderId;
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
    xhr.open("POST", "<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); 
    xhr.onreadystatechange = function() {
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
            console.log('Server response:', this.responseText);
            modal.style.display = "none";
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
        var limit = 10;
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
          var startRecord = ((currentPage - 1) * limit) + 1;
          var endRecord = Math.min(currentPage * limit, totalRows);
          document.getElementById('recordNumber').innerText = 'Showing ' + startRecord + ' to ' + endRecord + ' of ' + totalRows;
        }

        function changePage(delta) {
          currentPage += delta;
          currentPage = Math.max(1, Math.min(currentPage, Math.ceil(totalRows / limit)));
          paginate();
        }
        paginate();

		function assignDriver(orderId) {
    var driverSelect = document.getElementById("driverSelect_" + orderId);
    var driverId = driverSelect.options[driverSelect.selectedIndex].value;
    if (driverId === "0") {
        alert("Please select a driver");
        return;
    }

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "function/assignDriver.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
            var response = JSON.parse(this.responseText);
            if (response.success) {
                alert("Driver assigned successfully");
                location.reload();
            } else {
                alert("Failed: " + response.message);
            }
        }
    }
    xhr.send("orderId=" + orderId + "&driverId=" + encodeURIComponent(driverId));
}

function autoAssignDriver(orderId) {
    var driverSelect = document.getElementById("driverSelect_" + orderId);
    var driverId = driverSelect.options[driverSelect.selectedIndex].value;

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "function/getFirstAvailableDriver.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
            var response = JSON.parse(this.responseText);
            if (response.success) {
                var driverName = response.driverName;
                if (confirm("Auto assign to driver: " + driverName + "?")) {
                    // Set the dropdown to this driver and call assignDriver
                    driverSelect.value = response.driverId;
                    assignDriver(orderId);
                }
            } else {
                alert("No available drivers at this time");
            }
        }
    }
    xhr.send();
}

function completeOrder(orderId, driverName) {
    if (!confirm("Confirm to complete order " + orderId + "?\nDriver " + driverName + " will be set to Available.")) {
        return;
    }

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "function/completeOrder.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
            var response = JSON.parse(this.responseText);
            if (response.success) {
                alert("Order completed, driver status updated to Available");
                location.reload();
            } else {
                alert("Failed: " + response.message);
            }
        }
    }
    xhr.send("orderId=" + orderId);
}

function escalateOrder(orderId) {
    if (!confirm("Confirm to escalate order " + orderId + "?")) {
        return;
    }

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "function/escalateOrder.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
            var response = JSON.parse(this.responseText);
            if (response.success) {
                alert("Order escalated successfully");
                location.reload();
            } else {
                alert("Failed: " + response.message);
            }
        }
    }
    xhr.send("orderId=" + orderId);
}

function updateDriverStatus(driverId, status) {
    var statusLabels = {
        'online': 'Online',
        'available': 'Available',
        'offline': 'Offline'
    };
    
    if (!confirm("Confirm to set driver status to " + statusLabels[status] + "?")) {
        return;
    }

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "function/updateDriverStatus.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
            var response = JSON.parse(this.responseText);
            if (response.success) {
                alert("Driver status updated");
                location.reload();
            } else {
                alert("Update failed: " + response.message);
            }
        }
    }
    xhr.send("driverId=" + driverId + "&status=" + encodeURIComponent(status));
}

        
    </script>
  </body>
</html>