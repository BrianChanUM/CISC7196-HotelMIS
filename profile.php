<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/session_check.php';
require_once __DIR__ . '/config/language.php';
require_once __DIR__ . '/config/db_config.php';

$conn = getDBConnection();

$query = $conn->prepare('SELECT * FROM user WHERE UserName = ? OR Email = ?');
$query->execute([$_SESSION['username'], $_SESSION['username']]);
$userprofile = $query->fetch();

if($userprofile) {
    $_SESSION['username'] = $userprofile['UserName'];
    $_SESSION['role'] = $userprofile['Role'];
    $userId = $userprofile['UID'];

    if($_SESSION['role'] != 'guest') {
        $query = $conn->prepare('SELECT * FROM user INNER JOIN userprofile ON user.UID = userprofile.UID WHERE UserName = ?');
        $query->execute([$_SESSION['username']]);
        $userprofile = $query->fetch();
    }

    // 获取用户偏好设置
    $prefQuery = $conn->prepare('SELECT preference_type, preference_value FROM user_preferences WHERE user_id = ?');
    $prefQuery->execute([$userId]);
    $preferences = $prefQuery->fetchAll(PDO::FETCH_KEY_PAIR);

    $hotelPrefValue = isset($preferences['hotel']) ? $preferences['hotel'] : '';
    $diningPrefValue = isset($preferences['dining']) ? $preferences['dining'] : '';
    $limoPrefValue = isset($preferences['limo']) ? $preferences['limo'] : '';
    $irdPrefValue = isset($preferences['ird']) ? $preferences['ird'] : '';
    $languagePref = isset($preferences['language']) ? $preferences['language'] : 'ENG';

    // 获取房间类型
    $roomStmt = $conn->prepare('SELECT HotelRoomtype FROM hotelroomtype');
    $roomStmt->execute();
    $roomTypes = $roomStmt->fetchAll(PDO::FETCH_COLUMN);

    // 获取车辆类型
    $vehicleStmt = $conn->prepare('SELECT VehicleType FROM hotelvehicletype');
    $vehicleStmt->execute();
    $vehicleTypes = $vehicleStmt->fetchAll(PDO::FETCH_COLUMN);

    // 获取餐厅类型 (Dining)
    $diningStmt = $conn->prepare('SELECT OutletName FROM hoteloutlet WHERE Style = "Dining" OR Style = "F&B" OR Style = "Restaurant"');
    $diningStmt->execute();
    $diningTypes = $diningStmt->fetchAll(PDO::FETCH_COLUMN);

    // 获取IRD服务类型
    $irdStmt = $conn->prepare('SELECT OutletName FROM hoteloutlet WHERE Style = "IRD" OR Style = "InRoomService"');
    $irdStmt->execute();
    $irdTypes = $irdStmt->fetchAll(PDO::FETCH_COLUMN);

    // 如果没有数据，使用默认选项
    if (empty($diningTypes)) {
        $diningTypes = ['Chinese Restaurant', 'Western Restaurant', 'Japanese Restaurant', 'Coffee Shop', 'In Room Dining services'];
    }
    if (empty($irdTypes)) {
        $irdTypes = ['In Room Dining services', 'Breakfast', 'Lunch', 'Dinner', 'Laundry Service', 'Spa Treatment'];
    }
} else {
    echo "Invalid username or email";
}

$user = json_encode($_SESSION);

closeDBConnection($conn);
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

    <!-- Bootstrap -->
    <link rel="stylesheet" type="text/css"  href="css/bootstrap.css">
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
    <div id="tf-home" class="text-right">

</div>
  
	
	<div id="tf-about" class="text-center">  <h2>User Profile</h2> 
