<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    require_once __DIR__ . '/config/language.php';
    require_once __DIR__ . '/function/check_permission.php';
    requirePermission('admin_outlets', 'view', 'index.php');
    require_once __DIR__ . '/config/db_config.php';

    $conn = getDBConnection();

    $user = json_encode($_SESSION);

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
        header('Content-Type: application/json');
        
        if ($_POST['action'] == 'delete') {
            if (!checkPermission('admin_outlets', 'delete')) {
                echo json_encode(['success' => false, 'message' => 'Access denied. You do not have permission to delete.']);
                exit;
            }
            $outletName = $_POST['outletName'];
            $stmt = $conn->prepare("DELETE FROM hoteloutlet WHERE OutletName = ?");
            $stmt->bind_param("s", $outletName);
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Outlet deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error deleting outlet']);
            }
            $stmt->close();
            exit;
        }
        
        if ($_POST['action'] == 'update') {
            if (!checkPermission('admin_outlets', 'edit')) {
                echo json_encode(['success' => false, 'message' => 'Access denied. You do not have permission to edit.']);
                exit;
            }
            $oldOutletName = $_POST['oldOutletName'];
            $outletName = $_POST['outletName'];
            $outletSlogan = $_POST['outletSlogan'];
            $outletMenu = $_POST['outletMenu'];
            $openingHour = $_POST['openingHour'];
            $style = $_POST['style'];
            $status = $_POST['status'];
            
            $stmt = $conn->prepare("UPDATE hoteloutlet SET OutletName = ?, OutletSlogan = ?, OutletMenu = ?, `Opening Hour` = ?, Style = ?, Status = ? WHERE OutletName = ?");
            $stmt->bind_param("sssssis", $outletName, $outletSlogan, $outletMenu, $openingHour, $style, $status, $oldOutletName);
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Outlet updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error updating outlet']);
            }
            $stmt->close();
            exit;
        }
        
        if ($_POST['action'] == 'get') {
            $outletName = $_POST['outletName'];
            $stmt = $conn->prepare("SELECT * FROM hoteloutlet WHERE OutletName = ?");
            $stmt->bind_param("s", $outletName);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                echo json_encode(['success' => true, 'data' => $row]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Outlet not found']);
            }
            $stmt->close();
            exit;
        }
    }
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
		 <h2>Manage Outlet Table</h2> 
		<div class="tab">
				 <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search for bookings.."> 
			<button class="tablinks" onclick="openTab(event, 'All')">All</button>
              <button class="tablinks" onclick="openTab(event, 'TBC')">Active Outlet</button>
              <button class="tablinks" onclick="openTab(event, 'Confirmed')">Inactive Outlet</button>
            </div> 
          <div class="col-md-6">
           
			
         <!-- all status-->    
		<?php include(__DIR__ . '/layout/ManageoutletTable.php');?>
            	
         <!-- all active outlet status-->    
		<?php include(__DIR__ . '/layout/ManageoutletTableactive.php');?>

		 <!-- all inactive outlet status-->    
		<?php include(__DIR__ . '/layout/ManageoutletTableinactive.php');?>
			

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
          
          <div id="editModal" class="modal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5);">
            <div class="modal-content" style="background-color:#fefefe; margin:10% auto; padding:20px; border:1px solid #888; width:50%; max-width:500px;">
              <span class="close" onclick="closeEditModal()" style="float:right; font-size:28px; font-weight:bold; cursor:pointer;">&times;</span>
              <h3>Edit Outlet</h3>
              <form id="editForm">
                <input type="hidden" id="oldOutletName" name="oldOutletName">
                
                <label for="outletName">Outlet Name:</label>
                <input type="text" id="outletName" name="outletName" required style="width:100%; padding:8px; margin:5px 0;"><br>
                
                <label for="outletSlogan">Slogan:</label>
                <input type="text" id="outletSlogan" name="outletSlogan" style="width:100%; padding:8px; margin:5px 0;"><br>
                
                <label for="outletMenu">Menu:</label>
                <input type="text" id="outletMenu" name="outletMenu" style="width:100%; padding:8px; margin:5px 0;"><br>
                
                <label for="openingHour">Opening Hour:</label>
                <input type="text" id="openingHour" name="openingHour" style="width:100%; padding:8px; margin:5px 0;"><br>
                
                <label for="style">Style:</label>
                <input type="text" id="style" name="style" style="width:100%; padding:8px; margin:5px 0;"><br>
                
                <label for="status">Status:</label>
                <select id="status" name="status" style="width:100%; padding:8px; margin:5px 0;">
                  <option value="1">Active</option>
                  <option value="0">Inactive</option>
                </select><br><br>
                
                <button type="submit" class="action-btn edit-btn" style="background-color:#4CAF50;color:white;padding:8px 16px;border:none;cursor:pointer;">Update</button>
                <button type="button" onclick="closeEditModal()" class="action-btn delete-btn" style="background-color:#f44336;color:white;padding:8px 16px;border:none;cursor:pointer;">Cancel</button>
              </form>
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

