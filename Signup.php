<?php
require_once __DIR__ . '/config/db_config.php';
require_once __DIR__ . '/config/language.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $loginID = $_POST['adminEmployeeID'];
    $email = $_POST['adminEmail'];
    $pass = $_POST['adminPassword'];

    $conn = getDBConnection();

    $stmt = $conn->prepare("INSERT INTO user (UserName, Email, Password, Role) VALUES (?, ?, ?, 'User')");
    $stmt->bind_param("sss", $loginID, $email, $pass);

    if ($stmt->execute() === TRUE) {
        echo "<script type='text/javascript'>alert('" . t('signup_success') . "'); window.location.href = 'login.php';</script>";
    } else {
        echo "<script type='text/javascript'>alert('" . t('signup_error') . "'); window.location.href = 'Signup.php';</script>";
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
    <title><?php echo t('hotel_management_system'); ?> - <?php echo t('signup'); ?></title>
    
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
                <div class="col-md-6">
                    <div id="slideshow-wrap">
                        <input type="radio" id="button-1" name="controls" checked="checked"/>
                        <label for="button-1"></label>
                        <input type="radio" id="button-2" name="controls"/>
                        <label for="button-2"></label>
                        <input type="radio" id="button-3" name="controls"/>
                        <label for="button-3"></label>
                        <input type="radio" id="button-4" name="controls"/>
                        <label for="button-4"></label>
                        <input type="radio" id="button-5" name="controls"/>
                        <label for="button-5"></label>
                        
                        <div id="slideshow-inner">
                            <ul>
                                <li id="slide1">
                                    <img src="images/1.jpg" />
                                    <div class="description">
                                        <input type="checkbox" id="show-description-1"/>
                                        <label for="show-description-1" class="show-description-label">I</label>
                                        <div class="description-text">
                                            <h2>优雅一瞥</h2>
                                            <p>我们提供最舒适的宁静与安心</p>
                                        </div>
                                    </div>
                                </li>
                                <li id="slide2">
                                    <img src="images/2.jpg" />
                                    <div class="description">
                                        <input type="checkbox" id="show-description-2"/>
                                        <label for="show-description-2" class="show-description-label">1</label>
                                        <div class="description-text">
                                            <h2>豪华客房</h2>
                                            <p>这些房间专为您的舒适体验而设计</p>
                                        </div>
                                    </div>
                                </li>
                                <li id="slide3">
                                    <img src="images/3.jpg" />
                                    <div class="description">
                                        <input type="checkbox" id="show-description-3"/>
                                        <label for="show-description-3" class="show-description-label">2</label>
                                        <div class="description-text">
                                            <h2>贵宾房</h2>
                                            <p>这些房间体现您的品味与身份</p>
                                        </div>
                                    </div>
                                </li>
                                <li id="slide4">
                                    <img src="images/4.jpg" />
                                    <div class="description">
                                        <input type="checkbox" id="show-description-4"/>
                                        <label for="show-description-4" class="show-description-label">3</label>
                                        <div class="description-text">
                                            <h2>精品客房</h2>
                                            <p>我们提供各种价位的房间让您享受美好时光</p>
                                        </div>
                                    </div>
                                </li>
                                <li id="slide5">
                                    <img src="images/5.jpg" />
                                    <div class="description">
                                        <input type="checkbox" id="show-description-5"/>
                                        <label for="show-description-5" class="show-description-label">4</label>
                                        <div class="description-text">
                                            <h2>豪华套房</h2>
                                            <p>我们按国际标准设计房间，为客户提供优质服务</p>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="section-title">
                        <h3><?php echo t('signup_title'); ?></h3>
                        <div class="clearfix"></div>
                    </div>
                    
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="adminEmployeeID">登录ID</label>
                                    <input type="text" class="form-control" id="adminEmployeeID" name="adminEmployeeID" 
                                    placeholder="请输入登录ID" 
                                    pattern="[A-Za-z0-9]{6,12}" 
                                    title="登录ID必须为6-12位字母或数字" 
                                    required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="adminEmail">邮箱地址</label>
                                    <input type="email" class="form-control" id="adminEmail" name="adminEmail" placeholder="请输入邮箱" pattern="[a-z0-9._%+-]+@MIS\.com$" title="邮箱地址格式不正确" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="adminPassword">密码</label>
                                    <input type="password" class="form-control" id="adminPassword" name="adminPassword" placeholder="请输入密码" required oninput="checkPasswordSecurity(this)">
                                    <div id="passwordStrengthBar" style="height: 8px;"></div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="adminPasswordConfirm">确认密码</label>
                                    <input type="password" class="form-control" id="adminPasswordConfirm" name="adminPasswordConfirm" placeholder="请再次输入密码" required oninput="checkPasswordMatch(this)">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" class="btn tf-btn btn-success">创建账户</button>
                                </div>
                            </div>
                        </div>

                        <div id="successMessage" style="display: none;">
                            <p>账户创建成功！</p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include(__DIR__ . '/layout/footer.php');?>

    <script src="js/jquery.1.11.1.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script type="text/javascript" src="js/SmoothScroll.js"></script>
    <script type="text/javascript" src="js/jquery.isotope.js"></script>
    <script src="js/owl.carousel.js"></script>
    <script type="text/javascript" src="js/main.js"></script>

    <script>
        function checkPasswordSecurity(input) {
            var password = input.value;
            var strengthBar = document.getElementById('passwordStrengthBar');
            
            if (password.length < 8) {
                input.setCustomValidity("密码必须至少8个字符。");
                strengthBar.style.width = '10%';
                strengthBar.style.backgroundColor = 'red';
            } 
            else if (!/[A-Z]/.test(password) || !/[a-z]/.test(password) || !/[\W]/.test(password)) {
                input.setCustomValidity("密码必须包含至少一个大写字母、一个小写字母和一个符号。");
                strengthBar.style.width = '15%';
                strengthBar.style.backgroundColor = 'orange';
            } 
            else {
                input.setCustomValidity("");
                strengthBar.style.width = '30%';
                strengthBar.style.backgroundColor = 'green';
            }
        }

        function checkPasswordMatch(input) {
            var password = document.getElementById('adminPassword').value;
            var confirmPassword = input.value;
            if (password !== confirmPassword) {
                input.setCustomValidity("两次输入的密码不一致。");
            } else {
                input.setCustomValidity("");
            }
        }
    </script>
  </body>
</html>
