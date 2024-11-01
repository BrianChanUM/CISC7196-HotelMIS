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
    background-color: #fefefe;
    margin: auto; /* This will center the modal */
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    position: relative;
    top: 50%;
    transform: translateY(-50%); /* This will center the modal vertically */
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
          <a class="navbar-brand" href="index.php">HotelMIS </a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->


<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
    <?php include(__DIR__ . '/layout/header.php');?>
    <ul class="nav navbar-nav navbar-right" id="navbar"></ul>
	<?php include(__DIR__ . '/layout/navbar.php');?>

	
</div>
        <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
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
          <h3>Hotel Enquiry Table</h3>
		  <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search for bookings..">
          <div class="clearfix"></div>
        </div>

        <table id="bookingTable">
          <tr>
            <th onclick="sortTable(0)">Enquiry ID</th>
            <th onclick="sortTable(0)">UserName</th>
            <th onclick="sortTable(1)">Email</th>
            <th onclick="sortTable(2)">Contact Name</th>
            <th onclick="sortTable(3)">Enquiry Type</th>
            <th onclick="sortTable(4)">Enquiry Message</th>
            <th onclick="sortTable(5)">Enquiry CreatedTime</th>
            <th onclick="sortTable(6)">Enquiry Status</th>
            <th>Action</th>
			<th onclick="sortTable(6)">Enquiry Modified Time</th>
          </tr>
          <?php
          $servername = "localhost";
          $username = "root";
          $password = "";
          $dbname = "hmis";

          // Create connection
          $conn = new mysqli($servername, $username, $password, $dbname);

          // Check connection
          if ($conn->connect_error) {
              die("Connection failed: " . $conn->connect_error);
          }

          $sql = "SELECT * FROM enquiry";
          $result = $conn->query($sql);

          if ($result->num_rows > 0) {
              // output data of each row
              while($row = $result->fetch_assoc()) {
                  echo "<tr>";
				  echo "<td>" . $row["eID"] . "</td>";
                  echo "<td>" . $row["eUser"] . "</td>";
                  echo "<td>" . $row["eEmail"] . "</td>";
                  echo "<td>" . $row["ePhone"] . "</td>";
                  echo "<td>" . $row["eType"] . "</td>";
                  echo "<td>" . $row["eContent"] . "</td>";
                  echo "<td>" . $row["eCreatedDate"] . "</td>";
				  echo "<td>" . $row["eStatus"] . "</td>";
					echo "<td><button class='action-btn review' onclick='openModal(\"" . $row["eID"] . "\", \"" . $row["eUser"] . "\", \"" . $row["eEmail"] . "\", \"" . $row["ePhone"] . "\", \"" . $row["eType"] . "\", \"" . $row["eContent"] . "\", \"" . $row["eCreatedDate"] . "\", \"" . $row["eStatus"] . "\", \"" . $row["eModifiedDate"] . "\")'>Review</button></td>";
				  echo "<td>" . $row["eModifiedDate"] . "</td>";
                  echo "</tr>";
              }
          } else {
              echo "0 results";
          }
          $conn->close();
          ?>
        </table>
      </div>
      <div id="myModal" class="modal">
        <div class="modal-content">
          <span class="close">&times;</span>
          <p id="modalText"></p>
          <button class="action-btn confirm" onclick="confirmBooking()">Confirm</button>
          <button class="action-btn reject" onclick="rejectBooking()">Reject</button>
        </div>
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

	    function searchTable() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("bookingTable");
            tr = table.getElementsByTagName("tr");
            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[0]; // Change this to the column you want to filter
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }       
            }
        }

function sortTable(n) {
  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
  table = document.getElementById("bookingTable");
  switching = true;
  dir = "asc"; 
  while (switching) {
    switching = false;
    rows = table.rows;
    for (i = 1; i < (rows.length - 1); i++) {
      shouldSwitch = false;
      x = rows[i].getElementsByTagName("TD")[n];
      y = rows[i + 1].getElementsByTagName("TD")[n];
      if (dir == "asc") {
        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
          shouldSwitch = true;
          break;
        }
      } else if (dir == "desc") {
        if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
          shouldSwitch = true;
          break;
        }
      }
    }
    if (shouldSwitch) {
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
      switchcount ++; 
    } else {
      if (switchcount == 0 && dir == "asc") {
        dir = "desc";
        switching = true;
      }
    }
  }
}

var modal = document.getElementById("myModal");
var span = document.getElementsByClassName("close")[0];

function openModal(orderId, time, places, eventType, contact, phone, email, status) {
  document.getElementById("modalText").innerHTML = "EnquiryID: " + orderId + "<br>UserName: " + time + "<br>Email: " + places + "<br>Contact Name: " + eventType + "<br>Enquiry Type: " + contact + "<br>Enquiry Message: " + phone + "<br>E-CreatedTime: " + email + "<br>Enquiry Status: " + status;
  modal.style.display = "block";
}

span.onclick = function() {
  modal.style.display = "none";
}

window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}

function confirmBooking() {
  alert("Enquiry confirmed!");
}

function rejectBooking() {
  alert("Enquiry rejected!");
}



    </script>
  </body>
</html>

