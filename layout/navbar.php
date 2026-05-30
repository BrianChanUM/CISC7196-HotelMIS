<script>
    var user = <?php echo $user; ?>;
    var navbar = document.getElementById('navbar');

 if(user && user.username) {
        navbar.innerHTML += '<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fas fa-user"></i> Welcome, ' + user.username + ' (' + user.role + ') <b class="caret"></b></a><ul class="dropdown-menu"><li><a href="profile.php">Profile</a></li><li><a href="Preference.PHP">Preferences</a></li><li><a href="timesheet.php">TimeSheet History</a></li></ul></li>';
    if(user.role == 'admin') {
   navbar.innerHTML += '<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">AdminPanel <b class="caret"></b></a> <ul class="dropdown-menu"> <!-- Add your new menu items here --> <li><a href="addoutlet.php">Create New Outlet</a></li> <li><a href="addroomtype.php">Create New Room Type</a></li> <li><a href="addvehicletype.php">Create New Vehicle</a></li> <li><a href="addvehicle.php">Create New Vehicle No. </a></li><li><a href="Dashboard_user.php">All User Dashboard</a></li> <li class="dropdown-submenu"> <a href="Dashboard_request.php">All Department Report</a> <ul class="dropdown-menu"> <li><a href="#">FnB</a></li> <li><a href="#">Hotel</a></li> <li><a href="#">Limo</a></li> </ul> </li> <li><a href="adminlist_orderv3.php">All Transaction Details</a></li> </ul> </li>';
} else if(user.role == 'staff') {
    navbar.innerHTML += '<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">StaffPanel <b class="caret"></b></a><ul class="dropdown-menu"><li><a href="Dashboard_request.php">User Dashboard</a></li><li><a href="adminlist_orderv2.php">Department History</a></li><li><a href="jobassignment.php">Job Assignment</a></li><li><a href="autotask.php">Auto Task Setup</a></li></ul></li>';
} else if(user.role == 'guest') {
    navbar.innerHTML += '<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Guest Panel <b class="caret"></b></a><ul class="dropdown-menu"><li><a href="OrderHistory.php">Order History</a></li><li><a href="login_UserAccessLog_Table.html">User Access log</a></li><li><a href="#">Something else here</a></li></ul></li>';
}
        navbar.innerHTML += '<li><a href="#" id="logout" class="page-scroll">Logout</a></li>';
    } else {

        navbar.innerHTML += '<li><a href="login.php" class="page-scroll">Login</a></li>';
        navbar.innerHTML += '<li><a href="signup.php" class="page-scroll">Signup</a></li>'; 
    }

    document.getElementById('logout').addEventListener('click', function() {
    var confirmLogout = window.confirm('Are you sure you want to logout?');
    if(confirmLogout) {
        // Call a PHP function to destroy the session
        fetch('function/logout.php')
        .then(response => response.text())
        .then(data => {
            if(data == 'success') {
                window.alert('Logout successful!');
                window.location.href = 'index.php';
            } else {
                window.alert('Logout failed!');
            }
        });
    }
});
</script>