<?php
require_once __DIR__ . '/config/db_config.php';
require_once __DIR__ . '/config/language.php';

$conn = getDBConnection();

    // Query to get user role details
    $sql = "SELECT Role, COUNT(*) as Total FROM user GROUP BY Role";
    $result = $conn->query($sql);

    $data = array(); // Array to hold your data

    // Process the results
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            // Add each row of data to the $data array
            $data[] = $row;
        }
    }

    // Convert the $data array into JSON format
    $json_data = json_encode($data);

    // Query to get DB details
    $sql = "SHOW TABLE STATUS";
    $result = $conn->query($sql);

    $dbDetails = array(); // Array to hold your data

    // Process the results
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            // Add each row of data to the $dbDetails array
            $dbDetails[] = $row;
        }
    }

    // Convert the $dbDetails array into JSON format
    $json_dbDetails = json_encode($dbDetails);

    // Query to get user details
    $sql = "SELECT * FROM user INNER JOIN userprofile ON user.UID = userprofile.UID";
    $result = $conn->query($sql);

    $userDetails = array(); // Array to hold your data

    // Process the results
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            // Add each row of data to the $userDetails array
            $userDetails[] = $row;
        }
    }

    // Convert the $userDetails array into JSON format
    $json_userDetails = json_encode($userDetails);

    $conn->close();
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
	<link rel="stylesheet" type="text/css" href="css/chart.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
<?php
    $user = json_encode($_SESSION);
?>

<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
     <?php include(__DIR__ . '/layout/header.php');?>
    <ul class="nav navbar-nav navbar-right" id="navbar"></ul>
	 <?php include(__DIR__ . '/layout/language_switcher.php');?>
	 <?php include(__DIR__ . '/layout/navbar.php');?>
 
	
</div>
	
</div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>

    <!-- Home Page
    ==========================================-->
    <div id="tf-home" class="text-center">
	<a href="#tf-contact" ></a>
         
    </div>
	
	<div id="tf-about">
<div id="roleTable" style="display: flex; justify-content: center;">
     <table class="styled-table">
        <thead>
            <tr>
                <th>Role</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody id="tableBody">
        </tbody>
    </table>
</div>

  <div id="userDetailsTable" style="display: flex; justify-content: center;">
         <table class="styled-table">
            <thead>
                <tr>
                    <th>UID</th>
                    <th>UserName</th>
                    <th>Role</th>
                    <th>CreateDate</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Level</th>
                    <th>SalaryRate</th>
                    <th>OnboardDate</th>
                </tr>
            </thead>
            <tbody id="userDetailsBody">
            </tbody>
        </table>
    </div>


    </div>
	
	
	


      <?php include(__DIR__ . '/layout/footer.php');?>


    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery.1.11.1.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script type="text/javascript" src="js/SmoothScroll.js"></script>
    <script type="text/javascript" src="js/jquery.isotope.js"></script>

    <script src="js/owl.carousel.js"></script>

    <!-- Javascripts
    ================================================== -->
    <script type="text/javascript" src="js/main.js"></script>
 <script>
let data = <?php echo $json_data; ?>;
var userDetails = JSON.parse('<?php echo $json_userDetails; ?>');
var tableBody = document.getElementById('userDetailsBody');

function createCell(text) {
    const cell = document.createElement('td');
    cell.textContent = text;
    return cell;
}

function populateTable(data) {
    const tableBody = document.getElementById('tableBody');
    if (!tableBody) return;
    tableBody.innerHTML = '';

    data.forEach(item => {
        const row = document.createElement('tr');
        row.appendChild(createCell(item.Role));
        row.appendChild(createCell(item.Total));
        row.addEventListener('click', () => viewProfiles(item.Role));
        tableBody.appendChild(row);
    });
}

function viewProfiles(role) {
    fetch(`fetchProfiles.php?role=${role}`)
        .then(response => response.json())
        .then(data => {
            if (!data || data.length === 0) {
                console.log('No data found for role:', role);
                return;
            }

            const table = document.createElement('table');
            table.className = 'styled-table';

            const createHeaderCell = (text) => {
                const th = document.createElement('th');
                th.textContent = text;
                return th;
            };

            const thead = document.createElement('thead');
            const headerRow = document.createElement('tr');
            Object.keys(data[0]).forEach(key => {
                headerRow.appendChild(createHeaderCell(key));
            });
            thead.appendChild(headerRow);
            table.appendChild(thead);

            const tbody = document.createElement('tbody');
            data.forEach(item => {
                const row = document.createElement('tr');
                Object.values(item).forEach(value => {
                    row.appendChild(createCell(value !== null ? value : 'N/A'));
                });
                tbody.appendChild(row);
            });
            table.appendChild(tbody);

            var dropdownDiv = document.getElementById('dropdownDiv');
            if (dropdownDiv) {
                dropdownDiv.innerHTML = '';
                dropdownDiv.appendChild(table);
            } else {
                var container = document.createElement('div');
                container.id = 'dropdownDiv';
                container.style.marginTop = '20px';
                container.appendChild(table);
                document.getElementById('tf-about').appendChild(container);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

window.onload = function() {
    populateTable(data);

    if (tableBody && userDetails.length > 0) {
        for (var i = 0; i < userDetails.length; i++) {
            var row = document.createElement('tr');
            var columns = ['UID', 'UserName', 'Role', 'CreateDate', 'Email', 'Department', 'Level', 'SalaryRate', 'OnboardDate'];
            
            columns.forEach(function(columnName) {
                var column = document.createElement('td');
                column.innerText = userDetails[i][columnName] !== null ? userDetails[i][columnName] : 'N/A';
                row.appendChild(column);
            });
            tableBody.appendChild(row);
        }
    }
}
 </script>

  </body>
</html>


<style>
table {
    border-collapse: collapse;
    margin: 0 auto; /* This will center the table */
}

th, td {
    border: 1px solid black;
    padding: 10px;
}
.styled-table {
    border-collapse: collapse;
    margin: 0 auto; /* This will center the table */
    font-size: 0.9em;
    font-family: sans-serif;
    min-width: 400px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
}
.styled-table thead tr {
    background-color: #009879;
    color: white;
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
.styled-table tbody tr.active-row {
    font-weight: bold;
    color: #009879;
}



        .styled-table {
            border-collapse: collapse;
            margin: 25px 0;
            font-size: 0.9em;
            font-family: sans-serif;
            min-width: 400px;
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


</style>