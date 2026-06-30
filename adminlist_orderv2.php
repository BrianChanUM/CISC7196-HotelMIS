<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    require_once __DIR__ . '/config/session_check.php';
    require_once __DIR__ . '/config/language.php';
    require_once __DIR__ . '/function/check_permission.php';
    require_once __DIR__ . '/config/db_config.php';
    requireModulePermission('admin_orders', 'index.php');
    $user = json_encode($_SESSION);

    $conn = getDBConnection();

    function getOrdersByStatus($status, $conn) {
        $sql = "SELECT * FROM orderbookings WHERE status = ? ORDER BY OrderCreatedDate DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$status]);
        return $stmt->fetchAll();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $orderId = isset($_POST["orderId"]) ? intval($_POST["orderId"]) : 0;
        $status = isset($_POST["status"]) ? $_POST["status"] : '';
        
        if ($orderId > 0 && !empty($status)) {
            $getOrderSql = "SELECT OrderType, OrderRemark FROM orderbookings WHERE OrderID = ?";
            $orderStmt = $conn->prepare($getOrderSql);
            $orderStmt->execute([$orderId]);
            $orderRow = $orderStmt->fetch();
            
            if ($orderRow) {
                $orderType = $orderRow['OrderType'];
                $orderRemark = $orderRow['OrderRemark'];
                
                if ($status == 'Canceled') {
                    if ($orderType == 'Hotel') {
                        preg_match('/^([A-Za-z0-9\s]+)\s\|/', $orderRemark, $matches);
                        if (isset($matches[1])) {
                            $roomType = trim($matches[1]);
                            $restoreSql = "UPDATE hotelroomtype SET daily_quantity = daily_quantity + 1 WHERE HotelRoomtype = ?";
                            $restoreStmt = $conn->prepare($restoreSql);
                            $restoreStmt->execute([$roomType]);
                        }
                    } elseif ($orderType == 'Limo') {
                        preg_match('/^([A-Za-z0-9\s]+)\s\|/', $orderRemark, $matches);
                        if (isset($matches[1])) {
                            $vehicleType = trim($matches[1]);
                            $restoreSql = "UPDATE hotelvehicletype SET daily_quantity = daily_quantity + 1 WHERE VehicleType = ?";
                            $restoreStmt = $conn->prepare($restoreSql);
                            $restoreStmt->execute([$vehicleType]);
                        }
                    }
                }
            }

            $updateSql = "UPDATE orderbookings SET Status = ?, OrderModifiedDate = NOW() WHERE OrderID = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->execute([$status, $orderId]);
            
            if ($updateStmt->rowCount() > 0) {
                echo "Status updated successfully";
            } else {
                echo "Error updating status";
            }
            exit;
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CISC7196-HotelMIS-2023OCT18</title>
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="img/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="img/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="img/apple-touch-icon-114x114.png">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="css/ordertable.css">
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="css/owl.carousel.css" rel="stylesheet" media="screen">
    <link href="css/owl.theme.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/responsive.css">
    <link href='http://fonts.googleapis.com/css?family=Lato:100,300,400,700,900,100italic,300italic,400italic,700italic,900italic' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,700,300,600,800,400' rel='stylesheet' type='text/css'>
    <script type="text/javascript" src="js/modernizr.custom.js"></script>
    <script>
      var modal = null;
      var span = null;
      var table = null;
      var totalRows = 0;
      var limit = 10;
      var currentPage = 1;

      function openModal(orderId, orderType, time, email, remark, status, createdDate, modifiedDate) {
        modal = document.getElementById("myModal");
        var modalText = document.getElementById("modalText");
        modalText.innerHTML = "<b>Order Details</b><br>Order ID: " + orderId + "<br>Order Type: " + orderType + "<br>Time: " + time + "<br>Email: " + email + "<br>Order Remark: " + remark + "<br>Status: " + status + "<br>Order Created Date: " + createdDate + "<br>Modified Date: " + modifiedDate;
        modalText.dataset.orderId = orderId;
        modal.style.display = "block";
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
        if (evt && evt.currentTarget) {
          evt.currentTarget.className += " active";
        }
      }

      function searchTable() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchInput");
        filter = input.value.toUpperCase();
        var activeTab = document.querySelector('.tablinks.active');
        var tabId = activeTab ? activeTab.textContent : 'All';
        table = document.getElementById("orderbookings_" + tabId.toLowerCase()) || document.getElementById("orderbookings");
        if (!table) return;
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td");
          var found = false;
          for (var j = 0; j < td.length; j++) {
            if (td[j]) {
              txtValue = td[j].textContent || td[j].innerText;
              if (txtValue.toUpperCase().indexOf(filter) > -1) {
                found = true;
                break;
              }
            }
          }
          tr[i].style.display = found ? "" : "none";
        }
      }

      function sortTable(n, tableId) {
        var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
        table = document.getElementById(tableId || "orderbookings");
        switching = true;
        dir = "asc";
        while (switching) {
          switching = false;
          rows = table.rows;
          for (i = 1; i < (rows.length - 1); i++) {
            shouldSwitch = false;
            x = rows[i].getElementsByTagName("TD")[n];
            y = rows[i + 1].getElementsByTagName("TD")[n];
            if (dir == "asc") {
              if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                shouldSwitch = true;
                break;
              }
            } else if (dir == "desc") {
              if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                shouldSwitch = true;
                break;
              }
            }
          }
          if (shouldSwitch) {
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
            switchcount ++;
          } else {
            if (switchcount == 0 && dir == "asc") {
              dir = "desc";
              switching = true;
            }
          }
        }
      }

      function sortTableConfirmed(n) {
        sortTable(n, "orderbookings_confirmed");
      }

      function sortTableRejected(n) {
        sortTable(n, "orderbookings_rejected");
      }

      function paginate() {
        if (!table) return;
        for (var i = 1; i < table.rows.length; i++) {
          if (i < ((currentPage - 1) * limit) + 1 || i > (currentPage * limit)) {
            table.rows[i].style.display = 'none';
          } else {
            table.rows[i].style.display = '';
          }
        }
        var startRecord = ((currentPage - 1) * limit) + 1;
        var endRecord = Math.min(currentPage * limit, totalRows);
        if (document.getElementById('recordNumber')) {
          document.getElementById('recordNumber').innerText = 'Showing ' + startRecord + ' to ' + endRecord + ' of ' + totalRows;
        }
      }

      function changePage(delta) {
        currentPage += delta;
        currentPage = Math.max(1, Math.min(currentPage, Math.ceil(totalRows / limit)));
        paginate();
      }

      function confirmBooking() {
        updateStatus("Confirmed");
      }

      function rejectBooking() {
        updateStatus("Rejected");
      }

      function closeModal() {
        if (modal) {
          modal.style.display = "none";
        }
      }

      function updateStatus(status) {
        var modalText = document.getElementById("modalText");
        var orderId = modalText.dataset.orderId;
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); 
        xhr.onreadystatechange = function() {
          if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
            console.log('Server response:', this.responseText);
            if (modal) {
              modal.style.display = "none";
            }
            location.reload();
          }
        }
        xhr.onerror = function() {
          console.log('Request failed', xhr.response);
        };
        xhr.send("orderId=" + orderId + "&status=" + status);
      }

      function initPage() {
        modal = document.getElementById("myModal");
        span = document.getElementsByClassName("close")[0];
        
        if (span) {
          span.onclick = function() {
            modal.style.display = "none";
          }
        }
        
        window.onclick = function(event) {
          if (event.target == modal) {
            modal.style.display = "none";
          }
        }
        
        table = document.getElementById('orderbookings');
        totalRows = table ? table.rows.length - 1 : 0;
        currentPage = 1;
        
        var firstTab = document.querySelector('.tablinks');
        if (firstTab) {
          firstTab.click();
        }
        
        paginate();
      }
    </script>
  </head>
  <body>
    <nav id="tf-menu" class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php"><?php echo t('hotel_management_system'); ?></a>
        </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1"> <?php include(__DIR__ . '/layout/header.php');?> <ul class="nav navbar-nav navbar-right" id="navbar"></ul> <?php include(__DIR__ . '/layout/language_switcher.php');?> <?php include(__DIR__ . '/layout/navbar.php');?> </div>
      </div>
    </nav>
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
              <button class="tablinks" onclick="openTab(event, 'Rejected')">Rejected</button>
	  <button onclick="changePage(-1)">Previous</button>
            <button onclick="changePage(1)">Next</button>
			
            </div> 
	<p id="recordNumber"></p>
          <div class="col-md-6">
            <?php include(__DIR__ . '/test/tablecontent.php');?>
            <div id="TBC" class="tabcontent">
              <h3>TBC</h3>
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
  </tr>
  <?php
  $tbcOrders = getOrdersByStatus('TBC', $conn);
  if (count($tbcOrders) > 0) {
      foreach($tbcOrders as $row) {
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
  ?>
 </table>
            </div>
            <div id="Confirmed" class="tabcontent">
              <h3>Confirmed</h3>
              <table id="confirmedTable">
                <table id="orderbookings_confirmed">
                  <tr>
                    <th onclick="sortTableConfirmed(0)">Order ID</th>
                    <th onclick="sortTableConfirmed(1)">Order Type</th>
                    <th onclick="sortTableConfirmed(2)">Time</th>
                    <th onclick="sortTableConfirmed(3)">Email</th>
                    <th onclick="sortTableConfirmed(4)">Order Remark</th>
                    <th onclick="sortTableConfirmed(5)">Status</th>
                    <th onclick="sortTableConfirmed(6)">Order Created Date</th>
                    <th onclick="sortTableConfirmed(7)">Order Modified Date</th>
                    <th>Action</th>
                  </tr>
                  <?php
                  $confirmedOrders = getOrdersByStatus('Confirmed', $conn);
                  if (count($confirmedOrders) > 0) {
                      foreach($confirmedOrders as $row) {
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
                  ?>
                </table>
              </table>
            </div>
            <div id="Rejected" class="tabcontent">
              <h3>Rejected</h3>
              <table id="rejectedTable">
                <table id="orderbookings_rejected">
                  <tr>
                    <th onclick="sortTableRejected(0)">Order ID</th>
                    <th onclick="sortTableRejected(1)">Order Type</th>
                    <th onclick="sortTableRejected(2)">Time</th>
                    <th onclick="sortTableRejected(3)">Email</th>
                    <th onclick="sortTableRejected(4)">Order Remark</th>
                    <th onclick="sortTableRejected(5)">Status</th>
                    <th onclick="sortTableRejected(6)">Order Created Date</th>
                    <th onclick="sortTableRejected(7)">Order Modified Date</th>
                    <th>Action</th>
                  </tr>
                  <?php
                  $rejectedOrders = getOrdersByStatus('Rejected', $conn);
                  if (count($rejectedOrders) > 0) {
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery.1.11.1.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script type="text/javascript" src="js/SmoothScroll.js"></script>
    <script type="text/javascript" src="js/jquery.isotope.js"></script>
    <script type="text/javascript" src="js/main.js"></script>
    <script>
      document.addEventListener('DOMContentLoaded', initPage);
    </script>
  </body>
</html>