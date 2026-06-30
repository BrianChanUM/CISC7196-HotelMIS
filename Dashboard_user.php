<?php
require_once __DIR__ . '/config/session_check.php';
require_once __DIR__ . '/config/db_config.php';
require_once __DIR__ . '/config/language.php';
require_once __DIR__ . '/function/check_permission.php';
requireModulePermission('admin_dashboard', 'index.php');

$conn = getDBConnection();

$message = '';
$messageType = 'success';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action == 'update_user') {
            $uid = intval($_POST['uid']);
            $userName = $_POST['user_name'];
            $email = $_POST['email'];
            $role = $_POST['role'];
            $inHouseStatus = isset($_POST['in_house_status']) ? 1 : 0;
            $checkinDate = $_POST['checkin_date'] ?: NULL;
            $checkoutDate = $_POST['checkout_date'] ?: NULL;
            
            $updateUserStmt = $conn->prepare("
                UPDATE user 
                SET UserName = ?, Email = ?, Role = ?, in_house_status = ?, 
                    checkin_date = ?, checkout_date = ?, ModifiedDate = NOW() 
                WHERE UID = ?
            ");
            $updateUserStmt->execute([$userName, $email, $role, $inHouseStatus, $checkinDate, $checkoutDate, $uid]);
            
            $department = $_POST['department'];
            $level = intval($_POST['level']);
            $salaryRate = intval($_POST['salary_rate']);
            $onboardDate = $_POST['onboard_date'];
            
            $checkProfileStmt = $conn->prepare("SELECT COUNT(*) FROM userprofile WHERE UID = ?");
            $checkProfileStmt->execute([$uid]);
            $profileExists = $checkProfileStmt->fetchColumn();
            
            if ($profileExists > 0) {
                $updateProfileStmt = $conn->prepare("
                    UPDATE userprofile 
                    SET Department = ?, Level = ?, SalaryRate = ?, OnboardDate = ?, ModifiedDate = NOW() 
                    WHERE UID = ?
                ");
                $updateProfileStmt->execute([$department, $level, $salaryRate, $onboardDate, $uid]);
            } else {
                $insertProfileStmt = $conn->prepare("
                    INSERT INTO userprofile (UID, Department, Level, SalaryRate, OnboardDate) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                $insertProfileStmt->execute([$uid, $department, $level, $salaryRate, $onboardDate]);
            }
            
            $message = "User profile updated successfully!";
        } elseif ($action == 'update_status') {
            $uid = intval($_POST['uid']);
            $status = intval($_POST['status']);
            
            $updateStmt = $conn->prepare("
                UPDATE user 
                SET status = ?, login_attempts = 0, lock_time = NULL, ModifiedDate = NOW() 
                WHERE UID = ?
            ");
            $updateStmt->execute([$status, $uid]);
            
            $message = $status == 0 ? "User unlocked" : "User locked";
        } elseif ($action == 'update_password') {
            $uid = intval($_POST['uid']);
            $newPassword = $_POST['new_password'];
            
            if (!empty($newPassword)) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $updateStmt = $conn->prepare("UPDATE user SET Password = ?, ModifiedDate = NOW() WHERE UID = ?");
                $updateStmt->execute([$hashedPassword, $uid]);
                $message = "Password updated successfully!";
            } else {
                $message = "Password cannot be empty!";
                $messageType = 'error';
            }
        }
    }
}

$sql = "SELECT u.*, up.Department, up.Level, up.SalaryRate, up.OnboardDate 
        FROM user u 
        LEFT JOIN userprofile up ON u.UID = up.UID 
        ORDER BY u.UID";
$result = $conn->query($sql);
$users = $result->fetchAll(PDO::FETCH_ASSOC);

closeDBConnection($conn);
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=9">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo t('hotel_management_system'); ?> - User Management</title>
    
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome/css/font-awesome.css">
    <link href="css/owl.carousel.css" rel="stylesheet" media="screen">
    <link href="css/owl.theme.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/responsive.css">

    <style>
        .styled-table {
            border-collapse: collapse;
            margin: 25px 0;
            font-size: 0.9em;
            font-family: sans-serif;
            min-width: 100%;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
        }
        .styled-table thead tr {
            background-color: #009879;
            color: #ffffff;
            text-align: left;
        }
        .styled-table th,
        .styled-table td {
            padding: 12px 15px;
        }
        .styled-table tbody tr {
            border-bottom: 1px solid #dddddd;
        }
        .styled-table tbody tr:nth-of-type(even) {
            background-color: #f3f3f3;
        }
        .styled-table tbody tr:last-of-type {
            border-bottom: 2px solid #009879;
        }
        .btn-action {
            margin-right: 5px;
        }
        .modal-body {
            max-height: 600px;
            overflow-y: auto;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .alert {
            margin-top: 20px;
        }
        .status-active {
            color: green;
            font-weight: bold;
        }
        .status-locked {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <nav id="tf-menu" class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
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

    <div class="container">
        <div class="section-title">
            <h3>User Management</h3>
            <p>Admin can view and modify all user profiles</p>
            <div class="clearfix"></div>
        </div>

        <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType == 'error' ? 'danger' : 'success'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>

        <table class="styled-table">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Create Date</th>
                    <th>Login Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['UID']); ?></td>
                    <td><?php echo htmlspecialchars($user['UserName']); ?></td>
                    <td><?php echo htmlspecialchars($user['Email']); ?></td>
                    <td><?php echo htmlspecialchars($user['Role']); ?></td>
                    <td><?php echo htmlspecialchars($user['CreateDate']); ?></td>
                    <td>
                        <span class="<?php echo ($user['status'] ?? 0) == 0 ? 'status-active' : 'status-locked'; ?>">
                            <?php echo ($user['status'] ?? 0) == 0 ? 'Active' : 'Locked'; ?>
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-success btn-sm btn-action" onclick="showPreferences(<?php echo $user['UID']; ?>)">
                            <i class="fa fa-info-circle"></i> Preferences
                        </button>
                        <button class="btn btn-info btn-sm btn-action" onclick="editUser(<?php echo $user['UID']; ?>)">
                            <i class="fa fa-edit"></i> Edit Profile
                        </button>
                        <button class="btn btn-warning btn-sm btn-action" onclick="changeStatus(<?php echo $user['UID']; ?>, <?php echo ($user['status'] ?? 0) == 0 ? 1 : 0; ?>)">
                            <i class="fa fa-lock"></i> <?php echo ($user['status'] ?? 0) == 0 ? 'Lock' : 'Unlock'; ?>
                        </button>
                        <button class="btn btn-primary btn-sm btn-action" onclick="changePassword(<?php echo $user['UID']; ?>)">
                            <i class="fa fa-key"></i> Change Password
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Edit User Profile Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="editUserModalLabel">Edit User Profile</h4>
                </div>
                <div class="modal-body">
                    <form id="editUserForm" method="post">
                        <input type="hidden" name="action" value="update_user">
                        <input type="hidden" name="uid" id="edit_uid">
                        
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" class="form-control" name="user_name" id="edit_user_name" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" name="email" id="edit_email" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Role</label>
                            <select class="form-control" name="role" id="edit_role">
                                <option value="admin">Admin</option>
                                <option value="staff">Staff</option>
                                <option value="guest">Guest</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Department</label>
                            <input type="text" class="form-control" name="department" id="edit_department">
                        </div>
                        
                        <div class="form-group">
                            <label>Level</label>
                            <input type="number" class="form-control" name="level" id="edit_level" min="0">
                        </div>
                        
                        <div class="form-group">
                            <label>Salary Rate</label>
                            <input type="number" class="form-control" name="salary_rate" id="edit_salary_rate" min="0">
                        </div>
                        
                        <div class="form-group">
                            <label>Onboard Date</label>
                            <input type="date" class="form-control" name="onboard_date" id="edit_onboard_date">
                        </div>
                        
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="in_house_status" id="edit_in_house_status">
                                In-house Status
                            </label>
                        </div>
                        
                        <div class="form-group">
                            <label>Check-in Date</label>
                            <input type="date" class="form-control" name="checkin_date" id="edit_checkin_date">
                        </div>
                        
                        <div class="form-group">
                            <label>Check-out Date</label>
                            <input type="date" class="form-control" name="checkout_date" id="edit_checkout_date">
                        </div>
                        
                        <button type="submit" class="btn btn-success">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="changePasswordModalLabel">Change Password</h4>
                </div>
                <div class="modal-body">
                    <form id="changePasswordForm" method="post">
                        <input type="hidden" name="action" value="update_password">
                        <input type="hidden" name="uid" id="password_uid">
                        
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" class="form-control" name="new_password" placeholder="Please enter new password" required>
                        </div>
                        
                        <button type="submit" class="btn btn-success">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Preferences Modal -->
    <div class="modal fade" id="preferencesModal" tabindex="-1" role="dialog" aria-labelledby="preferencesModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="preferencesModalLabel">User Preferences</h4>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Preference Type</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody id="preferencesTableBody">
                            <tr><td colspan="2" class="text-center">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php include(__DIR__ . '/layout/footer.php');?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery.1.11.1.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script type="text/javascript" src="js/SmoothScroll.js"></script>
    <script type="text/javascript" src="js/main.js"></script>

    <script>
    var usersData = <?php echo json_encode($users); ?>;

    function showPreferences(uid) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'function/get_user_preferences.php?user_id=' + uid, true);
        xhr.onreadystatechange = function() {
            if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                var response = JSON.parse(this.responseText);
                var tbody = document.getElementById('preferencesTableBody');

                if (response.success && response.preferences.length > 0) {
                    var html = '';
                    var typeLabels = {
                        'hotel': 'Hotel Preference',
                        'dining': 'Dining Preference',
                        'limo': 'Limo Preference',
                        'ird': 'IRD Preference',
                        'language': 'Preferred Language'
                    };
                    var languageLabels = {
                        'TC': '繁體中文',
                        'SC': '简体中文',
                        'ENG': 'English'
                    };

                    response.preferences.forEach(function(pref) {
                        var label = typeLabels[pref.preference_type] || pref.preference_type;
                        var value = pref.preference_value;
                        if (pref.preference_type === 'language' && languageLabels[value]) {
                            value = languageLabels[value] + ' (' + value + ')';
                        }
                        if (!value) {
                            value = 'No Preference';
                        }
                        html += '<tr><td><strong>' + label + '</strong></td><td>' + value + '</td></tr>';
                    });
                    tbody.innerHTML = html;
                } else {
                    tbody.innerHTML = '<tr><td colspan="2" class="text-center">No preferences set</td></tr>';
                }
            }
        };
        xhr.send();
        $('#preferencesModal').modal('show');
    }

    function editUser(uid) {
        var user = usersData.find(function(u) { return u.UID == uid; });
        if (user) {
            document.getElementById('edit_uid').value = user.UID;
            document.getElementById('edit_user_name').value = user.UserName;
            document.getElementById('edit_email').value = user.Email;
            document.getElementById('edit_role').value = user.Role;
            document.getElementById('edit_department').value = user.Department || '';
            document.getElementById('edit_level').value = user.Level || '';
            document.getElementById('edit_salary_rate').value = user.SalaryRate || '';
            document.getElementById('edit_onboard_date').value = user.OnboardDate || '';
            document.getElementById('edit_in_house_status').checked = user.in_house_status == 1;
            document.getElementById('edit_checkin_date').value = user.checkin_date || '';
            document.getElementById('edit_checkout_date').value = user.checkout_date || '';
            
            $('#editUserModal').modal('show');
        }
    }

    function changeStatus(uid, status) {
        var confirmText = status == 1 ? 'Are you sure you want to lock this user?' : 'Are you sure you want to unlock this user?';
        if (confirm(confirmText)) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.style.display = 'none';
            
            var actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'update_status';
            
            var uidInput = document.createElement('input');
            uidInput.type = 'hidden';
            uidInput.name = 'uid';
            uidInput.value = uid;
            
            var statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'status';
            statusInput.value = status;
            
            form.appendChild(actionInput);
            form.appendChild(uidInput);
            form.appendChild(statusInput);
            
            document.body.appendChild(form);
            form.submit();
        }
    }

    function changePassword(uid) {
        document.getElementById('password_uid').value = uid;
        $('#changePasswordModal').modal('show');
    }
    </script>
</body>
</html>