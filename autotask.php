<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/session_check.php';
require_once __DIR__ . '/config/language.php';
require_once __DIR__ . '/function/check_permission.php';
require_once __DIR__ . '/config/db_config.php';
requireModulePermission('admin_auto_task', 'index.php');

$conn = getDBConnection();

if (isset($_POST['action']) && isset($_POST['order_id'])) {
    $action = $_POST['action'];
    $orderId = intval($_POST['order_id']);

    $statusMap = [
        'confirm' => 'Confirmed',
        'reject' => 'Rejected',
        'pending' => 'TBC'
    ];

    if (isset($statusMap[$action])) {
        $newStatus = $statusMap[$action];
        $stmt = $conn->prepare("UPDATE orderbookings SET Status = ?, OrderModifiedDate = NOW() WHERE OrderID = ?");
        $stmt->execute([$newStatus, $orderId]);

        if ($stmt->rowCount() > 0) {
            echo "<script>alert('Order status updated successfully!'); window.location.href = 'autotask.php';</script>";
        } else {
            echo "<script>alert('Failed to update order status.'); window.location.href = 'autotask.php';</script>";
        }
        exit();
    }
}

if (isset($_POST['batch_action']) && isset($_POST['selected_orders']) && isset($_POST['batch_status'])) {
    $selectedOrders = $_POST['selected_orders'];
    $newStatus = $_POST['batch_status'];

    if (!empty($selectedOrders) && in_array($newStatus, ['Confirmed', 'Rejected', 'TBC'])) {
        $placeholders = implode(',', array_fill(0, count($selectedOrders), '?'));

        $sql = "UPDATE orderbookings SET Status = ?, OrderModifiedDate = NOW() WHERE OrderID IN ($placeholders)";
        $stmt = $conn->prepare($sql);

        $params = array_merge([$newStatus], $selectedOrders);
        $stmt->execute($params);

        $count = $stmt->rowCount();
        if ($count > 0) {
            echo "<script>alert('Successfully updated $count orders!'); window.location.href = 'autotask.php';</script>";
        } else {
            echo "<script>alert('Failed to update orders.'); window.location.href = 'autotask.php';</script>";
        }
        exit();
    }
}

