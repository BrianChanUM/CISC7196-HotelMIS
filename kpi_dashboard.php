<?php
// 使用模拟数据，不依赖数据库连接

// 订单状态统计（模拟数据）
$statusStats = [
    ['Status' => 'Completed', 'Total' => 45],
    ['Status' => 'TBC', 'Total' => 8],
    ['Status' => 'Confirmed', 'Total' => 12],
    ['Status' => 'Cancelled', 'Total' => 20]
];

// 订单类型统计（模拟数据）
$orderTypeStats = [
    ['OrderType' => 'Limo', 'Total' => 18],
    ['OrderType' => 'Dining', 'Total' => 22],
    ['OrderType' => 'F&B', 'Total' => 8],
    ['OrderType' => 'Hotel', 'Total' => 17]
];

// 平均完成时间数据
$fulfillmentData = [
    ['type' => 'Limo', 'avg_time' => 45, 'count' => 16],
    ['type' => 'Dining', 'avg_time' => 35, 'count' => 17],
    ['type' => 'F&B', 'avg_time' => 25, 'count' => 6],
    ['type' => 'Hotel', 'avg_time' => 180, 'count' => 12]
];

// 需求热力图数据 - 按小时统计
$heatmapData = [];
for ($hour = 0; $hour < 24; $hour++) {
    $heatmapData[] = [
        'hour' => $hour,
        'Limo' => rand(5, 35),
        'Dining' => rand(10, 45),
        'F&B' => rand(3, 20),
        'Hotel' => rand(2, 15)
    ];
}

// 司机升级率数据
$driverData = [
    ['name' => 'Chan', 'assigned' => 15, 'completed' => 14, 'escalated' => 1],
    ['name' => 'Leong', 'assigned' => 18, 'completed' => 16, 'escalated' => 2],
    ['name' => 'Tang', 'assigned' => 12, 'completed' => 11, 'escalated' => 1],
    ['name' => 'Daniel', 'assigned' => 20, 'completed' => 17, 'escalated' => 3],
    ['name' => 'Sunny', 'assigned' => 16, 'completed' => 15, 'escalated' => 1],
    ['name' => 'Kobe', 'assigned' => 14, 'completed' => 12, 'escalated' => 2],
    ['name' => 'Roberto', 'assigned' => 10, 'completed' => 8, 'escalated' => 2]
];

// 取消分析数据
$cancellationData = [
    ['reason' => 'Customer changed mind', 'count' => 15, 'percentage' => 35],
    ['reason' => 'Double booking', 'count' => 8, 'percentage' => 19],
    ['reason' => 'Price issue', 'count' => 6, 'percentage' => 14],
    ['reason' => 'Service unavailable', 'count' => 5, 'percentage' => 12],
    ['reason' => 'Other', 'count' => 8, 'percentage' => 20]
];

