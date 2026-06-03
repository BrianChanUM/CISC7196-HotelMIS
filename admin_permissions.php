<?php
session_start();
require_once __DIR__ . '/config/db_config.php';
require_once __DIR__ . '/config/language.php';
require_once __DIR__ . '/function/check_permission.php';

$conn = getDBConnection();

if (!isAdmin()) {
    requireModulePermission('admin_permissions', 'index.php');
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_permissions'])) {
    $userId = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    
    if ($userId > 0) {
        $deleteStmt = $conn->prepare("DELETE FROM user_permissions WHERE user_id = ?");
        $deleteStmt->bind_param("i", $userId);
        $deleteStmt->execute();
        $deleteStmt->close();
        
        $modules = isset($_POST['modules']) ? $_POST['modules'] : [];
        
        $newPermissions = [];
        
        foreach ($modules as $module => $permissions) {
            foreach ($permissions as $permissionType => $value) {
                $isAllowed = isset($value) && $value == '1' ? 1 : 0;
                
                $stmt = $conn->prepare("INSERT INTO user_permissions (user_id, module, permission_type, is_allowed) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("issi", $userId, $module, $permissionType, $isAllowed);
                $stmt->execute();
                $stmt->close();
                
                $key = $module . '_' . $permissionType;
                $newPermissions[$key] = $isAllowed == 1;
            }
        }
        
        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $userId) {
            $_SESSION['permissions'] = $newPermissions;
        }
        
        $message = "Permissions saved successfully!";
    }
}

$users = [];
$sql = "SELECT UID, UserName, Email, Role FROM user WHERE Role != 'guest' ORDER BY UserName";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

$modulesList = [
    'hotel_booking' => 'Hotel Booking',
    'dining_booking' => 'Dining Booking',
    'limo_service' => 'Limo Service',
    'in_room_dining' => 'In Room Dining',
    'admin_orders' => 'Admin - Orders',
    'admin_job_assignment' => 'Admin - Job Assignment',
    'admin_auto_task' => 'Admin - Auto Task Setup',
    'admin_rooms' => 'Admin - Rooms',
    'admin_vehicles' => 'Admin - Vehicles',
    'admin_outlets' => 'Admin - Outlets',
    'admin_reports_fnb' => 'Admin - Reports FnB',
    'admin_reports_hotel' => 'Admin - Reports Hotel',
    'admin_reports_limo' => 'Admin - Reports Limo',
    'admin_permissions' => 'Admin - Permissions'
];

$permissionTypes = [
    'view' => 'View',
    'create' => 'Create',
    'edit' => 'Edit',
    'delete' => 'Delete'
];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Permissions - HotelMIS Admin</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .permissions-container {
            max-width: 1000px;
            margin: 80px auto 50px;
            padding: 20px;
        }
        .user-select {
            margin-bottom: 30px;
        }
        .permissions-table {
            background: #fff;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .permissions-table th {
            background: #333;
            color: #fff;
            padding: 12px;
            text-align: center;
        }
        .permissions-table td {
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        .permissions-table td:first-child {
            text-align: left;
            font-weight: bold;
        }
        .module-row:hover {
            background: #f9f9f9;
        }
        .checkbox-cell {
            vertical-align: middle;
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

    <div class="permissions-container">
        <div class="section-title">
            <h3>User Permissions Management</h3>
            <div class="clearfix"></div>
        </div>
        
        <?php if ($message): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>
        
        <div class="user-select">
            <label for="userSelect">Select User:</label>
            <select class="form-control" id="userSelect" name="user_id" onchange="loadUserPermissions()">
                <option value="">-- Select a User --</option>
                <?php foreach ($users as $user): ?>
                <option value="<?php echo $user['UID']; ?>">
                    <?php echo htmlspecialchars($user['UserName'] . ' (' . $user['Email'] . ') - ' . $user['Role']); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <form method="post" id="permissionsForm">
            <input type="hidden" name="user_id" id="selectedUserId" value="">
            
            <div class="permissions-table" id="permissionsTable" style="display: none;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Module</th>
                            <?php foreach ($permissionTypes as $type): ?>
                            <th><?php echo htmlspecialchars($type); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($modulesList as $moduleKey => $moduleName): ?>
                        <tr class="module-row">
                            <td><?php echo htmlspecialchars($moduleName); ?></td>
                            <?php foreach ($permissionTypes as $permKey => $permName): ?>
                            <td>
                                <input type="checkbox" 
                                       name="modules[<?php echo $moduleKey; ?>][<?php echo $permKey; ?>]" 
                                       value="1"
                                       class="perm-checkbox"
                                       data-module="<?php echo $moduleKey; ?>"
                                       data-permission="<?php echo $permKey; ?>">
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <button type="submit" name="save_permissions" class="btn btn-success btn-lg">Save Permissions</button>
            </div>
        </form>
    </div>

    <?php include(__DIR__ . '/layout/footer.php');?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery.1.11.1.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    
    <script>
    function loadUserPermissions() {
        var userId = document.getElementById('userSelect').value;
        var permissionsTable = document.getElementById('permissionsTable');
        var selectedUserIdInput = document.getElementById('selectedUserId');
        
        if (!userId) {
            permissionsTable.style.display = 'none';
            return;
        }
        
        selectedUserIdInput.value = userId;
        
        var checkboxes = document.querySelectorAll('.perm-checkbox');
        checkboxes.forEach(function(cb) {
            cb.checked = false;
        });
        
        permissionsTable.style.display = 'block';
        
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'function/get_user_permissions.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                try {
                    var data = JSON.parse(xhr.responseText);
                    if (data.permissions) {
                        data.permissions.forEach(function(perm) {
                            var cb = document.querySelector(
                                '.perm-checkbox[data-module="' + perm.module + '"][data-permission="' + perm.permission_type + '"]'
                            );
                            if (cb && perm.is_allowed == 1) {
                                cb.checked = true;
                            }
                        });
                    }
                } catch (e) {
                    console.error('Error parsing permissions:', e);
                }
            }
        };
        xhr.send('user_id=' + encodeURIComponent(userId));
    }
    </script>
</body>
</html>
