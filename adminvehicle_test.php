<?php
    session_start();
    $user = json_encode($_SESSION);
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Basic Page Needs
    ================================================== -->
    <meta charset="utf-8">
    <!--[if IE]>
			<meta http-equiv="x-ua-compatible" content="IE=9" />
			<![endif]-->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CISC7196-HotelMIS-2023OCT18</title>
  
    <!-- Favicons
    ================================================== -->
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="img/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="img/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="img/apple-touch-icon-114x114.png">
    <!-- Bootstrap -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
      table {
        border-collapse: collapse;
        width: 230%;
        font-family: Arial, sans-serif;
      }

      th,td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
		
      }

      th {
        background-color: #5C9EE2;
        color: black;
      }

      /* Add this to your existing style */
      .action-btn {
        margin: 5px;
        padding: 5px 10px;
        border: none;
        border-radius: 5px;
        color: white;
        cursor: pointer;
      }

      .confirm {
        background-color: green;
      }

      .reject {
        background-color: red;
      }

      .review {
        background-color: orange;
      }

     .modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto;
    background-color: rgb(0,0,0);
    background-color: rgba(0,0,0,0.4);
}

.modal-content {
    background-color: #ffffff;
    margin: auto;
    padding: 20px;
    border: 1px solid #ccc;
    width: 90%; /* Increase the width for a larger modal */
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 10px; /* Slightly rounded corners */
    transition: opacity 0.3s ease;
    font-size: 16px; /* Adjust font size as needed */
}




.save-button {
    background-color: #4CAF50;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}
.save-button:hover {
    background-color: #45a049;
}








/* Optional hover effect */
.modal-content:hover {
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

      .close {
        color: #aaaaaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
      }

      .close:hover,
      .close:focus {
        color: #000;
        text-decoration: none;
        cursor: pointer;
      }
	            .paging {
            background-color: grey;
            color: black;
          }
    </style>
    <!-- Slider
    ================================================== -->
    <link href="css/owl.carousel.css" rel="stylesheet" media="screen">
    <link href="css/owl.theme.css" rel="stylesheet" media="screen">
    <!-- Stylesheet
    ================================================== -->
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/responsive.css">
    
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
          <a class="navbar-brand" href="index.php">HotelMIS </a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->


<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
    <?php include(__DIR__ . '/layout/header.php');?>
    <ul class="nav navbar-nav navbar-right" id="navbar"></ul>
	<?php include(__DIR__ . '/layout/navbar.php');?>

	
</div>
       
      </div>

    </nav>
    <!-- Home Page
    ==========================================-->
    <div id="tf-home" class="text-center">
      <a href="#tf-contact"></a>
    </div>
    <div id="tf-about">
  <div class="container">
    <div class="row">
      <div class="col-md-6">
        <div class="section-title">
          <h3>Vehicle Type Management</h3>

          <div class="clearfix"></div>
		<button type="button" id="changeButton" class="btn btn-primary">Edit Profile</button>
        </div>

        <table id="bookingTable">
         <tr>
                <th>Vehicle Type</th>
                <th>Status</th>
            </tr>
          <?php
            // Replace with actual database query results
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "HMIS";

            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Query to retrieve vehicle types and status
            $sql = "SELECT vehicletype, status FROM hotelvehicletype";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td class="editable" data-vehicletype="' . $row['vehicletype'] . '">' . $row['vehicletype'] . '</td>';
                    echo '<td class="editable" data-status="' . $row['status'] . '">' . ($row['status'] ? 'Enabled' : 'Disabled') . '</td>';
                    echo '</tr>';
                }
            } else {
                echo '<tr><td colspan="2">No data available</td></tr>';
            }

            $conn->close();
            ?>
        </table>
		 <!--    <div id="modal">
        <div class="modal-content">
    <h3>Edit Vehicle Type</h3>
 <!--   <form method="post" >
    <div class="form-row">
        <label for="editVehicleType">Vehicle Type:</label>
        <input type="text" id="editVehicleType" placeholder="Enter vehicle type">
    </div>
	
	<br>
    <!-- Add other form elements here 
    <div class="form-row">
        <label>Status:</label>
        <input type="radio" id="enableStatus" name="status" value="enabled">
        <label for="enableStatus">Enable</label>
        <input type="radio" id="disableStatus" name="status" value="disabled">
        <label for="disableStatus">Disable</label>
    </div>
     <button type="submit">Save Changes</button> 
	<?php
// update_vehicle_data.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $vehicleType = $_POST['editVehicleType'];
    $status = $_POST['status']; // 'enabled' or 'disabled'

    // Replace with your actual database credentials (from environment variables or config file)
      $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "HMIS";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("UPDATE hotelvehicletype SET status = ? WHERE vehicletype = ?");
    $stmt->bind_param("ss", $status, $vehicleType);

    if ($stmt->execute()) {
        echo 'Database updated successfully';
    } else {
        echo 'Error updating database: ' . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

	 
</form> -->

</div>
    </div> -->
      </div>
    </div>
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
        // Add event listeners for double-click on editable cells
        const editableCells = document.querySelectorAll('.editable');
        editableCells.forEach((cell) => {
            cell.addEventListener('dblclick', (event) => {
                const vehicleType = cell.getAttribute('data-vehicletype');
                const status = cell.getAttribute('data-status');

                // Show the edit modal
                document.getElementById('editVehicleType').value = vehicleType;
                document.getElementById('editStatus').checked = status === '1'; // Convert to boolean

                // Display the modal
                document.getElementById('modal').style.display = 'block';
            });
        });

        // Implement saveChanges function to update the database (AJAX request)
        function saveChanges() {
            // Get edited values from the modal
            const editedVehicleType = document.getElementById('editVehicleType').value;
            const editedStatus = document.getElementById('editStatus').checked;

            // Update the database using AJAX (similar to your existing logic)
            // Refresh the table after updating
            // ...

            // Hide the modal
            document.getElementById('modal').style.display = 'none';
        }
		
		

window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}

<script>
document.getElementById('changeButton').addEventListener('click', function() {
    // Create a new iframe
    var iframe = document.createElement('iframe');

    // Set the iframe attributes
    iframe.src = 'profile.php';
    iframe.width = '100%';
    iframe.height = '600';

    // Remove any existing iframes
    var iframeContainer = document.getElementById('iframeContainer');
    while (iframeContainer.firstChild) {
        iframeContainer.firstChild.remove();
    }

    // Add the new iframe to the container
    iframeContainer.appendChild(iframe);
});

document.getElementById('changeButton').addEventListener('click', function() {
    // Open a new window with '.php'
    window.open('adminvehiclecreate.php', '_blank', 'width=600,height=600');
});

    </script>



    </script>
  </body>
</html>

