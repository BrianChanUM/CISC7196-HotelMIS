<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/db_config.php';
$user = $_SESSION;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Password Reset</title>
    <!-- Add your CSS links here -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Password Reset</h2>
        <form id="editUserForm" method="POST" action="ResetCurrentPW.php">
            <table class="table">
                <tbody>
                    <tr>
                        <td>Name:</td>
                        <td><input type="text" class="form-control" id="name" value="<?php echo $user['username']; ?>" readonly></td>
                    </tr>

                    <tr>
                        <td>Password:</td>
                        <td>
                            <input type="password" class="form-control" id="password" name="password">
                            <small id="passwordHelp" class="form-text text-muted">Security Level: <span id="securityLevel">Weak</span></small>
                        </td>
                    </tr>
                    <tr>
                        <td>Confirm Password:</td>
                        <td><input type="password" class="form-control" id="confirmPassword" name="confirmPassword"></td>
                    </tr>           
                </tbody>
            </table>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <script>
    // Get the form and input elements
    var form = document.getElementById('editUserForm');
    var passwordInput = document.getElementById('password');
    var confirmPasswordInput = document.getElementById('confirmPassword');
    var securityLevelElement = document.getElementById('securityLevel');

    // Add event listener for password input
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
    // Add event listener for form submission
    form.addEventListener('submit', function(event) {
        event.preventDefault();

        var password = passwordInput.value;
        var confirmPassword = confirmPasswordInput.value;

        if (password !== confirmPassword) {
            alert('Passwords do not match!');
            return;
        }

        // Add your code to update the user data here
        alert('User data updated successfully!');
    });
	
	   // Submit the form
    form.submit();
});
    </script>
</body>
</html>

<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the POST parameters
    $username = $user['username'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Check if the passwords match
    if ($password !== $confirmPassword) {
        echo '<script>alert("Passwords do not match!");</script>';
        return;
    }

    // Hash the password
    //$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Connect to the database
    $db = new PDO('mysql:host=' . $db_config['host'] . ';dbname=' . $db_config['dbname'], $db_config['username'], $db_config['password']);

    // Check if the user exists
    $query = $db->prepare('SELECT * FROM user WHERE UserName = :username');
    $query->execute(['username' => $username]);
    $user = $query->fetch();

    if ($user) {
        // The user exists, update the password
        $query = $db->prepare('UPDATE user SET Password = :password, ModifiedDate = NOW()WHERE UserName = :username');
        $result = $query->execute(['password' => $password, 'username' => $username]);

        // Check if the update was successful
        if ($result) {
            echo '<script>alert("Your password has been updated Success"); window.location.href = "index.php";</script>';
        } else {
            echo '<script>alert("Error");</script>';
        }
    } else {
        // The user does not exist
        echo '<script>alert("User does not exist!");</script>';
    }
}
?>