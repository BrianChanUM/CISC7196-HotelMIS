<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/db_config.php';

if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_SESSION['username'];
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirmPassword = isset($_POST['confirmPassword']) ? $_POST['confirmPassword'] : '';

    if (empty($password)) {
        $message = "Please enter a password";
    } elseif ($password !== $confirmPassword) {
        $message = "Passwords do not match!";
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters";
    } else {
        $conn = getDBConnection();

        $stmt = $conn->prepare("UPDATE user SET Password = ?, ModifiedDate = NOW() WHERE UserName = ?");
        $stmt->execute([$password, $username]);
        
        if ($stmt->rowCount() > 0) {
            $message = "Password updated successfully!";
            echo '<script>alert("Your password has been updated successfully!"); window.location.href = "profile.php";</script>';
            exit();
        } else {
            $message = "Error updating password";
        }
        
        closeDBConnection($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Password Reset - HotelMIS</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
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
                <a class="navbar-brand" href="index.php">Hotel Management System</a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <?php include(__DIR__ . '/layout/header.php');?>
                <ul class="nav navbar-nav navbar-right" id="navbar"></ul>
                <?php include(__DIR__ . '/layout/language_switcher.php');?>
                <?php include(__DIR__ . '/layout/navbar.php');?>
            </div>
        </div>
    </nav>

    <div class="container" style="margin-top: 100px;">
        <h2>Password Reset</h2>
        
        <?php if ($message): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>

        <form id="editUserForm" method="POST" action="ResetCurrentPW.php">
            <table class="table">
                <tbody>
                    <tr>
                        <td>Username:</td>
                        <td><input type="text" class="form-control" value="<?php echo htmlspecialchars($_SESSION['username']); ?>" readonly></td>
                    </tr>
                    <tr>
                        <td>New Password:</td>
                        <td>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <small id="passwordHelp" class="form-text text-muted">Security Level: <span id="securityLevel">Weak</span></small>
                        </td>
                    </tr>
                    <tr>
                        <td>Confirm Password:</td>
                        <td><input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required></td>
                    </tr>           
                </tbody>
            </table>
            <button type="submit" class="btn btn-primary">Update Password</button>
            <a href="profile.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <script>
    var passwordInput = document.getElementById('password');
    var securityLevelElement = document.getElementById('securityLevel');

    passwordInput.addEventListener('input', function(event) {
        var password = event.target.value;

        if (password.length > 8 && /[A-Z]/.test(password) && /[0-9]/.test(password) && /[^A-Za-z0-9]/.test(password)) {
            securityLevelElement.innerText = 'Strong';
            securityLevelElement.style.color = 'green';
        } else if (password.length > 8) {
            securityLevelElement.innerText = 'Medium';
            securityLevelElement.style.color = 'orange';
        } else {
            securityLevelElement.innerText = 'Weak';
            securityLevelElement.style.color = 'red';
        }
    });
    </script>
</body>
</html>