<?php
require_once __DIR__ . '/config/db_config.php';
require_once __DIR__ . '/config/language.php';
require_once __DIR__ . '/function/check_permission.php';
requireModulePermission('admin_reports_limo', 'index.php');

$conn = getDBConnection();

$reportType = isset($_POST['report_type']) ? $_POST['report_type'] : 'monthly';
$startDate = isset($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-01');
$endDate = isset($_POST['end_date']) ? $_POST['end_date'] : date('Y-m-t');
$brand = isset($_POST['brand']) ? $_POST['brand'] : '';

$brands = [];
$sql = "SELECT DISTINCT VehicleType FROM hotelvehicletype";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $brands[] = $row['VehicleType'];
    }
}

$chartData = [];
$labels = [];
$query = "";
$vehicleStats = [];

if ($reportType === 'monthly') {
    $query = "SELECT MONTH(Time) as month, COUNT(*) as total 
              FROM orderbookings 
              WHERE OrderType = 'Limo' 
              AND YEAR(Time) = YEAR(CURDATE())";
    if ($brand) {
        $query .= " AND OrderRemark LIKE '%$brand%'";
    }
    $query .= " GROUP BY MONTH(Time) ORDER BY month";
} elseif ($reportType === 'daily') {
    $query = "SELECT DATE(Time) as date, COUNT(*) as total 
              FROM orderbookings 
              WHERE OrderType = 'Limo' 
              AND Time BETWEEN '$startDate' AND '$endDate'";
    if ($brand) {
        $query .= " AND OrderRemark LIKE '%$brand%'";
    }
    $query .= " GROUP BY DATE(Time) ORDER BY date";
} else {
    $query = "SELECT DATE(Time) as date, COUNT(*) as total 
              FROM orderbookings 
              WHERE OrderType = 'Limo' 
              AND Time BETWEEN '$startDate' AND '$endDate'";
    if ($brand) {
        $query .= " AND OrderRemark LIKE '%$brand%'";
    }
    $query .= " GROUP BY DATE(Time) ORDER BY date";
}

$vehicleQuery = "SELECT vt.VehicleType, COUNT(ob.OrderID) as OrderCount
                FROM hotelvehicletype vt
                LEFT JOIN orderbookings ob ON ob.OrderRemark LIKE CONCAT('%', vt.VehicleType, '%')
                WHERE ob.OrderType = 'Limo'";
if ($startDate && $endDate) {
    $vehicleQuery .= " AND ob.Time BETWEEN '$startDate' AND '$endDate'";
}
$vehicleQuery .= " GROUP BY vt.VehicleType ORDER BY OrderCount DESC";

$vehicleResult = $conn->query($vehicleQuery);
if ($vehicleResult->num_rows > 0) {
    while ($row = $vehicleResult->fetch_assoc()) {
        $vehicleStats[] = [
            'type' => $row['VehicleType'],
            'count' => $row['OrderCount']
        ];
    }
}

$result = $conn->query($query);
$totalOrders = 0;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if ($reportType === 'monthly') {
            $labels[] = date('F', mktime(0, 0, 0, $row['month'], 1));
        } else {
            $labels[] = $row['date'];
        }
        $chartData[] = $row['total'];
        $totalOrders += $row['total'];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Limo Service Report - HotelMIS</title>
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
            <h3>Limo Service Report</h3>
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
                        <label for="brand">Vehicle Brand:</label>
                        <select class="form-control" id="brand" name="brand">
                            <option value="">All Brands</option>
                            <?php foreach ($brands as $b): ?>
                            <option value="<?php echo htmlspecialchars($b); ?>" <?php echo $brand === $b ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($b); ?>
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
            <div class="stat-box">
                <h3><?php echo count($vehicleStats); ?></h3>
                <p>Vehicle Types</p>
            </div>
            <div class="stat-box">
                <h3><?php echo $vehicleStats[0]['count'] ?? 0; ?></h3>
                <p>Top Vehicle Type</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="chart-container">
                    <h4>Orders by Vehicle Type</h4>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Vehicle Type</th>
                                <th>Order Count</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($vehicleStats as $stat): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($stat['type']); ?></td>
                                <td><?php echo $stat['count']; ?></td>
                                <td><?php echo $totalOrders > 0 ? round(($stat['count'] / $totalOrders) * 100, 1) : 0; ?>%</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-container">
                    <div style="margin-bottom: 15px; text-align: right;">
                        <label for="chartType" style="margin-right: 10px;">Chart Type:</label>
                        <select id="chartType" onchange="updateChartType()" style="padding: 5px 10px; border-radius: 4px; border: 1px solid #ccc;">
                            <option value="line" selected>Line Chart</option>
                            <option value="bar">Bar Chart</option>
                            <option value="pie">Pie Chart</option>
                            <option value="doughnut">Doughnut Chart</option>
                        </select>
                    </div>
                    <canvas id="limoChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <?php include(__DIR__ . '/layout/footer.php');?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery.1.11.1.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    
    <script>
    var ctx = document.getElementById('limoChart').getContext('2d');
    var vehicleStats = <?php echo json_encode($vehicleStats); ?>;
    
    var labels = vehicleStats.map(function(item) { return item.type; });
    var chartData = vehicleStats.map(function(item) { return item.count; });
    
    var limoChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Orders by Vehicle Type',
                data: chartData,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                title: {
                    display: true,
                    text: 'Orders by Vehicle Type'
                }
            },
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
        limoChart.config.type = chartType;
        limoChart.update();
    }
    </script>
</body>
</html>
