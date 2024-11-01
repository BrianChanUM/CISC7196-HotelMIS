<?php
session_start();
$user = $_SESSION;

// Connect to the database 'localhost', 'root', '', 'HMIS'
$db = new PDO('mysql:host=localhost;dbname=HMIS', 'root', '');

// Check if the user exists
$query = $db->prepare('SELECT * FROM user WHERE UserName = :username');
$query->execute(['username' => $user['username']]);
$user = $query->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the POST parameters
    $username = $user['UserName'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Check if the passwords match
    if ($password !== $confirmPassword) {
        echo '<script>alert("Passwords do not match!");</script>';
        return;
    }

    // The user exists, update the email, password, and modified date
    $query = $db->prepare('UPDATE user SET Email = :email, Password = :password, ModifiedDate = NOW() WHERE UserName = :username');
    $result = $query->execute(['email' => $email, 'password' => $password, 'username' => $username]);

    // Check if the update was successful
    if ($result) {
        echo '<script>alert("Your profile has been updated successfully"); window.close();</script>';
    } else {
        echo '<script>alert("Error updating profile");</script>';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Profile</title>
    <!-- Add your CSS links here -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>User Profile</h2>
        <form id="editUserForm" method="POST" action="profiledetails.php">
            <table class="table">
                <tbody>
                    <tr>
                        <td>Profile Name:</td>
                        <td><input type="text" class="form-control" id="name" name="name" value="<?php echo $user['UserName']; ?>" readonly></td>
                    </tr>
                    <tr>
                        <td>Email:</td>
                        <td><input type="text" class="form-control" id="email" name="email" value="<?php echo $user['Email']; ?>" ></td>
                    </tr>
                    <tr>
                        <td>Role:</td>
                        <td><input type="text" class="form-control" id="role" name="role" value="<?php echo $user['Role']; ?>" readonly></td>
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

    <!-- Add your JavaScript links here -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <script>
    // Add your JavaScript code here
    // Get the form and input elements
    var form = document.getElementById('editUserForm');
    var passwordInput = document.getElementById('password');
    var confirmPasswordInput = document.getElementById('confirmPassword');
    var securityLevelElement = document.getElementById('securityLevel');

    // Add event listener for password input
    passwordInput.addEventListener('input', function(event) {
        var password = event.target.value;

        if (password.length >= 8  && /[A-Z]/.test(password) && /[0-9]/.test(password) && /[^A-Za-z0-9]/.test(password)) {
            securityLevelElement.innerText = 'Strong';
            securityLevelElement.style.color = 'green';
        } else if (password.length >= 8) {
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
        form.submit(); // Submit the form
    });
    </script>
</body>
</html>