<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    require_once __DIR__ . '/config/session_check.php';
    require_once __DIR__ . '/config/language.php';
    require_once __DIR__ . '/config/db_config.php';
    
    if (!isset($_SESSION["username"]) || empty($_SESSION["username"])) {
        header("Location: login.php");
        exit();
    }
    
    $user = json_encode($_SESSION);
    $loggedInUsername = $_SESSION["username"];
    
    $conn = getDBConnection();
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <!-- Bootstrap -->
    <link rel="stylesheet" type="text/css"  href="css/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="css/ordertable.css">
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
            <div class="row">
           
	  
<!-- 	<button onclick="changePage(-1)">Previous</button>
<button onclick="changePage(1)">Next</button>
  <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search for bookings.."> -->


 
<?php
// 添加错误日志记录函数
function logError($message) {
    error_log("[OrderHistory Error] " . date('Y-m-d H:i:s') . " - " . $message);
}

// 获取用户角色信息
$userRole = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
$userEmail = isset($_SESSION['email']) ? $_SESSION['email'] : '';

try {
    // 根据用户角色构建查询
    if(true){
    // if ($userRole == 'guest' && !empty($userEmail)) {
        // 普通用户只能查看自己的订单
        $sql = "SELECT OrderID, OrderType, OrderCreatedDate, Email, ContactNo, OrderRemark, Status, PaymentStatus
                FROM `orderbookings`
                WHERE Email = ?
                ORDER BY `OrderID` DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$userEmail]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // else {
    //     // 管理员/经理可以查看所有订单
    //     $sql = "SELECT OrderID, OrderType, OrderCreatedDate, Email, ContactNo, OrderRemark, Status, PaymentStatus
    //             FROM `orderbookings`
    //             ORDER BY `OrderID` DESC";
    //     $result = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    // }
    
    // 获取订单类型统计
    if ($userRole == 'guest' && !empty($userEmail)) {
        $countSql = "SELECT OrderType, COUNT(*) AS order_count
                     FROM `orderbookings`
                     WHERE Email = ?
                     GROUP BY OrderType";
        $countStmt = $conn->prepare($countSql);
        $countStmt->execute([$userEmail]);
        $countResult = $countStmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $countSql = "SELECT OrderType, COUNT(*) AS order_count
                     FROM `orderbookings`
                     GROUP BY OrderType";
        $countResult = $conn->query($countSql)->fetchAll(PDO::FETCH_ASSOC);
    }
    
    $totalOrders = count($result);
    logError("Successfully fetched $totalOrders orders for user: " . ($userRole == 'guest' ? $userEmail : 'admin'));
    
} catch (PDOException $e) {
    $errorMsg = "Database query failed: " . $e->getMessage();
    logError($errorMsg);
    $result = [];
    $countResult = [];
    $totalOrders = 0;
}

closeDBConnection($conn);
?>

				
				
		<h4>Order History Details</h4>
		<table class="order-details" id="orderbookings">
  <tr>
            <th>Order ID</th>
			<th>Order Type</th>
			<th>Order Date</th>
            <th>Email</th>
			<th>Contact No</th>
			<th>Remark</th>
			<th>Status</th>
			<th>Payment Status</th>
        </tr>
        <?php
        if (!empty($result)) {
            foreach ($result as $row) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row["OrderID"] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($row["OrderType"] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($row["OrderCreatedDate"] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($row["Email"] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($row["ContactNo"] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($row["OrderRemark"] ?? "N/A") . "</td>";
                echo "<td>" . htmlspecialchars($row["Status"] ?? "N/A") . "</td>";
                echo "<td>" . htmlspecialchars($row["PaymentStatus"] ?? "N/A") . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='8'>No orders found.</td></tr>";
        }
        ?> 		
				

</table>



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

    <script src="js/owl.carousel.js"></script>

    <!-- Javascripts
    ================================================== -->
    <script type="text/javascript" src="js/main.js"></script>
  <script>

	  
var modal = document.getElementById("myModal");
var span = document.getElementsByClassName("close")[0];

function openModal(orderId, time, places, eventType, contact, phone, email, status) {
    var modalText = document.getElementById("modalText");
    modalText.innerHTML = "<b> Order Details</b>"+"</br>"+"<br>Order ID: " + orderId + "<br>Order Type: " + time + "<br>Time: " + places + "<br>Email: " + eventType + "<br>Order Remark: " + contact + "<br>Last Status: " + phone  ;
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

function closeModal() {
    modal.style.display = "none";
}




// 分页功能 - 添加错误处理
var table = document.getElementById('orderbookings');
var totalRows = 0;
var limit = 30; // Number of rows per page
var totalPages = 1;
var currentPage = 1;

// 检查表格是否存在
if (table) {
    totalRows = table.rows.length - 1; // 减去表头行
    totalPages = Math.max(1, Math.ceil(totalRows / limit));
} else {
    console.error('Order history table not found');
}

function paginate() {
    if (!table) return;
    
    for(var i = 1; i < table.rows.length; i++) {
        if(i < ((currentPage - 1) * limit) + 1 || i > (currentPage * limit)) {
            table.rows[i].style.display = 'none';
        } else {
            table.rows[i].style.display = '';
        }
    }
    // Update record number
    var startRecord = totalRows > 0 ? ((currentPage - 1) * limit) + 1 : 0;
    var endRecord = Math.min(currentPage * limit, totalRows);
    var recordNumberEl = document.getElementById('recordNumber');
    if (recordNumberEl) {
        recordNumberEl.innerText = 'Showing ' + startRecord + ' to ' + endRecord + ' of ' + totalRows;
    }
}

function changePage(delta) {
    if (totalRows === 0) return;
    
    currentPage += delta;
    // Make sure currentPage is within valid range
    currentPage = Math.max(1, Math.min(currentPage, totalPages));
    paginate();
}

// 只有在表格存在且有数据时才执行分页
if (table && totalRows > 0) {
    paginate();
}

		
</script>


  </body>
</html>


    <style>
        /* Add your custom CSS styles here */
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .dashboard th{
             background-color: #ffffbf;
        }
		.dashboard {
            font-weight: bold;
            color: #007bff;
        }
		
		    .order-details th {
            background-color: #4DC6FF;
        }

        /* Order Type Count table styles */
        .order-type-count th {
            background-color: #b3d9ff;
            color: #007bff;
        }
		
	  .widget {
             display: inline-block;
            background-color: #6c63ff;
            color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            margin-right: 20px;
			width: calc(25% - 20px); /* Distribute width evenly among 4 widgets */
        }

        /* Icon styles */
        .widget-icon {
            font-size: 24px;
            margin-right: 10px;
        }

        /* Widget titles */
        .widget h2 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        /* Widget content (counts) */
        .widget p {
            font-size: 18px;
            margin: 0;
        }
		
		 .bar {
        height: 20px;
        background-color: #ffffff;
        border-radius: 5px;
        margin-top: 10px;
		 .pie-chart {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        margin-top: 10px;
    }
    }
    </style>