<div id="tf-about" class="text-right">  
 <div class="container" >
    
        <form id="editUserForm" method="POST" action="ResetCurrentPW.php">
            <table class="table">
                <tbody>

                    <tr>
                        <td>Profile Name:</td>
                        <td><input type="text" class="form-control" id="name" value="<?php echo htmlspecialchars($userprofile['UserName']); ?>" readonly></td>
                    </tr>
                    <tr>
                        <td>Email:</td>
                        <td><input type="text" class="form-control" id="email" value="<?php echo htmlspecialchars($userprofile['Email']); ?>" readonly></td>
                    </tr>
                    <tr>
                        <td>Role:</td>
                        <td><input type="text" class="form-control" id="role" value="<?php echo htmlspecialchars($userprofile['Role']); ?>" readonly></td>
                    </tr>
                    <tr>
                        <td>Password:</td>
                        <td><input type="password" class="form-control" id="password" value="<?php echo htmlspecialchars($userprofile['Password']); ?>" readonly></td>
                    </tr>
                    <tr>
                        <td>Create Date:</td>
                        <td><input type="text" class="form-control" id="createDate" value="<?php echo htmlspecialchars($userprofile['CreateDate']); ?>" readonly></td>
                    </tr>
					<tr>
                        <td>Last Modified Date:</td>
                        <td><input type="text" class="form-control" id="ModifiedDate" value="<?php echo htmlspecialchars($userprofile['ModifiedDate']); ?>" readonly></td>
                    </tr>
					<tr>
                <?php if($userprofile['Role'] != 'guest'): ?>
                <tr>
                    <td>Department:</td>
                    <td><input type="text" class="form-control" id="department" value="<?php echo htmlspecialchars($userprofile['Department']); ?>" readonly></td>
                </tr>
                <tr>
                    <td>Level:</td>
                    <td><input type="text" class="form-control" id="level" value="<?php echo htmlspecialchars($userprofile['Level']); ?>" readonly></td>
                </tr>
                <tr>
                    <td>Salary Rate:</td>
                    <td><input type="text" class="form-control" id="salaryRate" value="<?php echo htmlspecialchars($userprofile['SalaryRate']); ?>" readonly></td>
                </tr>
                <tr>
                    <td>Onboard Date:</td>
                    <td><input type="text" class="form-control" id="onboardDate" value="<?php echo htmlspecialchars($userprofile['OnboardDate']); ?>" readonly></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

			<button type="button" id="changeButton" class="btn btn-primary">Edit Profile</button>
        </form>

        <!-- 偏好设置区域 -->
        <div class="preference-section" style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
            <h4 style="margin-bottom: 20px;"><i class="fas fa-cog"></i> Preference Settings</h4>
            <form id="preferenceForm">
                <table class="table">
                    <tbody>
                        <tr>
                            <td><strong>Hotel Preference:</strong></td>
                            <td>
                                <select id="hotelPref" name="hotel_pref" class="form-control" style="width: 100%;">
                                    <option value="">-- No Preference --</option>
                                    <?php foreach ($roomTypes as $room): ?>
                                        <option value="<?php echo htmlspecialchars($room); ?>" <?php echo $hotelPrefValue == $room ? 'selected' : ''; ?>><?php echo htmlspecialchars($room); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Dining Preference:</strong></td>
                            <td>
                                <select id="diningPref" name="dining_pref" class="form-control" style="width: 100%;">
                                    <option value="">-- No Preference --</option>
                                    <?php foreach ($diningTypes as $dining): ?>
                                        <option value="<?php echo htmlspecialchars($dining); ?>" <?php echo $diningPrefValue == $dining ? 'selected' : ''; ?>><?php echo htmlspecialchars($dining); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Limo Preference:</strong></td>
                            <td>
                                <select id="limoPref" name="limo_pref" class="form-control" style="width: 100%;">
                                    <option value="">-- No Preference --</option>
                                    <?php foreach ($vehicleTypes as $vehicle): ?>
                                        <option value="<?php echo htmlspecialchars($vehicle); ?>" <?php echo $limoPrefValue == $vehicle ? 'selected' : ''; ?>><?php echo htmlspecialchars($vehicle); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>IRD Preference:</strong></td>
                            <td>
                                <select id="irdPref" name="ird_pref" class="form-control" style="width: 100%;">
                                    <option value="">-- No Preference --</option>
                                    <?php foreach ($irdTypes as $ird): ?>
                                        <option value="<?php echo htmlspecialchars($ird); ?>" <?php echo $irdPrefValue == $ird ? 'selected' : ''; ?>><?php echo htmlspecialchars($ird); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Preferred Language:</strong></td>
                            <td>
                                <select id="languagePref" name="language_pref" class="form-control" style="width: 100%;">
                                    <option value="ENG" <?php echo $languagePref == 'ENG' ? 'selected' : ''; ?>>English (ENG)</option>
                                    <option value="TC" <?php echo $languagePref == 'TC' ? 'selected' : ''; ?>>繁體中文 (TC)</option>
                                    <option value="SC" <?php echo $languagePref == 'SC' ? 'selected' : ''; ?>>简体中文 (SC)</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" id="savePreferenceBtn" class="btn btn-success"><i class="fas fa-save"></i> Save Preferences</button>
                <span id="prefMessage" style="margin-left: 10px;"></span>
            </form>
        </div>
    </div> 

    </div>
 </div>

    <nav id="footer">
        <div class="container">
            <div class="pull-left fnav">
                  <p>ALL RIGHTS RESERVED. COPYRIGHT © 2023. Designed by BC<a href="#"> CISC7196-HotelMIS-2023OCT18</a> </p>
            </div>
            <div class="pull-right fnav">
                <ul class="footer-social">
                    <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                    <li><a href="#"><i class="fa fa-dribbble"></i></a></li>
                    <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                    <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                </ul>
            </div>
        </div>
    </nav>


    <!-- Add your JavaScript links here -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>


    <!-- Javascripts
    ================================================== -->
    <script type="text/javascript" src="js/main.js"></script>
     <script>
