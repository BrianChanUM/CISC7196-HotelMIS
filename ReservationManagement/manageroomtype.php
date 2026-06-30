<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/session_check.php';
require_once __DIR__ . '/config/language.php';
require_once __DIR__ . '/function/check_permission.php';
requirePermission('admin_rooms', 'view', 'index.php');
require_once __DIR__ . '/config/db_config.php';

$conn = getDBConnection();

$user = json_encode($_SESSION);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] == 'delete') {
        if (!checkPermission('admin_rooms', 'delete')) {
            echo json_encode(['success' => false, 'message' => 'Access denied. You do not have permission to delete.']);
            exit;
        }
        $roomType = $_POST['roomType'];
        $stmt = $conn->prepare("DELETE FROM hotelroomtype WHERE HotelRoomtype = ?");
        if ($stmt->execute([$roomType])) {
            echo json_encode(['success' => true, 'message' => 'Room type deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error deleting room type']);
        }
        exit;
    }
    
    if ($_POST['action'] == 'update') {
        if (!checkPermission('admin_rooms', 'edit')) {
            echo json_encode(['success' => false, 'message' => 'Access denied. You do not have permission to edit.']);
            exit;
        }
        $oldRoomType = $_POST['oldRoomType'];
        $roomType = $_POST['roomType'];
        $roomPrice = $_POST['roomPrice'];
        $dailyQuantity = $_POST['dailyQuantity'];
        $status = $_POST['status'];
        
        $stmt = $conn->prepare("UPDATE hotelroomtype SET HotelRoomtype = ?, HotelRoomPrice = ?, daily_quantity = ?, status = ? WHERE HotelRoomtype = ?");
        if ($stmt->execute([$roomType, $roomPrice, $dailyQuantity, $status, $oldRoomType])) {
            echo json_encode(['success' => true, 'message' => 'Room type updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating room type']);
        }
        exit;
    }
    
    if ($_POST['action'] == 'get') {
        $roomType = $_POST['roomType'];
        $stmt = $conn->prepare("SELECT * FROM hotelroomtype WHERE HotelRoomtype = ?");
        $stmt->execute([$roomType]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            echo json_encode(['success' => true, 'data' => $row]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Room type not found']);
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
    
    <style>
        .action-btn {
            padding: 5px 10px;
            margin: 2px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
        }
        .edit-btn {
            background-color: #4CAF50;
            color: white;
        }
        .delete-btn {
            background-color: #f44336;
            color: white;
        }
        .status-enabled {
            color: green;
            font-weight: bold;
        }
        .status-disabled {
            color: red;
            font-weight: bold;
        }
    </style>
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
            
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <?php include(__DIR__ . '/layout/header.php');?>
                <ul class="nav navbar-nav navbar-right" id="navbar"></ul>
                <?php include(__DIR__ . '/layout/language_switcher.php');?>
                <?php include(__DIR__ . '/layout/navbar.php');?>
            </div>
        </div>
    </nav>

    <div id="tf-home" class="text-center">
        <a href="#tf-contact"></a>
    </div>

    <div id="tf-about">
        <div class="container">
            <div class="row">
                <h2>Manage Room Types</h2>
                
                <div class="tab">
                    <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search for room types..">
                    <button class="tablinks" onclick="openTab(event, 'All')">All</button>
                    <button class="tablinks" onclick="openTab(event, 'Active')">Active</button>
                    <button class="tablinks" onclick="openTab(event, 'Inactive')">Inactive</button>
                </div>
                <div id="All" class="tabcontent" style="display:block">
                    <table id="roomTableAll" class="table table-bordered table-striped table-hover" style="font-size: 14px;">
                        <thead>
                            <tr>
                                <th>Room Type</th>
                                <th>Price</th>
                                <th>Daily Quantity</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $conn->query("SELECT * FROM hotelroomtype ORDER BY HotelRoomtype");
                            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            if (!empty($rows)) {
                                foreach($rows as $row) {
                                    $statusClass = $row['status'] ? 'status-enabled' : 'status-disabled';
                                    $statusText = $row['status'] ? 'Enabled' : 'Disabled';
                                    
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['HotelRoomtype']) . "</td>";
                                    echo "<td>$" . htmlspecialchars($row['HotelRoomPrice']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['daily_quantity']) . "</td>";
                                    echo "<td class='" . $statusClass . "'>" . $statusText . "</td>";
                                    echo "<td>";
                                    if (checkPermission('admin_rooms', 'edit')) {
                                        echo "<button class='action-btn edit-btn' onclick='openEditModal(\"" . htmlspecialchars($row['HotelRoomtype']) . "\")'>Edit</button>";
                                    }
                                    if (checkPermission('admin_rooms', 'delete')) {
                                        echo "<button class='action-btn delete-btn' onclick='deleteRoomType(\"" . htmlspecialchars($row['HotelRoomtype']) . "\")'>Delete</button>";
                                    }
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>No room types found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div id="Active" class="tabcontent" style="display:none">
                    <table id="roomTableActive" class="table table-bordered table-striped table-hover" style="font-size: 14px;">
                        <thead>
                            <tr>
                                <th>Room Type</th>
                                <th>Price</th>
                                <th>Daily Quantity</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $conn->prepare("SELECT * FROM hotelroomtype WHERE status = 1 ORDER BY HotelRoomtype");
                            $stmt->execute();
                            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            if (!empty($rows)) {
                                foreach($rows as $row) {
                                    $statusClass = $row['status'] ? 'status-enabled' : 'status-disabled';
                                    $statusText = $row['status'] ? 'Enabled' : 'Disabled';
                                    
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['HotelRoomtype']) . "</td>";
                                    echo "<td>$" . htmlspecialchars($row['HotelRoomPrice']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['daily_quantity']) . "</td>";
                                    echo "<td class='" . $statusClass . "'>" . $statusText . "</td>";
                                    echo "<td>";
                                    if (checkPermission('admin_rooms', 'edit')) {
                                        echo "<button class='action-btn edit-btn' onclick='openEditModal(\"" . htmlspecialchars($row['HotelRoomtype']) . "\")'>Edit</button>";
                                    }
                                    if (checkPermission('admin_rooms', 'delete')) {
                                        echo "<button class='action-btn delete-btn' onclick='deleteRoomType(\"" . htmlspecialchars($row['HotelRoomtype']) . "\")'>Delete</button>";
                                    }
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>No active room types found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div id="Inactive" class="tabcontent" style="display:none">
                    <table id="roomTableInactive" class="table table-bordered table-striped table-hover" style="font-size: 14px;">
                        <thead>
                            <tr>
                                <th>Room Type</th>
                                <th>Price</th>
                                <th>Daily Quantity</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $conn->prepare("SELECT * FROM hotelroomtype WHERE status = 0 ORDER BY HotelRoomtype");
                            $stmt->execute();
                            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            if (!empty($rows)) {
                                foreach($rows as $row) {
                                    $statusClass = $row['status'] ? 'status-enabled' : 'status-disabled';
                                    $statusText = $row['status'] ? 'Enabled' : 'Disabled';
                                    
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['HotelRoomtype']) . "</td>";
                                    echo "<td>$" . htmlspecialchars($row['HotelRoomPrice']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['daily_quantity']) . "</td>";
                                    echo "<td class='" . $statusClass . "'>" . $statusText . "</td>";
                                    echo "<td>";
                                    if (checkPermission('admin_rooms', 'edit')) {
                                        echo "<button class='action-btn edit-btn' onclick='openEditModal(\"" . htmlspecialchars($row['HotelRoomtype']) . "\")'>Edit</button>";
                                    }
                                    if (checkPermission('admin_rooms', 'delete')) {
                                        echo "<button class='action-btn delete-btn' onclick='deleteRoomType(\"" . htmlspecialchars($row['HotelRoomtype']) . "\")'>Delete</button>";
                                    }
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>No inactive room types found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="editModal" class="modal" style="display:none; position:fixed; z-index:1; left:0; top:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5);">
        <div class="modal-content" style="background-color:#fefefe; margin:15% auto; padding:20px; border:1px solid #888; width:50%; max-width:500px;">
            <span class="close" onclick="closeEditModal()" style="float:right; font-size:28px; font-weight:bold; cursor:pointer;">&times;</span>
            <h3>Edit Room Type</h3>
            <form id="editForm">
                <input type="hidden" id="oldRoomType" name="oldRoomType">
                
                <label for="roomType">Room Type:</label>
                <input type="text" id="roomType" name="roomType" required style="width:100%; padding:8px; margin:5px 0;"><br>
                
                <label for="roomPrice">Price:</label>
                <input type="number" id="roomPrice" name="roomPrice" required style="width:100%; padding:8px; margin:5px 0;"><br>
                
                <label for="dailyQuantity">Daily Quantity:</label>
                <input type="number" id="dailyQuantity" name="dailyQuantity" required style="width:100%; padding:8px; margin:5px 0;"><br>
                
                <label for="status">Status:</label>
                <select id="status" name="status" style="width:100%; padding:8px; margin:5px 0;">
                    <option value="1">Enabled</option>
                    <option value="0">Disabled</option>
                </select><br><br>
                
                <button type="submit" class="action-btn edit-btn">Update</button>
                <button type="button" onclick="closeEditModal()" class="action-btn delete-btn">Cancel</button>
            </form>
        </div>
    </div>

    <script>
    function searchTable() {
        var input = document.getElementById("searchInput");
        var filter = input.value.toUpperCase();
        
        var activeTab = document.querySelector(".tablinks.active");
        var tableId = "roomTableAll";
        if (activeTab) {
            var tabName = activeTab.textContent.trim();
            if (tabName === "Active") {
                tableId = "roomTableActive";
            } else if (tabName === "Inactive") {
                tableId = "roomTableInactive";
            }
        }
        
        var table = document.getElementById(tableId);
        if (!table) {
            table = document.getElementById("roomTableAll");
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

    function openTab(evt, tabName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(tabName).style.display = "block";
        if (evt) {
            evt.currentTarget.className += " active";
        }
    }

    function openEditModal(roomType) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "manageroomtype.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                var response = JSON.parse(this.responseText);
                if (response.success) {
                    document.getElementById("oldRoomType").value = roomType;
                    document.getElementById("roomType").value = response.data.HotelRoomtype;
                    document.getElementById("roomPrice").value = response.data.HotelRoomPrice;
                    document.getElementById("dailyQuantity").value = response.data.daily_quantity;
                    document.getElementById("status").value = response.data.status;
                    document.getElementById("editModal").style.display = "block";
                } else {
                    alert(response.message);
                }
            }
        };
        xhr.send("action=get&roomType=" + encodeURIComponent(roomType));
    }

    function closeEditModal() {
        document.getElementById("editModal").style.display = "none";
    }

    document.getElementById("editForm").addEventListener("submit", function(e) {
        e.preventDefault();
        var formData = new FormData();
        formData.append("action", "update");
        formData.append("oldRoomType", document.getElementById("oldRoomType").value);
        formData.append("roomType", document.getElementById("roomType").value);
        formData.append("roomPrice", document.getElementById("roomPrice").value);
        formData.append("dailyQuantity", document.getElementById("dailyQuantity").value);
        formData.append("status", document.getElementById("status").value);

        var xhr = new XMLHttpRequest();
        xhr.open("POST", "manageroomtype.php", true);
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

    function deleteRoomType(roomType) {
        if (confirm("Are you sure you want to delete this room type?")) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "manageroomtype.php", true);
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
            xhr.send("action=delete&roomType=" + encodeURIComponent(roomType));
        }
    }

    window.onclick = function(event) {
        var modal = document.getElementById("editModal");
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script type="text/javascript" src="js/bootstrap.js"></script>
</body>
</html>
<?php
closeDBConnection($conn);
?>