// 按类型取消统计（模拟数据）
$cancelledByType = [
    ['OrderType' => 'Limo', 'Total' => 6],
    ['OrderType' => 'Dining', 'Total' => 7],
    ['OrderType' => 'F&B', 'Total' => 3],
    ['OrderType' => 'Hotel', 'Total' => 4]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KPI Dashboard - HotelMIS</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            padding-top: 80px;
            background-color: #f8f9fa;
        }
        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 20px;
            margin-bottom: 20px;
        }
        .card-header {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .stat-box {
            text-align: center;
            padding: 20px;
            border-radius: 8px;
            color: white;
        }
        .stat-box h3 {
            font-size: 40px;
            margin: 0;
        }
        .stat-box p {
            margin: 10px 0 0;
            opacity: 0.9;
        }
        .stat-blue { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stat-green { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
        .stat-orange { background: linear-gradient(135deg, #fc4a1a 0%, #f7b733 100%); }
        .stat-red { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        
        .heatmap-cell {
            width: 100%;
            padding: 8px 4px;
            text-align: center;
            border-radius: 4px;
            font-size: 11px;
            color: white;
            font-weight: 500;
        }
        .heatmap-legend {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }
        .heatmap-legend-item {
            display: flex;
            align-items: center;
            margin-right: 15px;
            font-size: 12px;
        }
        .heatmap-legend-color {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            margin-right: 5px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        tr:hover {
            background-color: #f8f9fa;
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
                <a class="navbar-brand" href="index.php">HotelMIS</a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <?php include(__DIR__ . '/layout/header.php');?>
                <ul class="nav navbar-nav navbar-right" id="navbar"></ul>
                <?php include(__DIR__ . '/layout/language_switcher.php');?>
                <?php include(__DIR__ . '/layout/navbar.php');?>
            </div>
        </div>
    </nav>

    <div class="dashboard-container">
        <div class="section-title">
            <h2>KPI Dashboard</h2>
            <p>Real-time performance metrics and analytics</p>
        </div>
        
        <!-- 统计卡片 -->
        <div class="row">
            <div class="col-md-3">
                <div class="stat-box stat-blue">
                    <h3><?php 
                        $total = array_sum(array_column($statusStats, 'Total'));
                        echo $total;
                    ?></h3>
                    <p>Total Orders</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box stat-green">
                    <h3><?php 
                        $completed = array_filter($statusStats, function($s) { return $s['Status'] == 'Completed'; });
                        echo $completed ? reset($completed)['Total'] : 0;
                    ?></h3>
                    <p>Completed Orders</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box stat-orange">
                    <h3><?php 
                        $pending = array_filter($statusStats, function($s) { return $s['Status'] == 'TBC' || $s['Status'] == 'Confirmed'; });
                        echo array_sum(array_column($pending, 'Total'));
                    ?></h3>
                    <p>Pending Orders</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box stat-red">
                    <h3><?php 
                        $cancelled = array_filter($statusStats, function($s) { return $s['Status'] == 'Cancelled'; });
                        echo $cancelled ? reset($cancelled)['Total'] : 0;
                    ?></h3>
                    <p>Cancelled Orders</p>
                </div>
            </div>
        </div>

        <!-- 平均完成时间 -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Average Fulfillment Time (minutes)</div>
                    <canvas id="fulfillmentChart"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Order Distribution by Type</div>
                    <canvas id="orderTypeChart"></canvas>
                </div>
            </div>
        </div>

        <!-- 需求热力图 -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Demand Heatmap (Orders by Hour)</div>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Hour</th>
                                    <?php foreach(['Limo', 'Dining', 'F&B', 'Hotel'] as $type): ?>
                                    <th><?php echo $type; ?></th>
                                    <?php endforeach; ?>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($heatmapData as $row): ?>
                                <tr>
                                    <td><?php echo str_pad($row['hour'], 2, '0', STR_PAD_LEFT) . ':00'; ?></td>
                                    <?php 
                                    $types = ['Limo', 'Dining', 'F&B', 'Hotel'];
                                    $total = 0;
                                    foreach($types as $type): 
                                        $val = $row[$type];
                                        $total += $val;
                                        $color = '';
                                        if ($val <= 10) $color = '#e8f5e9';
                                        elseif ($val <= 20) $color = '#c8e6c9';
                                        elseif ($val <= 30) $color = '#81c784';
                                        else $color = '#4caf50';
                                    ?>
                                    <td>
                                        <div class="heatmap-cell" style="background-color: <?php echo $color; ?>; color: #333;">
                                            <?php echo $val; ?>
                                        </div>
                                    </td>
                                    <?php endforeach; ?>
                                    <td><strong><?php echo $total; ?></strong></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="heatmap-legend">
                        <div class="heatmap-legend-item">
                            <div class="heatmap-legend-color" style="background-color: #e8f5e9;"></div>
                            <span>Low (&lt;10)</span>
                        </div>
                        <div class="heatmap-legend-item">
                            <div class="heatmap-legend-color" style="background-color: #c8e6c9;"></div>
                            <span>Medium (10-20)</span>
                        </div>
                        <div class="heatmap-legend-item">
                            <div class="heatmap-legend-color" style="background-color: #81c784;"></div>
                            <span>High (20-30)</span>
                        </div>
                        <div class="heatmap-legend-item">
                            <div class="heatmap-legend-color" style="background-color: #4caf50;"></div>
                            <span>Very High (&gt;30)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 司机升级率 -->
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Driver Performance & Escalation Rates</div>
                    <canvas id="driverChart"></canvas>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Driver Escalation Rate</div>
                    <table>
                        <thead>
                            <tr>
                                <th>Driver</th>
                                <th>Escalated</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($driverData as $driver): 
                                $rate = round(($driver['escalated'] / $driver['assigned']) * 100, 1);
                            ?>
                            <tr>
                                <td><?php echo $driver['name']; ?></td>
                                <td><?php echo $rate; ?>%</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 取消分析 -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Cancellation Reasons</div>
                    <canvas id="cancellationChart"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Cancellations by Order Type</div>
                    <canvas id="cancellationByTypeChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <?php include(__DIR__ . '/layout/footer.php');?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    
    <script>
    // 平均完成时间图表
    var fulfillmentCtx = document.getElementById('fulfillmentChart').getContext('2d');
    var fulfillmentData = <?php echo json_encode($fulfillmentData); ?>;
    
    new Chart(fulfillmentCtx, {
        type: 'bar',
        data: {
            labels: fulfillmentData.map(d => d.type),
            datasets: [{
                label: 'Avg Time (min)',
                data: fulfillmentData.map(d => d.avg_time),
                backgroundColor: 'rgba(78, 115, 223, 0.8)',
                borderColor: 'rgba(78, 115, 223, 1)',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: { display: true, text: 'Average Fulfillment Time by Order Type' }
            },
            scales: {
                y: { beginAtZero: true, title: { display: true, text: 'Minutes' } },
                x: { title: { display: true, text: 'Order Type' } }
            }
        }
    });

    // 订单类型分布
    var orderTypeCtx = document.getElementById('orderTypeChart').getContext('2d');
    var orderTypeData = <?php echo json_encode($orderTypeStats); ?>;
    
    new Chart(orderTypeCtx, {
        type: 'doughnut',
        data: {
            labels: orderTypeData.map(d => d.OrderType),
            datasets: [{
                data: orderTypeData.map(d => d.Total),
                backgroundColor: ['#667eea', '#f093fb', '#4ade80', '#fbbf24', '#fb7185'],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'right' },
                title: { display: true, text: 'Order Distribution' }
            }
        }
    });

    // 司机表现图表
    var driverCtx = document.getElementById('driverChart').getContext('2d');
    var driverData = <?php echo json_encode($driverData); ?>;
    
    new Chart(driverCtx, {
        type: 'bar',
        data: {
            labels: driverData.map(d => d.name),
            datasets: [
                {
                    label: 'Assigned',
                    data: driverData.map(d => d.assigned),
                    backgroundColor: 'rgba(78, 115, 223, 0.6)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Completed',
                    data: driverData.map(d => d.completed),
                    backgroundColor: 'rgba(34, 197, 94, 0.6)',
                    borderColor: 'rgba(34, 197, 94, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Escalated',
                    data: driverData.map(d => d.escalated),
                    backgroundColor: 'rgba(239, 68, 68, 0.6)',
                    borderColor: 'rgba(239, 68, 68, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                title: { display: true, text: 'Driver Workload & Escalation' }
            },
            scales: {
                y: { beginAtZero: true, title: { display: true, text: 'Orders' } },
                x: { title: { display: true, text: 'Driver Name' } }
            }
        }
    });

    // 取消原因图表
    var cancelCtx = document.getElementById('cancellationChart').getContext('2d');
    var cancelData = <?php echo json_encode($cancellationData); ?>;
    
    new Chart(cancelCtx, {
        type: 'pie',
        data: {
            labels: cancelData.map(d => d.reason),
            datasets: [{
                data: cancelData.map(d => d.count),
                backgroundColor: ['#fb7185', '#fbbf24', '#34d399', '#60a5fa', '#a78bfa'],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'right' },
                title: { display: true, text: 'Cancellation Reasons' }
            }
        }
    });

    // 按类型取消统计
    var cancelByTypeCtx = document.getElementById('cancellationByTypeChart').getContext('2d');
    var cancelByTypeData = <?php echo json_encode($cancelledByType); ?>;
    var allTypes = ['Limo', 'Dining', 'F&B', 'Hotel'];
    var typeCounts = {};
    cancelByTypeData.forEach(d => typeCounts[d.OrderType] = d.Total);
    allTypes.forEach(t => { if (!typeCounts[t]) typeCounts[t] = 0; });
    
    new Chart(cancelByTypeCtx, {
        type: 'bar',
        data: {
            labels: allTypes,
            datasets: [{
                label: 'Cancelled',
                data: allTypes.map(t => typeCounts[t]),
                backgroundColor: 'rgba(239, 68, 68, 0.8)',
                borderColor: 'rgba(239, 68, 68, 1)',
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: { display: true, text: 'Cancellations by Order Type' }
            },
            scales: {
                y: { beginAtZero: true, title: { display: true, text: 'Count' } },
                x: { title: { display: true, text: 'Order Type' } }
            }
        }
    });
    </script>
</body>
</html>