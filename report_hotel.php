<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/session_check.php';
require_once __DIR__ . '/config/language.php';
require_once __DIR__ . '/function/check_permission.php';
require_once __DIR__ . '/config/db_config.php';
requireModulePermission('admin_reports_hotel', 'index.php');

$conn = getDBConnection();

$reportType = isset($_POST['report_type']) ? $_POST['report_type'] : 'monthly';
$startDate = isset($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-01');
$endDate = isset($_POST['end_date']) ? $_POST['end_date'] : date('Y-m-t');
$roomType = isset($_POST['room_type']) ? $_POST['room_type'] : '';

$roomTypes = [];
$sql = "SELECT DISTINCT HotelRoomtype FROM hotelroomtype";
$stmt = $conn->query($sql);
while ($row = $stmt->fetch()) {
    $roomTypes[] = $row['HotelRoomtype'];
}

$chartData = [];
$labels = [];
$params = [];

if ($reportType === 'monthly') {
    $query = "SELECT MONTH(Time) as month, COUNT(*) as total 
              FROM orderbookings 
              WHERE OrderType = 'Hotel' 
              AND YEAR(Time) = YEAR(CURDATE())";
    if ($roomType) {
        $query .= " AND OrderRemark LIKE ?";
        $params[] = "%$roomType%";
    }
    $query .= " GROUP BY MONTH(Time) ORDER BY month";
} elseif ($reportType === 'daily') {
    $query = "SELECT DATE(Time) as date, COUNT(*) as total 
              FROM orderbookings 
              WHERE OrderType = 'Hotel' 
              AND Time BETWEEN ? AND ?";
    $params[] = $startDate;
    $params[] = $endDate;
    if ($roomType) {
        $query .= " AND OrderRemark LIKE ?";
        $params[] = "%$roomType%";
    }
    $query .= " GROUP BY DATE(Time) ORDER BY date";
} else {
    $query = "SELECT DATE(Time) as date, COUNT(*) as total 
              FROM orderbookings 
              WHERE OrderType = 'Hotel' 
              AND Time BETWEEN ? AND ?";
    $params[] = $startDate;
    $params[] = $endDate;
    if ($roomType) {
        $query .= " AND OrderRemark LIKE ?";
        $params[] = "%$roomType%";
    }
    $query .= " GROUP BY DATE(Time) ORDER BY date";
}

$stmt = $conn->prepare($query);
$stmt->execute($params);
$totalOrders = 0;
while ($row = $stmt->fetch()) {
    if ($reportType === 'monthly') {
        $labels[] = date('F', mktime(0, 0, 0, $row['month'], 1));
    } else {
        $labels[] = $row['date'];
    }
    $chartData[] = $row['total'];
    $totalOrders += $row['total'];
}

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Booking Report - HotelMIS</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .report-container {
            max-width: 1200px;
            margin: 80px auto 50px;
            padding: 20px;
        }
        .report-form {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .chart-container {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .stats-container {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }
        .stat-box {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
            min-width: 200px;
        }
        .stat-box h3 {
            margin: 0;
            font-size: 36px;
            color: #333;
        }
        .stat-box p {
            margin: 10px 0 0;
            color: #666;
        }
    </style>
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
                <?php include(__DIR__ . '/layout/header.php');?>
                <ul class="nav navbar-nav navbar-right" id="navbar"></ul>
                <?php include(__DIR__ . '/layout/language_switcher.php');?>
                <?php include(__DIR__ . '/layout/navbar.php');?>
            </div>
        </div>
    </nav>
    <div class="report-container">
        <div class="section-title">
            <h3>Hotel Booking Report</h3>
            <div class="clearfix"></div>
        </div>
        <form method="post" class="report-form">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="report_type">Report Type:</label>
                        <select class="form-control" id="report_type" name="report_type">
                            <option value="monthly" <?php echo $reportType === 'monthly' ? 'selected' : ''; ?>>Monthly</option>
                            <option value="daily" <?php echo $reportType === 'daily' ? 'selected' : ''; ?>>Daily</option>
                            <option value="daterange" <?php echo $reportType === 'daterange' ? 'selected' : ''; ?>>Date Range</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="room_type">Room Type:</label>
                        <select class="form-control" id="room_type" name="room_type">
                            <option value="">All Room Types</option>
                            <?php foreach ($roomTypes as $rt): ?>
                            <option value="<?php echo htmlspecialchars($rt); ?>" <?php echo $roomType === $rt ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($rt); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="start_date">Start Date:</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo htmlspecialchars($startDate); ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="end_date">End Date:</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo htmlspecialchars($endDate); ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">Generate Report</button>
                    </div>
                </div>
            </div>
        </form>
        <div class="stats-container">
            <div class="stat-box">
                <h3><?php echo $totalOrders; ?></h3>
                <p>Total Orders</p>
            </div>
        </div>
        <div class="chart-container">
            <div style="margin-bottom: 15px; text-align: right;">
                <label for="chartType" style="margin-right: 10px;">Chart Type:</label>
                <select id="chartType" onchange="updateChartType()" style="padding: 5px 10px; border-radius: 4px; border: 1px solid #ccc;">
                    <option value="bar" selected>Bar Chart</option>
                    <option value="pie">Pie Chart</option>
                    <option value="line">Line Chart</option>
                    <option value="doughnut">Doughnut Chart</option>
                </select>
            </div>
            <canvas id="hotelChart"></canvas>
        </div>
    </div>
    <?php include(__DIR__ . '/layout/footer.php');?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery.1.11.1.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script>
    var ctx = document.getElementById('hotelChart').getContext('2d');
    var chartData = <?php echo json_encode($chartData); ?>;
    var labels = <?php echo json_encode($labels); ?>;
    
    var hotelChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Hotel Bookings',
                data: chartData,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
    
    function updateChartType() {
        var chartType = document.getElementById('chartType').value;
        hotelChart.config.type = chartType;
        hotelChart.update();
    }
    </script>
</body>
</html>