if (isset($_POST['process_orders']) || isset($_POST['refresh_orders'])) {
    $redirectUrl = 'autotask.php';
    if (isset($_POST['advanceDays'])) {
        $redirectUrl .= '?advanceDays=' . intval($_POST['advanceDays']);
    }
    if (isset($_POST['process_orders'])) {
        $redirectUrl .= '&process_orders=' . $_POST['process_orders'];
    }
    header("Location: $redirectUrl");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['task_name'], $_POST['task_priority'], $_POST['task_date'], $_POST['task_time'])) {
    $task_name = trim($_POST['task_name']);
    $task_priority = trim($_POST['task_priority']);
    $task_date = trim($_POST['task_date']);
    $task_time = trim($_POST['task_time']);
    
    if (!empty($task_name) && !empty($task_date) && !empty($task_time)) {
        $stmt = $conn->prepare("INSERT INTO tasks (task_name, task_priority, task_date, task_time) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$task_name, $task_priority, $task_date, $task_time])) {
            echo "<script>alert('New task created successfully!'); window.location.href = 'autotask.php';</script>";
        }
    }
    exit();
}?><!DOCTYPE html>
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
                       <div class="col-md-12">
					<div class="section-title">
                            <h3>To Create hmis Auto Schedule Rules</h3>
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
    
    <div class="order-processing-section" style="margin-top: 30px; padding: 20px; background: #f9f9f9; border-radius: 5px;">
        <h4 style="text-align: center; margin-bottom: 20px; color: #333; font-size: 18px;">Order Processing Panel</h4>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <div class="form-group" style="display: flex; align-items: center; justify-content: center; gap: 15px; margin-bottom: 25px;">
                <label for="advanceDays" style="font-weight: 600; color: #555;">Advance Days Filter:</label>
                <input type="number" class="form-control" id="advanceDays" name="advanceDays" value="<?php echo isset($_REQUEST['advanceDays']) ? intval($_REQUEST['advanceDays']) : 7; ?>" min="1" max="365" style="width: 120px; padding: 8px 12px;">
                <span style="font-size: 14px; color: #666;">days in advance</span>
                <button type="submit" name="refresh_orders" value="refresh" class="btn btn-default" style="padding: 8px 20px;">
                    <i class="fa fa-refresh"></i> Refresh
                </button>
            </div>
            <div class="form-group" style="margin-top: 15px;">
                <label style="display: block; margin-bottom: 12px; font-weight: 600; color: #555; text-align: center;">Select Order Type to Process:</label>
                <div style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
                    <button type="submit" name="process_orders" value="hotel" class="btn btn-primary" style="padding: 6px 15px; font-size: 12px;">
                        <i class="fa fa-building" style="margin-right: 5px;"></i> Hotel Orders
                    </button>
                    <button type="submit" name="process_orders" value="dining" class="btn btn-success" style="padding: 6px 15px; font-size: 12px;">
                        <i class="fa fa-utensils" style="margin-right: 5px;"></i> Dining Orders
                    </button>
                    <button type="submit" name="process_orders" value="limo" class="btn btn-info" style="padding: 6px 15px; font-size: 12px;">
                        <i class="fa fa-car" style="margin-right: 5px;"></i> Limo Orders
                    </button>
                </div>
            </div>
        </form>
        
        <?php 
        $advanceDays = isset($_REQUEST['advanceDays']) ? intval($_REQUEST['advanceDays']) : 7;
        $orderType = isset($_REQUEST['process_orders']) ? $_REQUEST['process_orders'] : 'all';
        
        if ($orderType == 'all') {
            $sql = "SELECT * FROM orderbookings 
                    WHERE Status = 'TBC' 
                    AND Time > CURDATE() + INTERVAL ? DAY
                    AND Email IS NOT NULL 
                    AND Email != ''
                    AND Time IS NOT NULL
                    ORDER BY OrderType, Time ASC";
        } else {
            $orderTypeMap = ['hotel' => 'Hotel', 'dining' => 'Dining', 'limo' => 'Limo'];
            $sql = "SELECT * FROM orderbookings 
                    WHERE OrderType = ?
                    AND Status = 'TBC' 
                    AND Time > CURDATE() + INTERVAL ? DAY
                    AND Email IS NOT NULL 
                    AND Email != ''
                    AND Time IS NOT NULL
                    ORDER BY Time ASC";
        }
        
        $stmt = $conn->prepare($sql);
        if ($orderType == 'all') {
            $stmt->bind_param("i", $advanceDays);
        } else {
            $stmt->bind_param("si", $orderTypeMap[$orderType], $advanceDays);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            echo "<div style='margin-top: 25px;'>";
            echo "<h5 style='color: #333; margin-bottom: 15px; font-weight: 600; font-size: 16px; border-bottom: 2px solid #eee; padding-bottom: 10px;'>";
            echo "<i class='fa fa-list-alt' style='margin-right: 8px;'></i>Pending Orders to be Processed (more than <span style='color: #007bff;'>$advanceDays</span> days in advance)";
            echo "</h5>";
            
            echo "<form method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "' id='batchForm' style='margin-bottom: 15px;'>";
            echo "<div style='display: flex; align-items: center; gap: 15px; flex-wrap: wrap;'>";
            echo "<label style='font-weight: 600; color: #555;'>";
            echo "<input type='checkbox' id='selectAll' onclick='toggleSelectAll()' style='margin-right: 8px;'> Select All";
            echo "</label>";
            echo "<select name='batch_status' class='form-control' style='width: 150px; padding: 6px 12px; font-size: 13px;'>";
            echo "<option value=''>-- Select Status --</option>";
            echo "<option value='Confirmed'>Confirm</option>";
            echo "<option value='Rejected'>Reject</option>";
            echo "<option value='TBC'>Pending</option>";
            echo "</select>";
            echo "<button type='submit' name='batch_action' value='update' class='btn btn-primary btn-sm' onclick='return validateBatchAction()'>";
            echo "<i class='fa fa-batch' style='margin-right: 5px;'></i> Batch Update";
            echo "</button>";
            echo "</div>";
            
            echo "<div style='overflow-x: auto;'>";
            echo "<table class='table table-hover table-bordered' style='font-size: 13px; background: #fff; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);'>";
            echo "<thead style='background: #333; color: #fff;'>";
            echo "<tr><th style='padding: 12px; text-align: center; width: 50px;'>Select</th><th style='padding: 12px; text-align: center;'>Order ID</th><th style='padding: 12px; text-align: center;'>Type</th><th style='padding: 12px; text-align: center;'>Booking Time</th><th style='padding: 12px; text-align: center;'>Contact No</th><th style='padding: 12px; text-align: center;'>Email</th><th style='padding: 12px; text-align: center;'>Guests</th><th style='padding: 12px; text-align: center;'>Remarks</th><th style='padding: 12px; text-align: center;'>Status</th><th style='padding: 12px; text-align: center;'>Created Date</th><th style='padding: 12px; text-align: center;'>Action</th></tr>";
            echo "</thead><tbody>";
            while ($row = $result->fetch_assoc()) {
                $orderTypeClass = '';
                switch($row['OrderType']) {
                    case 'Hotel': $orderTypeClass = 'label label-primary'; break;
                    case 'Dining': $orderTypeClass = 'label label-success'; break;
                    case 'Limo': $orderTypeClass = 'label label-info'; break;
                    default: $orderTypeClass = 'label label-default';
                }
                echo "<tr style='border-bottom: 1px solid #eee;'>";
                echo "<td style='padding: 10px; text-align: center;'>";
                echo "<input type='checkbox' name='selected_orders[]' value='" . htmlspecialchars($row['OrderID']) . "' class='order-checkbox'>";
                echo "</td>";
                echo "<td style='padding: 10px; text-align: center; font-weight: 600; color: #333;'>" . htmlspecialchars($row['OrderID']) . "</td>";
                echo "<td style='padding: 10px; text-align: center;'><span class='" . $orderTypeClass . "'>" . htmlspecialchars($row['OrderType']) . "</span></td>";
                echo "<td style='padding: 10px; text-align: center;'>" . htmlspecialchars($row['Time']) . "</td>";
                echo "<td style='padding: 10px; text-align: center;'>" . htmlspecialchars($row['ContactNo']) . "</td>";
                echo "<td style='padding: 10px; text-align: center;'>" . htmlspecialchars($row['Email']) . "</td>";
                echo "<td style='padding: 10px; text-align: center;'>" . htmlspecialchars($row['NoofGuest']) . "</td>";
                echo "<td style='padding: 10px; text-align: left; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;' title='" . htmlspecialchars($row['OrderRemark']) . "'>" . htmlspecialchars($row['OrderRemark']) . "</td>";
                echo "<td style='padding: 10px; text-align: center;'><span class='label label-warning' style='padding: 5px 10px;'>" . htmlspecialchars($row['Status']) . "</span></td>";
                echo "<td style='padding: 10px; text-align: center;'>" . htmlspecialchars($row['OrderCreatedDate']) . "</td>";
                echo "<td style='padding: 10px; text-align: center;'>";
                echo "<button onclick='processSingleOrder(" . intval($row['OrderID']) . ", \"confirm\")' class='btn btn-success btn-xs' style='margin-right: 5px;'><i class='fa fa-check'></i> Confirm</button>";
                echo "<button onclick='processSingleOrder(" . intval($row['OrderID']) . ", \"reject\")' class='btn btn-danger btn-xs' style='margin-right: 5px;'><i class='fa fa-times'></i> Reject</button>";
                echo "<button onclick='processSingleOrder(" . intval($row['OrderID']) . ", \"pending\")' class='btn btn-warning btn-xs'><i class='fa fa-clock-o'></i> Pending</button>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
            echo "</div>";
            echo "<div style='margin-top: 10px; text-align: right; font-size: 13px; color: #666;'>";
            echo "<i class='fa fa-info-circle'></i> Total pending orders: " . $result->num_rows;
            echo "</div>";
            echo "</div>";
            echo "</form>";
            
            echo "<form id='singleActionForm' method='post' action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "' style='display: none;'>";
            echo "<input type='hidden' name='order_id' id='singleOrderId'>";
            echo "<input type='hidden' name='action' id='singleAction'>";
            echo "</form>";
        } else {
            echo "<div style='margin-top: 25px; padding: 15px; background: #e8f5e9; border-radius: 5px; color: #2e7d32; text-align: center;'>";
            echo "<i class='fa fa-check-circle' style='font-size: 24px; margin-bottom: 10px;'></i><br>";
            echo "<strong>No pending orders found</strong><br>";
            echo "<span style='font-size: 13px;'>No orders require processing more than " . $advanceDays . " days in advance.</span>";
            echo "</div>";
        }
        $stmt->close();
        ?>
    </div>
    
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
    
    <script type="text/javascript">
        function toggleSelectAll() {
            var checkboxes = document.getElementsByClassName('order-checkbox');
            var selectAll = document.getElementById('selectAll');
            
            for (var i = 0; i < checkboxes.length; i++) {
                checkboxes[i].checked = selectAll.checked;
            }
        }
        
        function validateBatchAction() {
            var checkboxes = document.getElementsByClassName('order-checkbox');
            var selectedCount = 0;
            
            for (var i = 0; i < checkboxes.length; i++) {
                if (checkboxes[i].checked) {
                    selectedCount++;
                }
            }
            
            var statusSelect = document.querySelector('select[name="batch_status"]');
            var selectedStatus = statusSelect.value;
            
            if (selectedCount === 0) {
                alert('Please select at least one order!');
                return false;
            }
            
            if (!selectedStatus) {
                alert('Please select a status to update!');
                return false;
            }
            
            if (confirm('Are you sure you want to update ' + selectedCount + ' order(s) to ' + selectedStatus + '?')) {
                return true;
            }
            
            return false;
        }
        
        function processSingleOrder(orderId, action) {
            var statusText = action === 'confirm' ? 'Confirmed' : action === 'reject' ? 'Rejected' : 'Pending';
            if (confirm('Are you sure you want to update this order to ' + statusText + '?')) {
                document.getElementById('singleOrderId').value = orderId;
                document.getElementById('singleAction').value = action;
                document.getElementById('singleActionForm').submit();
            }
        }
    </script>

  </body>
</html>
<?php closeDBConnection($conn); ?>
