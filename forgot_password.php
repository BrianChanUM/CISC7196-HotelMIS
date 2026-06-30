<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/language.php';
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo t('forgot_password'); ?> - <?php echo t('hotel_management_system'); ?></title>
    
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    
    <link href='http://fonts.googleapis.com/css?family=Lato:100,300,400,700,900' rel='stylesheet' type='text/css'>
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
                <div class="col-md-3"></div>
                <div class="col-md-6">
                    <div class="section-title">
                        <h3><?php echo t('forgot_password'); ?></h3>
                        <div class="clearfix"></div>
                    </div>
                    
                    <?php
                    require_once __DIR__ . '/config/db_config.php';
                    $conn = getDBConnection();
                    
                    $message = '';
                    $error = '';
                    
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        $email = $_POST['email'] ?? '';
                        
                        if (empty($email)) {
                            $error = 'Please enter email address';
                        } else {
                            $stmt = $conn->prepare("SELECT UID, UserName, Email FROM user WHERE Email = ?");
                            $stmt->execute([$email]);
                            $user = $stmt->fetch();
                            
                            if ($user) {
                                $message = 'Password reset link has been sent to your email: ' . htmlspecialchars($email);
                            } else {
                                $error = 'This email is not registered';
                            }
                        }
                    }
                    closeDBConnection($conn);
                    ?>
                    
                    <?php if ($message): ?>
                    <div class="alert alert-success">
                        <?php echo $message; ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                    <?php endif; ?>
                    
                    <form method="post" action="">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="email"><?php echo t('contact_email'); ?></label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Please enter your registered email address" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Send Reset Link</button>
                                <a href="login.php" class="btn btn-default">Back to Login</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
</body>
</html>
