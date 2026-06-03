<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/db_config.php';
require_once __DIR__ . '/config/language.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usernameOrEmail = $_POST["username"];
    $password = $_POST["password"];

    $conn = getDBConnection();

    $stmt = $conn->prepare("SELECT * FROM user WHERE (UserName = ? OR Email = ?) AND Password = ?");
    $stmt->bind_param("sss", $usernameOrEmail, $usernameOrEmail, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION["username"] = $usernameOrEmail;
        $_SESSION["role"] = $row["Role"];
        $_SESSION["user_id"] = $row["UID"];
        
        $permissions = [];
        $permSql = "SELECT module, permission_type, is_allowed FROM user_permissions WHERE user_id = ?";
        $permStmt = $conn->prepare($permSql);
        $permStmt->bind_param("i", $row["UID"]);
        $permStmt->execute();
        $permResult = $permStmt->get_result();
        while ($permRow = $permResult->fetch_assoc()) {
            $key = $permRow['module'] . '_' . $permRow['permission_type'];
            $permissions[$key] = $permRow['is_allowed'] == 1;
        }
        $permStmt->close();
        $_SESSION["permissions"] = $permissions;
        
        echo "<script type='text/javascript'>alert('" . t('login_success') . " " . $_SESSION["username"] . "'); window.location.href = 'index.php';</script>";
    } else {
        echo "<script type='text/javascript'>alert('" . t('login_error') . "'); window.location.href = 'login.php';</script>";
    }

    $stmt->close();
    closeDBConnection($conn);
}
?>


<!DOCTYPE html>
<html lang="zh-CN">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=9">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo t('hotel_management_system'); ?> - <?php echo t('login'); ?></title>
    
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="img/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="img/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="img/apple-touch-icon-114x114.png">

    <link rel="stylesheet" type="text/css"  href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome/css/font-awesome.css">
    <link href="css/owl.carousel.css" rel="stylesheet" media="screen">
    <link href="css/owl.theme.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" type="text/css"  href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/responsive.css">

    <link href='http://fonts.googleapis.com/css?family=Lato:100,300,400,700,900,100italic,300italic,400italic,700italic,900italic' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,700,300,600,800,400' rel='stylesheet' type='text/css'>

    <script type="text/javascript" src="js/modernizr.custom.js"></script>

  </head>
  <body>
    <nav id="tf-menu" class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">切换导航</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php"><?php echo t('hotel_management_system'); ?></a>
            </div>
		
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right" id="navbar"></ul>
                <?php include(__DIR__ . '/layout/header.php');?>
                <?php include(__DIR__ . '/layout/language_switcher.php');?>
            </div>
        </div>
    </nav>

    <div id="tf-home" class="text-center">
        <a href="#tf-contact"></a>
    </div>
	
    <div id="tf-about">
        <div class="container">
            <div class="row">
                <div class="col-md-6"></div>
                <div class="col-md-6">
                    <div class="section-title">
                        <h3><?php echo t('login_title'); ?></h3>
                        <div class="clearfix"></div>
                    </div>
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="username"><?php echo t('username'); ?> / <?php echo t('contact_email'); ?></label>
                                    <input type="text" class="form-control" id="username" name="username" placeholder="请输入用户名或邮箱" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="password"><?php echo t('password'); ?></label>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="请输入密码" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary"><?php echo t('login_button'); ?></button>
                                <a href="Signup.php" class="btn btn-default">注册</a>
                                <a href="forgot_password.php" class="btn btn-link">忘记密码？</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <nav id="footer">
        <div class="container">
            <div class="pull-left fnav">
                <p>版权所有 &copy; 2023 | 由 <a href="#">CISC7196-酒店管理系统-2023年10月</a> 设计</p>
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

    <script src="js/jquery.1.11.1.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script type="text/javascript" src="js/owl.carousel.js"></script>
    <script type="text/javascript" src="js/SmoothScroll.js"></script>
    <script type="text/javascript" src="js/main.js"></script>

  </body>
</html>
