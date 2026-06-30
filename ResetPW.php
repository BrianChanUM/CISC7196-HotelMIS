<?php
require_once __DIR__ . '/config/db_config.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST["username"];
    $email = $_POST["email"];
    $new_password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    $conn = getDBConnection();

    // 使用PDO预处理语句查询用户
    $sql = "SELECT * FROM user WHERE UserName = ? AND Email LIKE ?";
    $stmt = $conn->prepare($sql);
    $emailPattern = "%$email%";
    $stmt->execute([$user, $emailPattern]);
    $result = $stmt->fetch();

    if ($result) {
        // User exists, proceed with password update
        if ($new_password == $confirm_password) {
            $sql = "UPDATE user SET Password = ?, ModifiedDate = NOW() WHERE UserName = ? AND Email LIKE ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$new_password, $user, $emailPattern]);
            
            if ($stmt->rowCount() > 0) {
                $message = "Password updated successfully";
            } else {
                $message = "Error updating password";
            }
        } else {
            $message = "Passwords do not match";
        }
    } else {
        $message = "Username or Email Not exist";
    }

    closeDBConnection($conn);
}
?>

<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <style>
    #password-strength-status { font-size: 12px; }
    .strong-password { color: green; }
    .medium-password { color: orange; }
    .weak-password { color: red; }
  </style>
</head>
<body>
    <div class="container">
         <h3>Password Reset (Forgot Password)</h3>
    <form id="editUserForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
      <table class="table">
        <tbody>
          <tr>
            <td>Name:</td>
            <td><input type="text" class="form-control" id="username" name="username" ></td>
          </tr>
          <tr>
            <td>Email address:</td>
            <td><input type="email" class="form-control" id="email" name="email" ></td>
          </tr>
          <tr>
            <td>Password:</td>
            <td>
              <input type="password" class="form-control" id="password" name="password" onkeyup="checkPasswordStrength();">
              <small id="passwordHelp" class="form-text text-muted">Security Level: <span id="securityLevel"> </span></small>
            </td>
          </tr>
          <tr>
            <td>Confirm Password:</td>
            <td><input type="password" class="form-control" id="confirmPassword" name="confirm_password"></td>
          </tr>
        </tbody>
      </table>
      <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
  </div>
  
    <?php if ($message): ?>
    <script>
      alert('<?php echo htmlspecialchars($message); ?>');
    </script>
  <?php endif; ?>
  
  <script>
    function checkPasswordStrength() {
      var password = document.getElementById("password").value;
      var status = document.getElementById("securityLevel");
      var strongRegex = new RegExp("^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})");
      var mediumRegex = new RegExp("^(((?=.*[a-z])(?=.*[A-Z]))|((?=.*[a-z])(?=.*[0-9]))|((?=.*[A-Z])(?=.*[0-9])))(?=.{6,})");
      if (strongRegex.test(password)) {
        status.className = "strong-password";
        status.innerHTML = "Strong";
      } else if (mediumRegex.test(password)) {
        status.className = "medium-password";
        status.innerHTML = "Medium";
      } else {
        status.className = "weak-password";
        status.innerHTML = "Weak";
      }
    }
  </script>
</body>
</html>