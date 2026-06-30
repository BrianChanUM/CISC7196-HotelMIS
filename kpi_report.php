<?php
require_once __DIR__ . '/config/session_check.php';
require_once __DIR__ . '/config/db_config.php';
require_once __DIR__ . '/config/language.php';
require_once __DIR__ . '/function/check_permission.php';
requireModulePermission('admin_reports_kpi', 'index.php');

$conn = getDBConnection();

$startDate = isset($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-01');
$endDate = isset($_POST['end_date']) ? $_POST['end_date'] : date('Y-m-t');
$orderType = isset($_POST['order_type']) ? $_POST['order_type'] : 'All';

$orderTypes = ['All', 'Hotel', 'Limo', 'Dining', 'F&B', 'InRoomService'];

$baseQuery = "SELECT * FROM orderbookings WHERE OrderCreatedDate BETWEEN ? AND ?";
$params = [$startDate . ' 00:00:00', $endDate . ' 23:59:59'];

if ($orderType !== 'All') {
    $baseQuery .= " AND OrderType = ?";
    $params[] = $orderType;
}

$stmt = $conn->prepare($baseQuery);
$stmt->execute($params);
$totalOrders = $stmt->rowCount();

$dateParams = [$startDate . ' 00:00:00', $endDate . ' 23:59:59'];

$escalatedQuery = "SELECT COUNT(*) as count FROM orderbookings WHERE escalated = 1 AND OrderCreatedDate BETWEEN ? AND ?";
$escalatedParams = $dateParams;
if ($orderType !== 'All') {
    $escalatedQuery .= " AND OrderType = ?";
    $escalatedParams[] = $orderType;
}
$escalatedStmt = $conn->prepare($escalatedQuery);
$escalatedStmt->execute($escalatedParams);
$escalatedCount = $escalatedStmt->fetch()['count'];
$escalatedRatio = $totalOrders > 0 ? round(($escalatedCount / $totalOrders) * 100, 1) : 0;

$cancelledQuery = "SELECT COUNT(*) as count FROM orderbookings WHERE Status = 'Cancelled' AND OrderCreatedDate BETWEEN ? AND ?";
$cancelledParams = $dateParams;
if ($orderType !== 'All') {
    $cancelledQuery .= " AND OrderType = ?";
    $cancelledParams[] = $orderType;
}
$cancelledStmt = $conn->prepare($cancelledQuery);
$cancelledStmt->execute($cancelledParams);
$cancelledCount = $cancelledStmt->fetch()['count'];
$cancelledRatio = $totalOrders > 0 ? round(($cancelledCount / $totalOrders) * 100, 1) : 0;

$statusQuery = "SELECT Status, COUNT(*) as count FROM orderbookings WHERE Status IN ('TBC', 'Confirmed', 'Completed') AND OrderCreatedDate BETWEEN ? AND ?";
$statusParams = $dateParams;
if ($orderType !== 'All') {
    $statusQuery .= " AND OrderType = ?";
    $statusParams[] = $orderType;
}
$statusQuery .= " GROUP BY Status";
$statusStmt = $conn->prepare($statusQuery);
$statusStmt->execute($statusParams);
$statusData = $statusStmt->fetchAll(PDO::FETCH_ASSOC);

$statusCounts = ['TBC' => 0, 'Confirmed' => 0, 'Completed' => 0];
foreach ($statusData as $row) {
    $statusCounts[$row['Status']] = $row['count'];
}

$statusRatios = [];
foreach ($statusCounts as $status => $count) {
    $statusRatios[$status] = $totalOrders > 0 ? round(($count / $totalOrders) * 100, 1) : 0;
}

$timeseriesQuery = "SELECT DATE(OrderCreatedDate) as date, HOUR(OrderCreatedDate) as hour, COUNT(*) as count FROM orderbookings WHERE OrderCreatedDate BETWEEN ? AND ?";
$timeseriesParams = $dateParams;
if ($orderType !== 'All') {
    $timeseriesQuery .= " AND OrderType = ?";
    $timeseriesParams[] = $orderType;
}
$timeseriesQuery .= " GROUP BY DATE(OrderCreatedDate), HOUR(OrderCreatedDate) ORDER BY DATE(OrderCreatedDate), HOUR(OrderCreatedDate)";
$timeseriesStmt = $conn->prepare($timeseriesQuery);
$timeseriesStmt->execute($timeseriesParams);
$timeseriesRaw = $timeseriesStmt->fetchAll(PDO::FETCH_ASSOC);

$timeseriesLabels = [];
$timeseriesData = [];
foreach ($timeseriesRaw as $row) {
    $timeseriesLabels[] = $row['date'] . ' ' . str_pad($row['hour'], 2, '0', STR_PAD_LEFT) . ':00:00';
    $timeseriesData[] = $row['count'];
}

closeDBConnection($conn);

$statusLabels = json_encode(['TBC', 'Confirmed', 'Completed']);
$statusDataValues = json_encode([$statusCounts['TBC'], $statusCounts['Confirmed'], $statusCounts['Completed']]);
$statusRatioValues = json_encode([$statusRatios['TBC'], $statusRatios['Confirmed'], $statusRatios['Completed']]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KPI Report - HotelMIS</title>
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/responsive.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script type="text/javascript" src="js/modernizr.custom.js"></script>
    <style>
        body { padding-top: 80px; }
        .report-container { max-width: 1400px; margin: 0 auto; padding: 20px; }
        .report-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 20px;
            margin-bottom: 20px;
        }
        .report-card h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .stat-box {
            text-align: center;
            padding: 30px;
            border-radius: 10px;
            color: white;
            margin-bottom: 20px;
        }
        .stat-box h3 {
            font-size: 48px;
            margin: 0;
            font-weight: 700;
        }
        .stat-box p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-size: 16px;
        }
        .stat-red { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
        .stat-orange { background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); }
        .heatmap-cell {
            width: 40px;
            height: 40px;
            border-radius: 4px;
            border: 1px solid #eee;
            text-align: center;
            vertical-align: middle;
            font-size: 11px;
            color: #333;
        }
        .heatmap-header {
            text-align: center;
            font-weight: 600;
            font-size: 12px;
        }
        .filter-bar {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
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
                <?php include(__DIR__ . '/layout/header.php'); ?>
                <ul class="nav navbar-nav navbar-right" id="navbar"></ul>
                <?php include(__DIR__ . '/layout/language_switcher.php'); ?>
                <?php include(__DIR__ . '/layout/navbar.php'); ?>
            </div>
        </div>
    </nav>

    <div id="tf-about">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2>KPI Report</h2>
                    <p class="text-muted">Key Performance Indicators Dashboard</p>
                </div>
            </div>

            <div class="report-container">
                <div class="filter-bar">
                    <form method="post" class="form-inline">
                        <div class="form-group mr-3">
                            <label for="start_date" class="mr-2">Start Date:</label>
                            <input type="date" name="start_date" id="start_date" value="<?php echo $startDate; ?>" class="form-control">
                        </div>
                        <div class="form-group mr-3">
                            <label for="end_date" class="mr-2">End Date:</label>
                            <input type="date" name="end_date" id="end_date" value="<?php echo $endDate; ?>" class="form-control">
                        </div>
                        <div class="form-group mr-3">
                            <label for="order_type" class="mr-2">Order Type:</label>
                            <select name="order_type" id="order_type" class="form-control">
                                <?php foreach ($orderTypes as $type): ?>
                                    <option value="<?php echo $type; ?>" <?php echo $orderType === $type ? 'selected' : ''; ?>><?php echo $type; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                    </form>
                    <p class="text-muted mt-2">Total Orders: <strong><?php echo $totalOrders; ?></strong></p>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="report-card">
                            <h3><i class="fas fa-exclamation-circle"></i> Escalated Ratio</h3>
                            <div class="stat-box stat-red">
                                <h3><?php echo $escalatedRatio; ?>%</h3>
                                <p>(<?php echo $escalatedCount; ?> out of <?php echo $totalOrders; ?> orders)</p>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo $escalatedRatio; ?>%" aria-valuenow="<?php echo $escalatedRatio; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="report-card">
                            <h3><i class="fas fa-ban"></i> Cancelled Ratio</h3>
                            <div class="stat-box stat-orange">
                                <h3><?php echo $cancelledRatio; ?>%</h3>
                                <p>(<?php echo $cancelledCount; ?> out of <?php echo $totalOrders; ?> orders)</p>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo $cancelledRatio; ?>%" aria-valuenow="<?php echo $cancelledRatio; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="report-card">
                            <h3><i class="fas fa-chart-bar"></i> Status Distribution</h3>
                            <div class="row">
                                <div class="col-md-8">
                                    <canvas id="statusChart"></canvas>
                                </div>
                                <div class="col-md-4">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr><th>Status</th><th>Count</th><th>Ratio</th></tr>
                                        </thead>
                                        <tbody>
                                            <tr><td>TBC</td><td><?php echo $statusCounts['TBC']; ?></td><td><?php echo $statusRatios['TBC']; ?>%</td></tr>
                                            <tr><td>Confirmed</td><td><?php echo $statusCounts['Confirmed']; ?></td><td><?php echo $statusRatios['Confirmed']; ?>%</td></tr>
                                            <tr><td>Completed</td><td><?php echo $statusCounts['Completed']; ?></td><td><?php echo $statusRatios['Completed']; ?>%</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="report-card">
                            <h3><i class="fas fa-chart-line"></i> Demand Time Series (by OrderCreatedDate)</h3>
                            <div style="height: 400px;">
                                <canvas id="timeseriesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include(__DIR__ . '/layout/footer.php'); ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script type="text/javascript" src="js/main.js"></script>
    <script>
        var ctx = document.getElementById('statusChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo $statusLabels; ?>,
                datasets: [{
                    label: 'Order Count',
                    data: <?php echo $statusDataValues; ?>,
                    backgroundColor: ['#6366f1', '#22c55e', '#f59e0b'],
                    borderColor: ['#4f46e5', '#16a34a', '#d97706'],
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var total = <?php echo $totalOrders; ?>;
                                var value = context.raw;
                                var percent = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return value + ' orders (' + percent + '%)';
                            }
                        }
                    }
                },
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: 'Order Count' } },
                    x: { title: { display: true, text: 'Status' } }
                }
            }
        });

        var timeseriesCtx = document.getElementById('timeseriesChart').getContext('2d');
        new Chart(timeseriesCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($timeseriesLabels); ?>,
                datasets: [{
                    label: 'Order Count',
                    data: <?php echo json_encode($timeseriesData); ?>,
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3,
                    pointRadius: 2,
                    pointHoverRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw + ' orders at ' + context.label;
                            }
                        }
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        title: { display: true, text: 'Order Count' },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: { 
                        title: { display: true, text: 'Date/Time' },
                        grid: { display: false },
                        ticks: { 
                            maxRotation: 45, 
                            minRotation: 45,
                            font: { size: 10 }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>