document.getElementById('changeButton').addEventListener('click', function() {
    // Open a new window with 'profile.php'
    window.open('profiledetails.php', '_blank', 'width=600,height=600');
});

// 保存偏好设置
document.getElementById('savePreferenceBtn').addEventListener('click', function() {
    var hotelPref = document.getElementById('hotelPref').value;
    var diningPref = document.getElementById('diningPref').value;
    var limoPref = document.getElementById('limoPref').value;
    var irdPref = document.getElementById('irdPref').value;
    var languagePref = document.getElementById('languagePref').value;
    var userId = <?php echo $userId; ?>;

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'function/save_preferences.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
            var response = JSON.parse(this.responseText);
            var messageSpan = document.getElementById('prefMessage');
            if (response.success) {
                messageSpan.innerHTML = '<span style="color: green;"><i class="fas fa-check-circle"></i> ' + response.message + '</span>';
            } else {
                messageSpan.innerHTML = '<span style="color: red;"><i class="fas fa-times-circle"></i> ' + response.message + '</span>';
            }
            setTimeout(function() {
                messageSpan.innerHTML = '';
            }, 3000);
        }
    };
    xhr.send('user_id=' + userId +
             '&hotel_pref=' + encodeURIComponent(hotelPref) +
             '&dining_pref=' + encodeURIComponent(diningPref) +
             '&limo_pref=' + encodeURIComponent(limoPref) +
             '&ird_pref=' + encodeURIComponent(irdPref) +
             '&language_pref=' + encodeURIComponent(languagePref));
});
    </script>
  </body>
</html>

<style>
.table {
    width: 100%;
    margin: 20px 0;
    border-collapse: collapse;
}

.table td, .table th {
    border: 1px solid #ddd;
    padding: 4px;
}

.table tr:nth-child(even) {
    background-color: #ffe6e6;

}

.table tr:nth-child(odd) {
    background-color: #e6f2ff;
}

.table tr:hover {
    background-color: #cccccc;
}

.table th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
    background-color: #4CAF50;
    color: white;
}

</style>