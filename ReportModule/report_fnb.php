<?php
require_once __DIR__ . '/config/session_check.php';
require_once __DIR__ . '/config/db_config.php';
require_once __DIR__ . '/config/language.php';
require_once __DIR__ . '/function/check_permission.php';
requireModulePermission('admin_reports_fnb', 'index.php');

$conn = getDBConnection();

$reportType = isset($_POST['report_type']) ? $_POST['report_type'] : 'monthly';
$startDate = isset($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-01');
$endDate = isset($_POST['end_date']) ? $_POST['end_date'] : date('Y-m-t');
$outletName = isset($_POST['outlet_name']) ? $_POST['outlet_name'] : '';

$outlets = [];
$sql = "SELECT DISTINCT OutletName, Style FROM hoteloutlet";
$result = $conn->query($sql);
while ($row = $result->fetch()) {
    $outlets[] = ['name' => $row['OutletName'], 'type' => $row['Style']];
}

$chartData = [];
$labels = [];
$outletStats = [];

$params = [];
if ($reportType === 'monthly') {
    $query = "SELECT MONTH(Time) as month, COUNT(*) as total 
              FROM orderbookings 
              WHERE OrderType = 'Dining' 
              AND YEAR(Time) = YEAR(CURDATE())";
    if ($outletName) {
        $query .= " AND OrderRemark LIKE ?";
        $params[] = "%$outletName%";
    }
    $query .= " GROUP BY MONTH(Time) ORDER BY month";
} elseif ($reportType === 'daily') {
    $query = "SELECT DATE(Time) as date, COUNT(*) as total 
              FROM orderbookings 
              WHERE OrderType = 'Dining' 
              AND Time BETWEEN ? AND ?";
    $params[] = $startDate;
    $params[] = $endDate;
    if ($outletName) {
        $query .= " AND OrderRemark LIKE ?";
        $params[] = "%$outletName%";
    }
    $query .= " GROUP BY DATE(Time) ORDER BY date";
} else {
    $query = "SELECT DATE(Time) as date, COUNT(*) as total 
              FROM orderbookings 
              WHERE OrderType = 'Dining' 
              AND Time BETWEEN ? AND ?";
    $params[] = $startDate;
    $params[] = $endDate;
    if ($outletName) {
        $query .= " AND OrderRemark LIKE ?";
        $params[] = "%$outletName%";
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

$outletParams = [];
$outletQuery = "SELECT o.OutletName, o.Style, COUNT(ob.OrderID) as OrderCount
                FROM hoteloutlet o
                LEFT JOIN orderbookings ob ON ob.OrderRemark LIKE CONCAT('%', o.OutletName, '%')
                WHERE ob.OrderType IN ('Dining', 'F&B')";
if ($startDate && $endDate) {
    $outletQuery .= " AND ob.Time BETWEEN ? AND ?";
    $outletParams[] = $startDate;
    $outletParams[] = $endDate;
}
$outletQuery .= " GROUP BY o.OutletName, o.Style ORDER BY OrderCount DESC";

$outletStmt = $conn->prepare($outletQuery);
$outletStmt->execute($outletParams);
while ($row = $outletStmt->fetch()) {
    $outletStats[] = [
        'name' => $row['OutletName'],
        'type' => $row['Style'],
        'count' => $row['OrderCount']
    ];
}

$avgOrdersPerDay = 0;
if ($reportType !== 'monthly' && $totalOrders > 0) {
    $days = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24) + 1;
    $avgOrdersPerDay = round($totalOrders / $days, 1);
}

closeDBConnection($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dining (F&B) Report - HotelMIS</title>
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
            <h3>Dining (F&B) Report</h3>
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
                        <label for="outlet_name">Restaurant:</label>
                        <select class="form-control" id="outlet_name" name="outlet_name">
                            <option value="">All Restaurants</option>
                            <?php foreach ($outlets as $o): ?>
                            <option value="<?php echo htmlspecialchars($o['name']); ?>" <?php echo $outletName === $o['name'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($o['name']); ?>
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
                <p>Total Restaurant Orders</p>
            </div>
            <div class="stat-box">
                <h3><?php echo count($outletStats); ?></h3>
                <p>Restaurants</p>
            </div>
            <div class="stat-box">
                <h3><?php echo $avgOrdersPerDay; ?></h3>
                <p>Avg Orders/Day</p>
            </div>
            <div class="stat-box">
                <h3><?php echo $outletStats[0]['count'] ?? 0; ?></h3>
                <p>Top Restaurant</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="chart-container">
                    <h4>Orders by Restaurant</h4>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Restaurant Name</th>
                                <th>Type</th>
                                <th>Order Count</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($outletStats as $stat): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($stat['name']); ?></td>
                                <td><span class="label label-info"><?php echo htmlspecialchars($stat['type']); ?></span></td>
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
                            <option value="pie">Pie Chart</option>
                            <option value="bar">Bar Chart</option>
                            <option value="line">Line Chart</option>
                            <option value="doughnut">Doughnut Chart</option>
                        </select>
                    </div>
                    <canvas id="fnbChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <?php include(__DIR__ . '/layout/footer.php');?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery.1.11.1.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    
    <script>
    var ctx = document.getElementById('fnbChart').getContext('2d');
    var outletStats = <?php echo json_encode($outletStats); ?>;
    var totalOrders = <?php echo $totalOrders; ?>;
    
    var labels = outletStats.map(function(item) { return item.name; });
    var chartData = outletStats.map(function(item) { return item.count; });
    
    var fnbChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                label: 'Orders by Restaurant',
                data: chartData,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(255, 206, 86, 0.6)',
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(153, 102, 255, 0.6)',
                    'rgba(255, 159, 64, 0.6)',
                    'rgba(199, 21, 133, 0.6)',
                    'rgba(0, 128, 0, 0.6)'
                ],
                borderWidth: 1
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
                    text: 'Orders by Restaurant'
                }
            }
        }
    });
    
    function updateChartType() {
        var chartType = document.getElementById('chartType').value;
        fnbChart.config.type = chartType;
        fnbChart.update();
    }
    </script>
</body>
</html>
