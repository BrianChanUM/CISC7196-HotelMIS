<?php
require_once __DIR__ . '/config/db_config.php';
require_once __DIR__ . '/config/language.php';

$conn = getDBConnection();

    $sql = "SELECT OrderType, status, COUNT(*) as Total FROM orderbookings GROUP BY OrderType, status;";
    $result = $conn->query($sql);
	
    $sql1 = "SELECT OrderType, COUNT(*) as Total FROM orderbookings WHERE Status='TBC' GROUP BY OrderType";
    $result1 = $conn->query($sql1);

    $sql2 = "SELECT OrderType, COUNT(*) as Total FROM orderbookings WHERE Status='Confirmed' GROUP BY OrderType";
    $result2 = $conn->query($sql2);

    $sql3 = "SELECT OrderType, COUNT(*) as Total FROM orderbookings WHERE Status='Cancelled' GROUP BY OrderType";
    $result3 = $conn->query($sql3);

    $sql4 = "SELECT OrderType, COUNT(*) as Total FROM orderbookings WHERE Status='Completed' GROUP BY OrderType";
    $result4 = $conn->query($sql4);

    $data = array();
    $tbcData = array();
    $confirmedData = array();
    $cancelledData = array();
    $completedData = array();

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    if ($result1->num_rows > 0) {
        while($row = $result1->fetch_assoc()) {
            $tbcData[] = $row;
        }
    }

    if ($result2->num_rows > 0) {
        while($row = $result2->fetch_assoc()) {
            $confirmedData[] = $row;
        }
    }

    if ($result3->num_rows > 0) {
        while($row = $result3->fetch_assoc()) {
            $cancelledData[] = $row;
        }
    }

    if ($result4->num_rows > 0) {
        while($row = $result4->fetch_assoc()) {
            $completedData[] = $row;
        }
    }

    $json_data = json_encode($data);
    $json_tbc_data = json_encode($tbcData);
    $json_confirmed_data = json_encode($confirmedData);
    $json_cancelled_data = json_encode($cancelledData);
    $json_completed_data = json_encode($completedData);

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

        <div class="container"> <h4>MIS Request Performance Dashboard</h4>
             <div id="tabs">
        <button class="tab" onclick="showBarChart()">Bar Chart</button>
        <button class="tab" onclick="showPieChart()">Pie Chart</button>
		<button class="tab" onclick="showTBC()">TBC</button>
<button class="tab" onclick="showConfirmed()">Confirmed</button>
<button class="tab" onclick="showCancelled()">Cancelled</button>
<button class="tab" onclick="showCompleted()">Completed</button>
    </div>
    <!-- chart goes here -->
<div id="chartContainer" ">
    <canvas id="chart"></canvas> 
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
     var data = JSON.parse('<?php echo $json_data; ?>');
     var tbcData = JSON.parse('<?php echo $json_tbc_data; ?>');
     var confirmedData = JSON.parse('<?php echo $json_confirmed_data; ?>');
     var cancelledData = JSON.parse('<?php echo $json_cancelled_data; ?>');
     var completedData = JSON.parse('<?php echo $json_completed_data; ?>');
	 
       var labels = data.map(row => row.OrderType);
        var chartData = data.map(row => row.Total);
		var backgroundColors = [
    'rgba(255, 99, 132, 0.2)',
    'rgba(54, 162, 235, 0.2)',
    'rgba(255, 206, 86, 0.2)',
    'rgba(75, 192, 192, 0.2)',
    'rgba(153, 102, 255, 0.2)',
    'rgba(255, 159, 64, 0.2)'
];

var borderColors = [
    'rgba(255, 99, 132, 1)',
    'rgba(54, 162, 235, 1)',
    'rgba(255, 206, 86, 1)',
    'rgba(75, 192, 192, 1)',
    'rgba(153, 102, 255, 1)',
    'rgba(255, 159, 64, 1)'
];

var tabButtons = document.getElementsByClassName("tab");
for (var i = 0; i < tabButtons.length; i++) {
    tabButtons[i].addEventListener("click", function() {
        var current = document.getElementsByClassName("active");
        if (current.length > 0) {
            current[0].className = current[0].className.replace(" active", "");
        }
        this.className += " active";
    });
}
		
       var ctx = document.getElementById('chart').getContext('2d');
       var myChart = new Chart(ctx, {
           type: 'bar',
           data: {
               labels: labels,
               datasets: [{
                   label: '# of Request',
                   data: chartData,
                    backgroundColor: backgroundColors,
					borderColor: borderColors,
					borderWidth: 1
               }]
           },
           options: {
               scales: {
                   y: {
                       beginAtZero: true
                   }
               }
           }
       });

      function showBarChart() {
    myChart.destroy();
    myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: '# of Request',
                data: chartData,
                backgroundColor: backgroundColors,
				borderColor: borderColors,
				borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    var descEl = document.getElementById('chartDescription');
    if (descEl) descEl.innerText = "This bar chart shows the No. of request.";
}

       function showPieChart() {
           myChart.destroy();
           myChart = new Chart(ctx, {
               type: 'pie',
               data: {
                   labels: labels,
                   datasets: [{
                       label: '# of Request',
                       data: chartData,
                       backgroundColor: backgroundColors,
                       borderColor: borderColors,
                       borderWidth: 1
                   }]
               },
               options: {
                   responsive: true
               }
           });
           var descEl = document.getElementById('chartDescription');
           if (descEl) descEl.innerText = "This pie chart shows the No. of request.";
       }

function showTBC() {
    myChart.destroy();
    var tbcLabels = tbcData.map(row => row.OrderType);
    var tbcChartData = tbcData.map(row => row.Total);
    myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: tbcLabels,
            datasets: [{
                label: '# of TBC Requests',
                data: tbcChartData,
                backgroundColor: backgroundColors,
                borderColor: borderColors,
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    var descEl = document.getElementById('chartDescription');
    if (descEl) descEl.innerText = "This bar chart shows the No. of request with 'TBC' status.";
}

function showConfirmed() {
    myChart.destroy();
    var confirmedLabels = confirmedData.map(row => row.OrderType);
    var confirmedChartData = confirmedData.map(row => row.Total);
    myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: confirmedLabels,
            datasets: [{
                label: '# of Confirmed Requests',
                data: confirmedChartData,
                backgroundColor: backgroundColors,
                borderColor: borderColors,
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    var descEl = document.getElementById('chartDescription');
    if (descEl) descEl.innerText = "This bar chart shows the No. of request with 'Confirmed' status.";
}

function showCancelled() {
    myChart.destroy();
    var cancelledLabels = cancelledData.map(row => row.OrderType);
    var cancelledChartData = cancelledData.map(row => row.Total);
    myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: cancelledLabels,
            datasets: [{
                label: '# of Cancelled Requests',
                data: cancelledChartData,
                backgroundColor: backgroundColors,
                borderColor: borderColors,
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    var descEl = document.getElementById('chartDescription');
    if (descEl) descEl.innerText = "This bar chart shows the No. of request with 'Cancelled' status.";
}

function showCompleted() {
    myChart.destroy();
    var completedLabels = completedData.map(row => row.OrderType);
    var completedChartData = completedData.map(row => row.Total);
    myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: completedLabels,
            datasets: [{
                label: '# of Completed Requests',
                data: completedChartData,
                backgroundColor: backgroundColors,
                borderColor: borderColors,
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    var descEl = document.getElementById('chartDescription');
    if (descEl) descEl.innerText = "This bar chart shows the No. of request with 'Completed' status.";
}
    </script>


  </body>
</html>