function openModal(orderId, OutletName, places, eventType, contact, phone, email, status) {
    var modal = document.getElementById("myModal");
    var modalText = document.getElementById("modalText");
    var buttonContainer = document.getElementById("buttonContainer");
    modalText.innerHTML = "<b>Order Details</b><br>OutletName: " + orderId + "<br>Order Type: " + OutletName + "<br>OutletName: " + places + "<br>Email: " + eventType + "<br>Order Remark: " + contact + "<br>Last Status: " + phone + "<br>Order Created Date: " + email + "<br>Status: " + status;
    modalText.dataset.orderId = orderId;
    buttonContainer.style.display = "block";
    modal.style.display = "block";
}

function openOutletModal(outletName, slogan, menu, openingHour, status, style) {
    var modal = document.getElementById("myModal");
    var modalText = document.getElementById("modalText");
    var buttonContainer = document.getElementById("buttonContainer");
    var statusText = status == 1 ? "Active" : "Inactive";
    modalText.innerHTML = "<b>Outlet Details</b><br><br>" +
                          "<b>Name:</b> " + outletName + "<br><br>" +
                          "<b>Slogan:</b> " + slogan + "<br><br>" +
                          "<b>Menu:</b> " + menu + "<br><br>" +
                          "<b>Opening Hour:</b> " + openingHour + "<br><br>" +
                          "<b>Style:</b> " + style + "<br><br>" +
                          "<b>Status:</b> " + statusText;
    buttonContainer.style.display = "none";
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
          if (evt) {
            evt.currentTarget.className += " active";
          }
        }
		
		document.addEventListener('DOMContentLoaded', function() {
  openTab(null, 'All');
});

        function openEditModal(outletName) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "manageoutlet.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (this.readyState === 4 && this.status === 200) {
                    var response = JSON.parse(this.responseText);
                    if (response.success) {
                        document.getElementById("oldOutletName").value = outletName;
                        document.getElementById("outletName").value = response.data.OutletName;
                        document.getElementById("outletSlogan").value = response.data.OutletSlogan;
                        document.getElementById("outletMenu").value = response.data.OutletMenu;
                        document.getElementById("openingHour").value = response.data['Opening Hour'];
                        document.getElementById("style").value = response.data.Style;
                        document.getElementById("status").value = response.data.Status;
                        document.getElementById("editModal").style.display = "block";
                    } else {
                        alert(response.message);
                    }
                }
            };
            xhr.send("action=get&outletName=" + encodeURIComponent(outletName));
        }

        function closeEditModal() {
            document.getElementById("editModal").style.display = "none";
        }

        document.getElementById("editForm").addEventListener("submit", function(e) {
            e.preventDefault();
            var formData = new FormData();
            formData.append("action", "update");
            formData.append("oldOutletName", document.getElementById("oldOutletName").value);
            formData.append("outletName", document.getElementById("outletName").value);
            formData.append("outletSlogan", document.getElementById("outletSlogan").value);
            formData.append("outletMenu", document.getElementById("outletMenu").value);
            formData.append("openingHour", document.getElementById("openingHour").value);
            formData.append("style", document.getElementById("style").value);
            formData.append("status", document.getElementById("status").value);

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "manageoutlet.php", true);
            xhr.onreadystatechange = function() {
                if (this.readyState === 4 && this.status === 200) {
                    var response = JSON.parse(this.responseText);
                    alert(response.message);
                    if (response.success) {
                        closeEditModal();
                        location.reload();
                    }
                }
            };
            xhr.send(new URLSearchParams(formData));
        });

        function deleteOutlet(outletName) {
            if (confirm("Are you sure you want to delete this outlet?")) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "manageoutlet.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (this.readyState === 4 && this.status === 200) {
                        var response = JSON.parse(this.responseText);
                        alert(response.message);
                        if (response.success) {
                            location.reload();
                        }
                    }
                };
                xhr.send("action=delete&outletName=" + encodeURIComponent(outletName));
            }
        }

        function searchTable() {
            var input = document.getElementById("searchInput");
            var filter = input.value.toUpperCase();
            
            var activeTab = document.querySelector(".tablinks.active");
            var tableId = "outletTableAll";
            if (activeTab) {
                var tabName = activeTab.textContent.trim();
                if (tabName === "Active Outlet") {
                    tableId = "outletTableActive";
                } else if (tabName === "Inactive Outlet") {
                    tableId = "outletTableInactive";
                }
            }
            
            var table = document.getElementById(tableId);
            if (!table) {
                table = document.getElementById("outletTableAll");
            }
            
            var tr = table.getElementsByTagName("tr");

            for (var i = 1; i < tr.length; i++) {
                var td = tr[i].getElementsByTagName("td")[0];
                if (td) {
                    var txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }

        window.onclick = function(event) {
            var modal = document.getElementById("editModal");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
        
    </script>
  </body>
</html>